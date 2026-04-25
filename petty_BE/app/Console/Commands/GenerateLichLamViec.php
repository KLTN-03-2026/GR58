<?php

namespace App\Console\Commands;

use App\Models\LichLamViec;
use App\Models\LichNghi;
use App\Models\NhanVien;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateLichLamViec extends Command
{
    protected $signature = 'lich-lam-viec:generate {year} {month} {--force}';
    protected $description = 'Sinh lịch làm việc cho tháng chỉ định (thứ 2–thứ 7, ca luân phiên sáng/chiều theo parity tuần)';

    public function handle(): int
    {
        $year  = (int) $this->argument('year');
        $month = (int) $this->argument('month');
        $force = $this->option('force');

        $existing = LichLamViec::whereYear('ngay_lam', $year)
            ->whereMonth('ngay_lam', $month)
            ->exists();

        if ($existing && ! $force) {
            $this->info("Lịch tháng {$month}/{$year} đã tồn tại. Dùng --force để tạo lại.");
            return 0;
        }

        if ($existing && $force) {
            LichLamViec::whereYear('ngay_lam', $year)
                ->whereMonth('ngay_lam', $month)
                ->delete();
            $this->info("Đã xóa lịch cũ tháng {$month}/{$year}.");
        }

        $bacSis = NhanVien::where('vai_tro', 'bac_si')
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('id')
            ->get();

        $yTas = NhanVien::where('vai_tro', 'y_ta')
            ->where('trang_thai', 'hoat_dong')
            ->orderBy('id')
            ->get();

        if ($bacSis->isEmpty() && $yTas->isEmpty()) {
            $this->error('Không tìm thấy nhân viên hoạt động (bác sĩ/y tá).');
            return 1;
        }

        // Lấy các ngày đã được duyệt nghỉ trong tháng theo nhân viên
        $approvedLeaves = LichNghi::where('trang_thai', LichNghi::DA_DUYET)
            ->whereYear('ngay_nghi', $year)
            ->whereMonth('ngay_nghi', $month)
            ->get()
            ->groupBy('nhan_vien_id')
            ->map(fn ($items) => $items->pluck('ngay_nghi')->map->toDateString()->toArray());

        $start   = Carbon::create($year, $month, 1);
        $end     = $start->copy()->endOfMonth();
        $records = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $d       = $date->day;
            $dateStr = $date->toDateString();

            // 4-slot cyclic rotation áp dụng riêng cho từng nhóm BS và YT
            // slot = (d - 1 + person_index) % 4
            //   slot 0 → ca_toi  (ca đêm)
            //   slot 1 → nghỉ bù (bỏ qua, không insert)
            //   slot 2 → ca_sang
            //   slot 3 → ca_sang
            foreach ([$bacSis, $yTas] as $group) {
                foreach ($group as $idx => $nv) {
                    $slot = ($d - 1 + $idx) % 4;

                    if ($slot === 1) {
                        continue; // nghỉ bù sau ca đêm
                    }

                    $leaves = $approvedLeaves->get($nv->id, []);
                    if (in_array($dateStr, $leaves)) {
                        continue;
                    }

                    $thoiGianTruc = $slot === 0 ? LichLamViec::CA_TOI : LichLamViec::CA_SANG;

                    $records[] = [
                        'ngay_lam'       => $dateStr,
                        'thoi_gian_truc' => $thoiGianTruc,
                        'nhan_vien_id'   => $nv->id,
                        'phong_truc'     => '',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }
            }
        }

        DB::table('lich_lam_viecs')->insert($records);

        $this->info("Đã tạo " . count($records) . " bản ghi lịch làm việc cho tháng {$month}/{$year}.");
        return 0;
    }
}
