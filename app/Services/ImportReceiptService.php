<?php

namespace App\Services;

use App\Repositories\Contracts\ImportReceiptRepositoryInterface;

class ImportReceiptService extends BaseService
{
    protected ImportReceiptRepositoryInterface $receiptRepository;

    public function __construct(ImportReceiptRepositoryInterface $receiptRepository)
    {
        $this->receiptRepository = $receiptRepository;
    }

    public function getImportReceipts(array $filters = [])
    {
        return $this->receiptRepository->all($filters);
    }

    public function getImportReceiptById(int $id)
    {
        return $this->receiptRepository->find($id);
    }

    public function createImportReceiptWithDetails(array $data)
    {
        $this->logInfo('Creating import receipt with details', ['import_date' => $data['import_date'] ?? null]);
        return $this->receiptRepository->createWithDetails($data);
    }

    public function updateImportReceiptWithDetails(int $id, array $data)
    {
        $this->logInfo('Updating import receipt with details', ['receipt_id' => $id]);
        return $this->receiptRepository->updateWithDetails($id, $data);
    }

    public function deleteImportReceipt(int $id)
    {
        $this->logInfo('Deleting import receipt', ['receipt_id' => $id]);
        return $this->receiptRepository->delete($id);
    }
}
