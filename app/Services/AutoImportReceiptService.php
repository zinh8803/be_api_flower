<?php

namespace App\Services;

use App\Repositories\Contracts\AutoImportReceiptInterface;

class AutoImportReceiptService extends BaseService
{
    protected AutoImportReceiptInterface $autoImportReceiptRepository;

    public function __construct(AutoImportReceiptInterface $autoImportReceiptRepository)
    {
        $this->autoImportReceiptRepository = $autoImportReceiptRepository;
    }

    public function getAutoImportReceipts()
    {
        return $this->autoImportReceiptRepository->all();
    }

    public function getAutoImportReceiptById($id)
    {
        return $this->autoImportReceiptRepository->find($id);
    }

    public function createAutoImportReceipt(array $data)
    {
        $data['import_date'] = $data['import_date'] ?? now()->format('Y-m-d');
        $data['run_time'] = $data['run_time'] ?? now()->format('H:i');

        $this->logInfo('Creating auto import receipt config');
        return $this->autoImportReceiptRepository->create($data);
    }

    public function updateAutoImportReceipt($id, array $data)
    {
        $this->logInfo('Updating auto import receipt config', ['id' => $id]);
        return $this->autoImportReceiptRepository->update($id, $data);
    }

    public function deleteAutoImportReceipt($id)
    {
        $this->logInfo('Deleting auto import receipt config', ['id' => $id]);
        return $this->autoImportReceiptRepository->delete($id);
    }
}
