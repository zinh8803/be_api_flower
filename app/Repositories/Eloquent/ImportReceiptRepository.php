<?php

namespace App\Repositories\Eloquent;

use App\Models\FlowerLog;
use App\Models\ImportReceipt;
use App\Repositories\Contracts\ImportReceiptRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ImportReceiptRepository extends BaseRepository implements ImportReceiptRepositoryInterface
{
    public function __construct(ImportReceipt $importReceipt)
    {
        parent::__construct($importReceipt);
    }

    public function all($filters = [], ...$args)
    {
        $query = $this->model->with('details.flower')->orderBy('import_date', 'desc')->orderBy('id', 'desc');

        if (!empty($filters['from_date'])) {
            $query->whereDate('import_date', '>=', $filters['from_date']);
        }
        if (!empty($filters['to_date'])) {
            $query->whereDate('import_date', '<=', $filters['to_date']);
        }

        return $query->paginate(10);
    }

    public function find($id, array $columns = ['*']): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->model->with('details.flower')->find($id, $columns);
    }

    public function createWithDetails(array $data)
    {
        return DB::transaction(function () use ($data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            $total = collect($details)->sum(function ($item) {
                return $item['quantity'] * $item['import_price'];
            });
            $data['total_price'] = $total;

            $receipt = parent::create($data);

            foreach ($details as $detail) {
                $detail['import_date'] = $data['import_date'];
                $detail['subtotal'] = $detail['quantity'] * $detail['import_price'];
                $detail['used_quantity'] = 0;
                $receipt->details()->create($detail);

                FlowerLog::create([
                    'flower_id' => $detail['flower_id'],
                    'type' => 'import',
                    'change_type' => 'plus',
                    'quantity' => $detail['quantity'],
                    'note' => 'Nhập hoa theo phiếu ' . ($receipt->import_code ?? "#{$receipt->id}"),
                    'reference_type' => 'ImportReceipt',
                    'reference_id' => $receipt->id,
                    'user_id' => auth()->id(),
                ]);
            }

            return $this->find($receipt->id);
        });
    }

    public function updateWithDetails($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $details = $data['details'] ?? [];
            unset($data['details']);

            $total = collect($details)->sum(function ($item) {
                return $item['quantity'] * $item['import_price'];
            });
            $data['total_price'] = $total;

            $receipt = $this->update($id, $data);

            $existingDetails = $receipt->details()->get()->keyBy('flower_id');

            $processedFlowerIds = [];

            foreach ($details as $detail) {
                $detail['import_date'] = $data['import_date'];
                $detail['subtotal'] = $detail['quantity'] * $detail['import_price'];

                if ($existingDetails->has($detail['flower_id'])) {
                    $existingDetail = $existingDetails->get($detail['flower_id']);
                    $oldQty = $existingDetail->quantity;
                    $newQty = $detail['quantity'];
                    $diff = $newQty - $oldQty;

                    $existingDetail->update([
                        'quantity' => $detail['quantity'],
                        'import_price' => $detail['import_price'],
                        'import_date' => $detail['import_date'],
                        'subtotal' => $detail['subtotal'],
                    ]);

                    if ($diff !== 0) {
                        FlowerLog::create([
                            'flower_id' => $detail['flower_id'],
                            'type' => 'adjustment',
                            'change_type' => $diff > 0 ? 'plus' : 'minus',
                            'quantity' => abs($diff),
                            'note' => 'Điều chỉnh số lượng nhập phiếu ' . ($receipt->import_code ?? "#{$receipt->id}"),
                            'reference_type' => 'ImportReceipt',
                            'reference_id' => $receipt->id,
                            'user_id' => auth()->id(),
                        ]);
                    }

                    $processedFlowerIds[] = $detail['flower_id'];
                } else {
                    $detail['used_quantity'] = 0;
                    $receipt->details()->create($detail);

                    FlowerLog::create([
                        'flower_id' => $detail['flower_id'],
                        'type' => 'import',
                        'change_type' => 'plus',
                        'quantity' => $detail['quantity'],
                        'note' => 'Bổ sung hoa vào phiếu nhập ' . ($receipt->import_code ?? "#{$receipt->id}"),
                        'reference_type' => 'ImportReceipt',
                        'reference_id' => $receipt->id,
                        'user_id' => auth()->id(),
                    ]);

                    $processedFlowerIds[] = $detail['flower_id'];
                }
            }

            $removedDetails = $receipt->details()->whereNotIn('flower_id', $processedFlowerIds)->get();
            foreach ($removedDetails as $removedDetail) {
                FlowerLog::create([
                    'flower_id' => $removedDetail->flower_id,
                    'type' => 'adjustment',
                    'change_type' => 'minus',
                    'quantity' => $removedDetail->quantity,
                    'note' => 'Xóa hoa khỏi phiếu nhập ' . ($receipt->import_code ?? "#{$receipt->id}"),
                    'reference_type' => 'ImportReceipt',
                    'reference_id' => $receipt->id,
                    'user_id' => auth()->id(),
                ]);
            }

            $receipt->details()->whereNotIn('flower_id', $processedFlowerIds)->delete();

            return $this->find($id);
        });
    }
}
