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

class InventoryReportController extends Controller
{
    public function getInventoryReport(Request $request): mixed
    {
        [$from, $to] = $this->resolveDateRange($request);
        $danhMucId   = $request->get('danh_muc');

        // ─── 1. BIỂU ĐỒ BIẾN ĐỘNG KHO theo tháng ─────────────────────
        $nhapQuery = DB::table('chi_tiet_phieu_nhap_khos as ct')
            ->join('phieu_nhap_khos as pnk', 'ct.phieu_nhap_kho_id', '=', 'pnk.id')
            ->join('hang_hoas as hh', 'ct.hang_hoa_id', '=', 'hh.id')
            ->where('pnk.trang_thai', 'da_duyet')
            ->whereBetween('pnk.ngay_nhap', [$from->toDateString(), $to->toDateString()]);

        if ($danhMucId) $nhapQuery->where('hh.danh_muc_hang_hoa_id', $danhMucId);

        $nhapByMonth = $nhapQuery->selectRaw('
            DATE_FORMAT(pnk.ngay_nhap, "%Y-%m") AS thang,
            SUM(ct.thanh_tien) AS tong_nhap
        ')->groupBy('thang')->orderBy('thang')->get()->keyBy('thang');

        // Hủy/xuất (kiem_ke có chênh lệch âm)
        $huyByMonth = DB::table('kiem_kes')
            ->whereBetween('ngay_kiem_ke', [$from->toDateString(), $to->toDateString()])
            ->where('chenh_lech', '<', 0)
            ->selectRaw('
                DATE_FORMAT(ngay_kiem_ke, "%Y-%m") AS thang,
                ABS(SUM(chenh_lech)) AS so_luong_huy
            ')->groupBy('thang')->orderBy('thang')->get()->keyBy('thang');

        // Tạo labels đầy đủ
        $labels = [];
        $cur    = $from->copy()->startOfMonth();
        while ($cur->lte($to)) {
            $labels[] = $cur->format('Y-m');
            $cur->addMonth();
        }
        if (empty($labels)) $labels = [$from->format('Y-m')];

        $seriesNhap = [];
        $seriesHuy  = [];
        $labelDisp  = [];
        foreach ($labels as $l) {
            $labelDisp[] = Carbon::createFromFormat('Y-m', $l)->format('T/y');
            $seriesNhap[] = round((float)($nhapByMonth[$l]->tong_nhap ?? 0) / 1_000_000, 2);
            $seriesHuy[]  = (int)($huyByMonth[$l]->so_luong_huy ?? 0);
        }

        $movementChart = [
            'labels'  => $labelDisp,
            'series'  => [
                ['name' => 'Nhập kho (M đ)', 'data' => $seriesNhap],
                ['name' => 'Xuất/Hủy (SL)', 'data' => $seriesHuy],
            ],
        ];

        // ─── 2. DONUT: tỷ trọng tồn kho theo danh mục ────────────────
        $tonKhoQuery = DB::table('chi_tiet_phieu_nhap_khos as ct')
            ->join('phieu_nhap_khos as pnk', 'ct.phieu_nhap_kho_id', '=', 'pnk.id')
            ->join('hang_hoas as hh', 'ct.hang_hoa_id', '=', 'hh.id')
            ->leftJoin('danh_muc_hang_hoas as dm', 'hh.danh_muc_hang_hoa_id', '=', 'dm.id')
            ->where('pnk.trang_thai', 'da_duyet');

        if ($danhMucId) $tonKhoQuery->where('hh.danh_muc_hang_hoa_id', $danhMucId);

        $tonKhoByDm = $tonKhoQuery->selectRaw('
            COALESCE(dm.ten_danh_muc_hang_hoa, "Khác") AS ten_dm,
            SUM(ct.thanh_tien) AS tong_gia_tri
        ')->groupBy('ten_dm')->orderByDesc('tong_gia_tri')->get();

        $totalGiaTri = $tonKhoByDm->sum('tong_gia_tri');
        $donutLabels = [];
        $donutSeries = [];
        foreach ($tonKhoByDm as $row) {
            $donutLabels[] = $row->ten_dm;
            $donutSeries[] = $totalGiaTri > 0 ? round($row->tong_gia_tri / $totalGiaTri * 100, 1) : 0;
        }

        $donutChart = [
            'labels' => $donutLabels,
            'series' => $donutSeries,
            'total'  => $this->formatCurrencyShort($totalGiaTri),
        ];

        // ─── 3. TOP HÀNG BÁN CHẠY (nhập nhiều nhất) ──────────────────
        $topQuery = DB::table('chi_tiet_phieu_nhap_khos as ct')
            ->join('phieu_nhap_khos as pnk', 'ct.phieu_nhap_kho_id', '=', 'pnk.id')
            ->join('hang_hoas as hh', 'ct.hang_hoa_id', '=', 'hh.id')
            ->leftJoin('danh_muc_hang_hoas as dm', 'hh.danh_muc_hang_hoa_id', '=', 'dm.id')
            ->where('pnk.trang_thai', 'da_duyet')
            ->whereBetween('pnk.ngay_nhap', [$from->toDateString(), $to->toDateString()]);

        if ($danhMucId) $topQuery->where('hh.danh_muc_hang_hoa_id', $danhMucId);

        $topSelling = $topQuery->selectRaw('
            hh.id, hh.ten_mat_hang AS name,
            COALESCE(dm.ten_danh_muc_hang_hoa, "Khác") AS category,
            SUM(ct.so_luong) AS quantity,
            SUM(ct.thanh_tien) AS tong_von,
            hh.gia_ban
        ')->groupBy('hh.id','hh.ten_mat_hang','category','hh.gia_ban')
        ->orderByDesc('quantity')->limit(10)->get()
        ->map(function ($row, $idx) {
            $revenue = (float)$row->quantity * (float)$row->gia_ban;
            $profit  = $revenue - (float)$row->tong_von;
            $margin  = $revenue > 0 ? round($profit / $revenue * 100, 1) : 0;
            return [
                'id'       => $row->id,
                'rank'     => '#' . ($idx + 1),
                'name'     => $row->name,
                'category' => $row->category,
                'quantity' => number_format($row->quantity),
                'revenue'  => number_format($revenue) . 'đ',
                'profit'   => number_format($profit) . 'đ',
                'margin'   => $margin . '%',
            ];
        });

        // ─── 4. HÀNG CHẬM LUÂN CHUYỂN (Dead Stock > 90 ngày) ─────────
        $deadStockQuery = DB::table('chi_tiet_phieu_nhap_khos as ct')
            ->join('phieu_nhap_khos as pnk', 'ct.phieu_nhap_kho_id', '=', 'pnk.id')
            ->join('hang_hoas as hh', 'ct.hang_hoa_id', '=', 'hh.id')
            ->leftJoin('danh_muc_hang_hoas as dm', 'hh.danh_muc_hang_hoa_id', '=', 'dm.id')
            ->where('pnk.trang_thai', 'da_duyet')
            ->where('pnk.ngay_nhap', '<=', Carbon::now()->subDays(90)->toDateString());

        if ($danhMucId) $deadStockQuery->where('hh.danh_muc_hang_hoa_id', $danhMucId);

        $deadStock = $deadStockQuery->selectRaw('
            hh.id, hh.ten_mat_hang AS name,
            COALESCE(dm.ten_danh_muc_hang_hoa, "Khác") AS category,
            SUM(ct.so_luong) AS quantity,
            SUM(ct.thanh_tien) AS gia_tri_ton,
            DATEDIFF(NOW(), MIN(pnk.ngay_nhap)) AS so_ngay_ton
        ')->groupBy('hh.id','hh.ten_mat_hang','category')
        ->orderByDesc('so_ngay_ton')->limit(10)->get()
        ->map(fn($row) => [
            'id'          => $row->id,
            'name'        => $row->name,
            'category'    => $row->category,
            'quantity'    => number_format($row->quantity),
            'stockValue'  => number_format($row->gia_tri_ton) . 'đ',
            'daysInStock' => (int)$row->so_ngay_ton . ' ngày',
        ]);

        // ─── 5. HÀNG SẮP HẾT HẠN (trong 30 ngày) ────────────────────
        $expiringQuery = DB::table('chi_tiet_phieu_nhap_khos as ct')
            ->join('phieu_nhap_khos as pnk', 'ct.phieu_nhap_kho_id', '=', 'pnk.id')
            ->join('hang_hoas as hh', 'ct.hang_hoa_id', '=', 'hh.id')
            ->where('pnk.trang_thai', 'da_duyet')
            ->whereNotNull('ct.han_su_dung')
            ->whereBetween('ct.han_su_dung', [Carbon::now()->toDateString(), Carbon::now()->addDays(30)->toDateString()]);

        if ($danhMucId) $expiringQuery->where('hh.danh_muc_hang_hoa_id', $danhMucId);

        $expiringSoon = $expiringQuery->selectRaw('
            ct.id, hh.ten_mat_hang AS name,
            ct.so_lo AS lotNumber,
            ct.han_su_dung AS expiryDate,
            ct.so_luong AS quantity,
            DATEDIFF(ct.han_su_dung, NOW()) AS ngay_con_lai
        ')->orderBy('ct.han_su_dung')->limit(20)->get()
        ->map(fn($row) => [
            'id'         => $row->id,
            'name'       => $row->name,
            'lotNumber'  => $row->lotNumber,
            'expiryDate' => Carbon::parse($row->expiryDate)->format('d/m/Y'),
            'quantity'   => number_format($row->quantity),
            'urgent'     => $row->ngay_con_lai <= 7,
            'status'     => $row->ngay_con_lai <= 7
                ? 'URGENT - ' . $row->ngay_con_lai . ' ngày'
                : 'Còn ' . $row->ngay_con_lai . ' ngày',
        ]);

        // ─── 6. DANH MỤC CHO FILTER ───────────────────────────────────
        $danhMucs = DB::table('danh_muc_hang_hoas')
            ->select('id', 'ten_danh_muc_hang_hoa as label')
            ->get()
            ->map(fn($r) => ['value' => $r->id, 'label' => $r->label])
            ->prepend(['value' => 'all', 'label' => 'Tất cả'])
            ->values();

        // ─── 7. EXPORT EXCEL ──────────────────────────────────────────
        if ($request->get('export') === 'excel') {
            return $this->exportExcel($topSelling, $deadStock, $expiringSoon, $from, $to);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'period'         => ['start' => $from->toDateString(), 'end' => $to->toDateString()],
                'movement_chart' => $movementChart,
                'donut_chart'    => $donutChart,
                'top_selling'    => $topSelling,
                'dead_stock'     => $deadStock,
                'expiring_soon'  => $expiringSoon,
                'danh_mucs'      => $danhMucs,
            ],
        ]);
    }

    // ─── Export Excel ─────────────────────────────────────────────────
    private function exportExcel($topSelling, $deadStock, $expiringSoon, $from, $to)
    {
        $col = fn(int $c) => Coordinate::stringFromColumnIndex($c);
        $spreadsheet = new Spreadsheet();

        // ── Sheet 1: Top bán chạy ─────────────────────────────────────
        $s1 = $spreadsheet->getActiveSheet()->setTitle('Top Bán Chạy');
        $period = $from->format('d/m/Y') . ' – ' . $to->format('d/m/Y');

        $this->writeSheetHeader($s1, $col, 'BÁO CÁO KHO & VẬT TƯ — TOP HÀNG BÁN CHẠY', $period, 5,
            [1=>'#', 2=>'Tên hàng', 3=>'Danh mục', 4=>'SL nhập', 5=>'Doanh thu DK']
        );
        foreach ($topSelling as $idx => $r) {
            $row = $idx + 4;
            $sheet = $s1;
            $sheet->setCellValue($col(1).$row, $r['rank']);
            $sheet->setCellValue($col(2).$row, $r['name']);
            $sheet->setCellValue($col(3).$row, $r['category']);
            $sheet->setCellValue($col(4).$row, $r['quantity']);
            $sheet->setCellValue($col(5).$row, $r['revenue']);
            $bg = ($row % 2 === 0) ? 'F0FDFA' : 'FFFFFF';
            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
            ]);
        }

        // ── Sheet 2: Dead Stock ────────────────────────────────────────
        $s2 = $spreadsheet->createSheet()->setTitle('Hàng Chậm Luân Chuyển');
        $this->writeSheetHeader($s2, $col, 'HÀNG CHẬM LUÂN CHUYỂN (>90 NGÀY)', $period, 4,
            [1=>'Tên hàng', 2=>'Danh mục', 3=>'SL tồn', 4=>'Ngày tồn']
        );
        foreach ($deadStock as $idx => $r) {
            $row = $idx + 4;
            $s2->setCellValue($col(1).$row, $r['name']);
            $s2->setCellValue($col(2).$row, $r['category']);
            $s2->setCellValue($col(3).$row, $r['quantity']);
            $s2->setCellValue($col(4).$row, $r['daysInStock']);
            $bg = ($row % 2 === 0) ? 'FFF7ED' : 'FFFFFF';
            $s2->getStyle("A{$row}:D{$row}")->applyFromArray([
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
            ]);
        }

        // ── Sheet 3: Sắp hết hạn ──────────────────────────────────────
        $s3 = $spreadsheet->createSheet()->setTitle('Sắp Hết Hạn');
        $this->writeSheetHeader($s3, $col, 'HÀNG SẮP HẾT HẠN (30 NGÀY TỚI)', $period, 5,
            [1=>'Tên hàng', 2=>'Số lô', 3=>'Ngày HH', 4=>'SL còn', 5=>'Trạng thái']
        );
        foreach ($expiringSoon as $idx => $r) {
            $row = $idx + 4;
            $s3->setCellValue($col(1).$row, $r['name']);
            $s3->setCellValue($col(2).$row, $r['lotNumber']);
            $s3->setCellValue($col(3).$row, $r['expiryDate']);
            $s3->setCellValue($col(4).$row, $r['quantity']);
            $s3->setCellValue($col(5).$row, $r['status']);
            $bg = $r['urgent'] ? 'FEE2E2' : 'FFF7ED';
            $s3->getStyle("A{$row}:E{$row}")->applyFromArray([
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
            ]);
        }

        // ── Activate sheet 1 ─────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'bao-cao-kho-' . $from->format('Y-m') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function writeSheetHeader($sheet, $col, string $title, string $period, int $numCols, array $headers): void
    {
        $lastCol = $col($numCols);
        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->setCellValue('A1', $title);
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold'=>true,'size'=>13,'color'=>['rgb'=>'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0D9488']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(28);

        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->setCellValue('A2', 'Kỳ: ' . $period);
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['italic'=>true,'size'=>10],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'ECFDF5']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(16);

        foreach ($headers as $c => $h) $sheet->setCellValue($col($c).'3', $h);
        $sheet->getStyle("A3:{$lastCol}3")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F766E']],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        foreach (range(1, $numCols) as $c) $sheet->getColumnDimension($col($c))->setWidth(20);
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(28);
    }

    private function formatCurrencyShort(float $val): string
    {
        if ($val >= 1_000_000_000) return round($val / 1_000_000_000, 1) . ' tỷ';
        if ($val >= 1_000_000)     return round($val / 1_000_000, 1) . 'M';
        return number_format($val) . 'đ';
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'this_year');
        return match ($period) {
            'this_month' => [Carbon::now()->startOfMonth(),                                 Carbon::now()->endOfMonth()],
            '3months'    => [Carbon::now()->subMonths(2)->startOfMonth(),                   Carbon::now()->endOfMonth()],
            '6months'    => [Carbon::now()->subMonths(5)->startOfMonth(),                   Carbon::now()->endOfMonth()],
            'this_year'  => [Carbon::now()->startOfYear(),                                  Carbon::now()->endOfYear()],
            default      => [Carbon::now()->startOfYear(),                                  Carbon::now()->endOfYear()],
        };
    }
}