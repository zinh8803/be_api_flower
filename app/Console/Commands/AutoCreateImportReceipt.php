<?php

namespace App\Console\Commands;

use App\Events\AutoImport;
use App\Models\AutoImportReceipt;
use App\Models\User;
use App\Notifications\AutoImportNotification;
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
        Log::info("Ngày: $today, Giờ: $nowTime");

        $config = AutoImportReceipt::where('enabled', 1)
            ->where('import_date', $today)
            ->whereRaw("DATE_FORMAT(run_time, '%H:%i') = ?", [$nowTime])
            ->orderByDesc('id')
            ->first();
        if ($config) {
            Log::info("Kết quả truy vấn:", $config->toArray());

            Log::info("Đã tìm thấy cấu hình id: " . $config->id);
            $data = [
                'import_date' => $today,
                'details' => $config->details,
            ];
            $repo->createWithDetails($data);
            $this->info("Đã tạo phiếu nhập tự động cho ngày $today lúc $nowTime");


            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new AutoImportNotification($config));
            }

            $employees = User::where('role', 'employee')->get();
            foreach ($employees as $employee) {
                $employee->notify(new AutoImportNotification($config));
            }
            event(new AutoImport($config));
        } else {
            Log::info("Không tìm thấy cấu hình phù hợp để tạo phiếu nhập.");
        }
    }
    public static function schedule(Schedule $schedule)
    {
        $schedule->command(static::class)->everyMinute();
    }
}
