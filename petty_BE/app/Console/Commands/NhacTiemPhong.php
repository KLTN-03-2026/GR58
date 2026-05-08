<?php

namespace App\Console\Commands;

use App\Models\LichHen;
use App\Models\ThongBao;
use App\Services\ThongBaoService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class NhacTiemPhong extends Command
{
    protected $signature = 'thongbao:nhac-tiem-phong';
    protected $description = 'Gửi thông báo nhắc tiêm phòng cho thú cưng sắp hết hạn (>= 11 tháng kể từ lần tiêm cuối)';

    public function handle(): int
    {
        $service = app(ThongBaoService::class);
        $elevenMonthsAgo = Carbon::now()->subMonths(11);
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Tìm các lịch hẹn tiêm phòng (dịch vụ có tên chứa "tiêm" hoặc "vaccine") đã hoàn thành
        $lichHens = LichHen::whereHas('dichVu', function ($q) {
                $q->where('ten', 'like', '%tiêm%')
                  ->orWhere('ten', 'like', '%vaccine%')
                  ->orWhere('ten', 'like', '%Tiêm%')
                  ->orWhere('ten', 'like', '%Vaccine%');
            })
            ->where('trang_thai', 'completed')
            ->where('ngay_gio', '<=', $elevenMonthsAgo)
            ->whereNotNull('khach_hang_id')
            ->whereNotNull('thu_cung_id')
            ->with(['thuCung', 'khachHang'])
            ->get();

        $sent = 0;

        foreach ($lichHens as $lichHen) {
            $thuCung = $lichHen->thuCung;
            $khachHangId = $lichHen->khach_hang_id;

            if (!$thuCung || !$khachHangId) continue;

            // Kiểm tra đã có thông báo tiêm phòng cho thú cưng này trong 30 ngày gần đây
            $existing = ThongBao::where('khach_hang_id', $khachHangId)
                ->where('loai', 'tiem_phong')
                ->where('reference_type', 'ThuCung')
                ->where('reference_id', $thuCung->id)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->exists();

            if ($existing) continue;

            // Kiểm tra thú cưng không có lịch tiêm mới hơn
            $hasNewerVaccine = LichHen::where('thu_cung_id', $thuCung->id)
                ->whereHas('dichVu', function ($q) {
                    $q->where('ten', 'like', '%tiêm%')
                      ->orWhere('ten', 'like', '%vaccine%')
                      ->orWhere('ten', 'like', '%Tiêm%')
                      ->orWhere('ten', 'like', '%Vaccine%');
                })
                ->where('ngay_gio', '>', $elevenMonthsAgo)
                ->exists();

            if ($hasNewerVaccine) continue;

            $tenThuCung = $thuCung->ten ?? 'thú cưng của bạn';
            $service->create(
                $khachHangId,
                'tiem_phong',
                "Đã đến lúc tiêm phòng lại cho {$tenThuCung}",
                "Lần tiêm phòng gần nhất của {$tenThuCung} đã hơn 11 tháng. Hãy đặt lịch tiêm phòng để bảo vệ sức khỏe cho bé nhé!",
                'ThuCung',
                $thuCung->id
            );
            $sent++;
        }

        $this->info("Đã gửi {$sent} thông báo nhắc tiêm phòng.");
        return Command::SUCCESS;
    }
}
