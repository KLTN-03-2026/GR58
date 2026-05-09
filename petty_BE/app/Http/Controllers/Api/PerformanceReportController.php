<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class PerformanceReportController extends Controller
{
    // ── Cấu hình lương ────────────────────────────────────────────────
    // Lương cứng/tháng
    const LUONG_BS = 15_000_000;
    const LUONG_YT =  8_000_000;

    // Hoa hồng % doanh thu
    const HOA_HONG_BS = 0.03;   // 3%
    const HOA_HONG_YT = 0.015;  // 1.5%

    // Thưởng doanh thu
    const THUONG_100M = 5_000_000;   // DT >= 100M
    const THUONG_50M  = 2_000_000;   // DT >= 50M
    const THUONG_20M  =   500_000;   // DT >= 20M

    // ── Helper tính lương ─────────────────────────────────────────────
    private function tinhLuong(array $s, int $soNgayKy, int $soNgayThang): array
    {
        $vai_tro   = $s['vai_tro'];
        $doanh_thu = $s['doanh_thu'];

        // 1. Lương cứng — tính theo tỉ lệ ngày trong kỳ / tháng
        $luong_thang   = $vai_tro === 'bac_si' ? self::LUONG_BS : self::LUONG_YT;
        $luong_co_dinh = round($luong_thang * $soNgayKy / $soNgayThang);

        // 2. Hoa hồng
        $ty_le_hh = $vai_tro === 'bac_si' ? self::HOA_HONG_BS : self::HOA_HONG_YT;
        $hoa_hong = round($doanh_thu * $ty_le_hh);

        // 3. Thưởng doanh thu
        $thuong = match(true) {
            $doanh_thu >= 100_000_000 => self::THUONG_100M,
            $doanh_thu >=  50_000_000 => self::THUONG_50M,
            $doanh_thu >=  20_000_000 => self::THUONG_20M,
            default                   => 0,
        };

        $tong_luong = $luong_co_dinh + $hoa_hong + $thuong;

        return [
            'luong_co_dinh' => $luong_co_dinh,
            'hoa_hong'      => $hoa_hong,
            'thuong_dt'     => $thuong,
            'tong_luong'    => $tong_luong,
            'ghi_chu_luong' => match(true) {
                $doanh_thu >= 100_000_000 => '🏆 Xuất sắc (+5M)',
                $doanh_thu >=  50_000_000 => '🥇 Tốt (+2M)',
                $doanh_thu >=  20_000_000 => '🥈 Khá (+500K)',
                default                   => '—',
            },
        ];
    }

    public function getPerformanceReport(Request $request): mixed
    {
        [$from, $to] = $this->resolveDateRange($request);
        $vaiTro      = $request->get('vai_tro', 'all');

        // Số ngày trong kỳ và số ngày tháng (để tính lương theo tỉ lệ)
        $soNgayKy     = (int) $from->diffInDays($to) + 1;
        $soNgayThang  = (int) $from->daysInMonth;

        // ─── 1. DOANH THU ─────────────────────────────────────────────
        $revenueQuery = DB::table('thanh_toans as tt')
            ->join('lich_hens as lh', 'tt.lich_hen_id', '=', 'lh.id')
            ->join('nhan_viens as nv', 'lh.nhan_vien_id', '=', 'nv.id')
            ->where('tt.trang_thai', 'da_thanh_toan')
            ->whereBetween('tt.ngay_thanh_toan', [$from, $to])
            ->whereNotNull('lh.nhan_vien_id');

        if ($vaiTro !== 'all') $revenueQuery->where('nv.vai_tro', $vaiTro);

        $revenueByStaff = $revenueQuery->selectRaw('
            nv.id, nv.full_name, nv.vai_tro, nv.chuc_danh, nv.anh_dai_dien,
            COUNT(DISTINCT tt.id) AS so_don,
            COALESCE(SUM(tt.tong_tien_sau_giam), 0) AS doanh_thu
        ')->groupBy('nv.id','nv.full_name','nv.vai_tro','nv.chuc_danh','nv.anh_dai_dien')
        ->orderByDesc('doanh_thu')->get()->keyBy('id');

        // ─── 2. SỐ CA ─────────────────────────────────────────────────
        $shiftQuery = DB::table('lich_lam_viecs')
            ->whereBetween('ngay_lam', [$from->toDateString(), $to->toDateString()]);
        if ($vaiTro !== 'all') {
            $shiftQuery->join('nhan_viens as nv2','lich_lam_viecs.nhan_vien_id','=','nv2.id')
                       ->where('nv2.vai_tro', $vaiTro);
        }
        $shiftByStaff = $shiftQuery->selectRaw('
            lich_lam_viecs.nhan_vien_id AS nhan_vien_id, COUNT(*) AS so_ca
        ')->groupBy('lich_lam_viecs.nhan_vien_id')->get()->keyBy('nhan_vien_id');

        // ─── 3. LỊCH HẸN ─────────────────────────────────────────────
        $apptQuery = DB::table('lich_hens as lh')
            ->join('nhan_viens as nv','lh.nhan_vien_id','=','nv.id')
            ->whereBetween('lh.ngay_gio', [$from, $to])
            ->whereNotNull('lh.nhan_vien_id');
        if ($vaiTro !== 'all') $apptQuery->where('nv.vai_tro', $vaiTro);
        $apptByStaff = $apptQuery->selectRaw('
            lh.nhan_vien_id, COUNT(*) AS tong_lich,
            SUM(CASE WHEN lh.trang_thai = "hoan_thanh" THEN 1 ELSE 0 END) AS hoan_thanh
        ')->groupBy('lh.nhan_vien_id')->get()->keyBy('nhan_vien_id');

        // ─── 4. BUILD DANH SÁCH ───────────────────────────────────────
        $allIds = $revenueByStaff->keys()
            ->merge($shiftByStaff->keys())
            ->merge($apptByStaff->keys())
            ->unique();

        if ($allIds->isEmpty()) {
            $nvQ = DB::table('nhan_viens')->where('trang_thai','hoat_dong');
            if ($vaiTro !== 'all') $nvQ->where('vai_tro',$vaiTro);
            $allIds = $nvQ->pluck('id');
        }

        $staffInfo = DB::table('nhan_viens')->whereIn('id',$allIds)
            ->select('id','full_name','vai_tro','chuc_danh','anh_dai_dien')
            ->get()->keyBy('id');

        $staffList = $allIds->map(function ($id) use ($revenueByStaff,$shiftByStaff,$apptByStaff,$staffInfo,$soNgayKy,$soNgayThang) {
            $info   = $staffInfo[$id]      ?? null;
            $rev    = $revenueByStaff[$id] ?? null;
            $shift  = $shiftByStaff[$id]   ?? null;
            $appt   = $apptByStaff[$id]    ?? null;

            $doanh_thu  = (float) ($rev->doanh_thu   ?? 0);
            $so_ca      = (int)   ($shift->so_ca     ?? 0);
            $tong_lich  = (int)   ($appt->tong_lich  ?? 0);
            $hoan_thanh = (int)   ($appt->hoan_thanh ?? 0);
            $vai_tro    = $info->vai_tro ?? ($rev->vai_tro ?? 'y_ta');
            $ty_le_ht   = $tong_lich > 0 ? round($hoan_thanh / $tong_lich * 100, 1) : 0;

            $base = [
                'id'            => $id,
                'full_name'     => $info->full_name    ?? ($rev->full_name   ?? 'N/A'),
                'vai_tro'       => $vai_tro,
                'vai_tro_label' => $vai_tro === 'bac_si' ? 'Bác sĩ' : 'Y tá',
                'chuc_danh'     => $info->chuc_danh    ?? ($rev->chuc_danh   ?? ''),
                'anh_dai_dien'  => $info->anh_dai_dien ?? ($rev->anh_dai_dien ?? null),
                'doanh_thu'     => $doanh_thu,
                'so_don'        => (int) ($rev->so_don ?? 0),
                'so_ca'         => $so_ca,
                'tong_lich'     => $tong_lich,
                'hoan_thanh'    => $hoan_thanh,
                'ty_le_ht'      => $ty_le_ht,
                'luong_thang'   => $vai_tro === 'bac_si' ? self::LUONG_BS : self::LUONG_YT,
            ];

            return array_merge($base, $this->tinhLuong($base, $soNgayKy, $soNgayThang));
        })->sortByDesc('doanh_thu')->values();

        // ─── 5. XUẤT EXCEL ────────────────────────────────────────────
        if ($request->get('export') === 'excel') {
            return $this->exportExcel($staffList, $from, $to, $soNgayKy, $soNgayThang);
        }

        // ─── 6. JSON ──────────────────────────────────────────────────
        $totalLich = $staffList->sum('tong_lich');
        $totalHT   = $staffList->sum('hoan_thanh');

        return response()->json([
            'status' => true,
            'data'   => [
                'period'  => ['start' => $from->toDateString(), 'end' => $to->toDateString()],
                'so_ngay_ky' => $soNgayKy,
                'summary' => [
                    'total_revenue'  => $staffList->sum('doanh_thu'),
                    'total_ca'       => $staffList->sum('so_ca'),
                    'total_lich'     => $totalLich,
                    'ty_le_ht'       => $totalLich > 0 ? round($totalHT / $totalLich * 100, 1) : 0,
                    'total_staff'    => $staffList->count(),
                    'tong_luong'     => $staffList->sum('tong_luong'),
                ],
                'top3'      => $staffList->take(3)->values(),
                'staff'     => $staffList,
                'bar_chart' => $staffList->take(10)->map(fn($s) => [
                    'name'      => $s['full_name'],
                    'doanh_thu' => round($s['doanh_thu'] / 1_000_000, 2),
                    'so_don'    => $s['so_don'],
                ])->values(),
                'roles' => [
                    ['value'=>'all',    'label'=>'Tất cả'],
                    ['value'=>'bac_si', 'label'=>'Bác sĩ'],
                    ['value'=>'y_ta',   'label'=>'Y tá'],
                ],
                // Thông tin công thức để hiển thị UI
                'cong_thuc' => [
                    'luong_bs'    => self::LUONG_BS,
                    'luong_yt'    => self::LUONG_YT,
                    'hh_bs'       => self::HOA_HONG_BS * 100,
                    'hh_yt'       => self::HOA_HONG_YT * 100,
                    'thuong_100m' => self::THUONG_100M,
                    'thuong_50m'  => self::THUONG_50M,
                    'thuong_20m'  => self::THUONG_20M,
                ],
            ],
        ]);
    }

    // ─── Export Excel ─────────────────────────────────────────────────
    private function exportExcel($staffList, $from, $to, $soNgayKy, $soNgayThang)
    {
        $col = fn(int $c) => Coordinate::stringFromColumnIndex($c);

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bảng tính lương');

        $period  = $from->format('d/m/Y') . ' – ' . $to->format('d/m/Y');
        $lastRow = $staffList->count() + 4;

        // ── Dòng 1: Tiêu đề ──────────────────────────────────────────
        $sheet->mergeCells('A1:L1');
        $sheet->setCellValue('A1', 'BẢNG TÍNH LƯƠNG — ' . strtoupper($period));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold'=>true,'size'=>15,'color'=>['rgb'=>'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0D9488']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Dòng 2: Công thức ─────────────────────────────────────────
        $sheet->mergeCells('A2:L2');
        $sheet->setCellValue('A2',
            "Công thức: Lương = Lương cứng (BS: " . number_format(self::LUONG_BS) .
            "đ / YT: " . number_format(self::LUONG_YT) .
            "đ) × ({$soNgayKy}/{$soNgayThang} ngày)  +  Hoa hồng (BS:3%/YT:1.5% DT)  +  Thưởng DT (≥100M:+5M / ≥50M:+2M / ≥20M:+500K)"
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic'=>true,'size'=>9,'color'=>['rgb'=>'064E3B']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'ECFDF5']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->mergeCells('A3:L3');
        $sheet->getRowDimension(3)->setRowHeight(6);

        // ── Dòng 4: Header ────────────────────────────────────────────
        $headers = [
            1=>'STT', 2=>'Họ và tên', 3=>'Vai trò', 4=>'Chức danh',
            5=>'Doanh thu', 6=>'Số đơn', 7=>'Số ca', 8=>'Tỉ lệ HT(%)',
            9=>'Lương cứng', 10=>'Hoa hồng', 11=>'Thưởng DT', 12=>'TỔNG LƯƠNG',
        ];
        foreach ($headers as $c => $h) $sheet->setCellValue($col($c).'4', $h);
        $sheet->getStyle('A4:L4')->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F766E']],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // ── Data ──────────────────────────────────────────────────────
        foreach ($staffList as $idx => $s) {
            $row = $idx + 5;
            $data = [
                1=>$idx+1, 2=>$s['full_name'], 3=>$s['vai_tro_label'],
                4=>$s['chuc_danh']?:'—', 5=>$s['doanh_thu'],
                6=>$s['so_don'], 7=>$s['so_ca'], 8=>$s['ty_le_ht'].'%',
                9=>$s['luong_co_dinh'], 10=>$s['hoa_hong'],
                11=>$s['thuong_dt'], 12=>$s['tong_luong'],
            ];
            foreach ($data as $c => $v) $sheet->setCellValue($col($c).$row, $v);

            // Màu nền xen kẽ + highlight bác sĩ/y tá
            $bg = $s['vai_tro'] === 'bac_si'
                ? ($row % 2 === 0 ? 'EFF6FF' : 'DBEAFE')   // xanh nhạt = bác sĩ
                : ($row % 2 === 0 ? 'F5F3FF' : 'EDE9FE');   // tím nhạt = y tá
            $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
            ]);
            // Bold + màu xanh đậm cột tổng lương
            $sheet->getStyle($col(12).$row)->applyFromArray([
                'font' => ['bold'=>true,'color'=>['rgb'=>'065F46']],
            ]);
            $sheet->getRowDimension($row)->setRowHeight(20);
        }

        // ── Format số ────────────────────────────────────────────────
        foreach ([5,9,10,11,12] as $c) {
            $sheet->getStyle($col($c).'5:'.$col($c).$lastRow)
                ->getNumberFormat()->setFormatCode('#,##0');
        }

        // ── Căn chỉnh ────────────────────────────────────────────────
        $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C5:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F5:L{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ── Border ───────────────────────────────────────────────────
        $sheet->getStyle("A4:L{$lastRow}")->applyFromArray([
            'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'D1D5DB']]],
        ]);

        // ── Dòng tổng ────────────────────────────────────────────────
        $sumRow = $lastRow + 1;
        $sheet->mergeCells("A{$sumRow}:D{$sumRow}");
        $sheet->setCellValue("A{$sumRow}", 'TỔNG CỘNG (' . $staffList->count() . ' nhân viên)');
        $sums = [5=>$staffList->sum('doanh_thu'), 6=>$staffList->sum('so_don'),
                 7=>$staffList->sum('so_ca'),
                 9=>$staffList->sum('luong_co_dinh'), 10=>$staffList->sum('hoa_hong'),
                 11=>$staffList->sum('thuong_dt'), 12=>$staffList->sum('tong_luong')];
        foreach ($sums as $c => $v) {
            $sheet->setCellValue($col($c).$sumRow, $v);
            if (in_array($c,[5,9,10,11,12]))
                $sheet->getStyle($col($c).$sumRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        $sheet->getStyle("A{$sumRow}:L{$sumRow}")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'134E4A']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle($col(12).$sumRow)->applyFromArray([
            'font' => ['bold'=>true,'size'=>12,'color'=>['rgb'=>'6EE7B7']],
        ]);
        $sheet->getRowDimension($sumRow)->setRowHeight(24);

        // ── Độ rộng cột ──────────────────────────────────────────────
        $widths=['A'=>5,'B'=>24,'C'=>9,'D'=>18,'E'=>16,'F'=>8,'G'=>7,'H'=>12,
                 'I'=>14,'J'=>14,'K'=>12,'L'=>16];
        foreach ($widths as $c=>$w) $sheet->getColumnDimension($c)->setWidth($w);

        // ── Stream ───────────────────────────────────────────────────
        $filename = 'bang-tinh-luong-' . $from->format('Y-m') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'this_month');
        return match ($period) {
            'today'      => [Carbon::today()->startOfDay(),            Carbon::today()->endOfDay()],
            '7days'      => [Carbon::now()->subDays(6)->startOfDay(),  Carbon::now()->endOfDay()],
            '30days'     => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()],
            'this_month' => [Carbon::now()->startOfMonth(),            Carbon::now()->endOfMonth()],
            'this_year'  => [Carbon::now()->startOfYear(),             Carbon::now()->endOfYear()],
            'custom'     => [
                Carbon::parse($request->get('start', Carbon::now()->startOfMonth()))->startOfDay(),
                Carbon::parse($request->get('end',   Carbon::now()))->endOfDay(),
            ],
            default => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };
    }
}