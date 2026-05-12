<?php

namespace App\Console\Commands;

use App\Models\LichHen;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelOverdueAppointments extends Command
{
    protected $signature   = 'lich-hen:cancel-overdue
                                {--days=7 : Số ngày quá hạn trước khi hủy (mặc định 7)}
                                {--dry-run : Chạy thử, không thực sự hủy}';

    protected $description = 'Tự động hủy các lịch hẹn quá hạn chưa được xử lý';

    public function handle(): int
    {
        $days   = (int) $this->option('days');
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = Carbon::now()->subDays($days);

        // Chỉ hủy các lịch hẹn ở trạng thái chưa hoàn thành
        $cancelableStatuses = [
            'pending',       // Chờ xác nhận
            'confirmed',     // Đã xác nhận nhưng chưa đến
            'cho_xac_nhan',
            'da_xac_nhan',
            'in-progress',   // Đang khám nhưng quá lâu không xử lý
        ];

        $query = LichHen::whereIn('trang_thai', $cancelableStatuses)
            ->where('ngay_gio', '<', $cutoff);

        $count = $query->count();

        if ($count === 0) {
            $this->info('✅ Không có lịch hẹn quá hạn cần hủy.');
            return self::SUCCESS;
        }

        $this->info("📋 Tìm thấy {$count} lịch hẹn quá hạn (trước {$cutoff->format('d/m/Y H:i')})");

        if ($dryRun) {
            $this->warn('🔍 Chế độ dry-run: không thực sự hủy.');
            $query->with(['khachHang', 'dichVu'])
                ->get()
                ->each(fn($lh) => $this->line(
                    "  - ID:{$lh->id} | {$lh->ngay_gio} | " .
                    ($lh->khachHang?->full_name ?? 'N/A') . ' | ' .
                    ($lh->dichVu?->ten ?? 'N/A') . ' | ' .
                    $lh->trang_thai
                ));
            return self::SUCCESS;
        }

        // Cập nhật hàng loạt
        $updated = $query->update([
            'trang_thai' => 'cancelled',
            'ghi_chu'    => \DB::raw(
                "CONCAT(COALESCE(ghi_chu, ''), ' [Tự động hủy: quá hạn {$days} ngày - " .
                now()->format('d/m/Y') . "]')"
            ),
            'updated_at' => now(),
        ]);

        Log::info("CancelOverdueAppointments: Đã hủy {$updated} lịch hẹn quá {$days} ngày.", [
            'cutoff'  => $cutoff->toDateTimeString(),
            'updated' => $updated,
        ]);

        $this->info("✅ Đã hủy {$updated} lịch hẹn quá hạn.");
        return self::SUCCESS;
    }
}