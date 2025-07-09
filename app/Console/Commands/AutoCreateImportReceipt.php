<?php

namespace App\Console\Commands;

use App\Models\AutoImportReceipt;
use App\Repositories\Eloquent\ImportReceiptRepository;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class AutoCreateImportReceipt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-receipt:auto-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động tạo phiếu nhập theo cấu hình';

    /**
     * Execute the console command.
     */
    public function handle(ImportReceiptRepository $repo)
    {
        Log::info("AutoCreateImportReceipt đã được gọi lúc " . now());
        $today = now()->toDateString();
        $nowTime = now()->format('H:i');
        $configs = AutoImportReceipt::where('enabled', true)
            ->where('import_date', $today)
            ->where('run_time', $nowTime)
            ->get();
        foreach ($configs as $config) {
            $data = [
                'import_date' => $today,
                'details' => $config->details,
            ];
            $repo->createWithDetails($data);
            $this->info("Đã tạo phiếu nhập tự động cho ngày $today lúc $nowTime");
        }
    }
    public static function schedule(Schedule $schedule)
    {
        $schedule->command(static::class)->everyMinute();
    }
}
