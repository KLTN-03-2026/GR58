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

class RevenueReportController extends Controller
{
    public function getRevenueReport(Request $request): mixed
    {
        [$from, $to] = $this->resolveDateRange($request);
        $serviceId   = $request->get('service', 'all');

        // ─── 1. TỔNG HỢP ─────────────────────────────────────────────
        // Nguồn: thanh_toans.trang_thai = 'da_thanh_toan' (giống StatisticController)
        $baseQ = DB::table('thanh_toans as tt')
            ->where('tt.trang_thai', 'da_thanh_toan')
            ->whereBetween('tt.ngay_thanh_toan', [$from, $to]);

        if ($serviceId !== 'all') {
            $baseQ->join('lich_hens as lh', 'tt.lich_hen_id', '=', 'lh.id')
                  ->where('lh.dich_vu_id', $serviceId);
        }

        $summary = (clone $baseQ)->selectRaw('
            COUNT(tt.id)                             AS total_orders,
            COALESCE(SUM(tt.tong_tien_sau_giam), 0)  AS total_revenue,
            COALESCE(SUM(tt.so_tien_giam), 0)        AS total_discount
        ')->first();

        // Chi phí từ phiếu chi (giống StatisticController)
        $totalCogs = (float) DB::table('phieu_chis')
            ->whereBetween('ngay_chi', [$from->toDateString(), $to->toDateString()])
            ->sum('tong_so_tien');

        $totalRevenue = (float) ($summary->total_revenue ?? 0);
        $totalOrders  = (int)   ($summary->total_orders  ?? 0);
        $totalProfit  = max(0, $totalRevenue - $totalCogs);
        $aov          = $totalOrders > 0 ? round($totalRevenue / $totalOrders) : 0;

        // ─── 2. CHART DATA THEO NGÀY ─────────────────────────────────
        $dailyRows = (clone $baseQ)->selectRaw('
            DATE(tt.ngay_thanh_toan) AS ngay,
            COUNT(tt.id)             AS so_don,
            SUM(tt.tong_tien_sau_giam) AS doanh_thu
        ')->groupBy('ngay')->orderBy('ngay')->get()->keyBy('ngay');

        $chartCategories = [];
        $chartRevenue    = [];
        $chartProfit     = [];
        $cur = $from->copy()->startOfDay();

        while ($cur->lte($to)) {
            $d                 = $cur->toDateString();
            $chartCategories[] = $cur->format('d/m');
            $rev               = (float) ($dailyRows[$d]->doanh_thu ?? 0);
            $profitRatio       = $totalRevenue > 0 ? $totalProfit / $totalRevenue : 0;
            $chartRevenue[]    = round($rev / 1_000_000, 2);
            $chartProfit[]     = round($rev * $profitRatio / 1_000_000, 2);
            $cur->addDay();
        }

        // ─── 3. DONUT THEO PT THANH TOÁN ─────────────────────────────
        $paymentRows = (clone $baseQ)->selectRaw('
            tt.hinh_thuc_thanh_toan AS phuong_thuc,
            SUM(tt.tong_tien_sau_giam) AS tong_tien
        ')->groupBy('tt.hinh_thuc_thanh_toan')->get();

        // ─── 4. DONUT THEO DỊCH VỤ ───────────────────────────────────
        $svcQ = DB::table('thanh_toans as tt')
            ->join('lich_hens as lh', 'tt.lich_hen_id', '=', 'lh.id')
            ->leftJoin('dich_vus as dv', 'lh.dich_vu_id', '=', 'dv.id')
            ->leftJoin('danh_muc_dich_vus as dm', 'dv.danh_muc_id', '=', 'dm.id')
            ->where('tt.trang_thai', 'da_thanh_toan')
            ->whereBetween('tt.ngay_thanh_toan', [$from, $to]);

        if ($serviceId !== 'all') {
            $svcQ->where('lh.dich_vu_id', $serviceId);
        }

        $serviceRows = $svcQ->selectRaw('
            COALESCE(dm.ten_nhom, dv.ten, "Khác") AS ten_khoa,
            SUM(tt.tong_tien_sau_giam) AS tong_tien
        ')->groupBy('ten_khoa')->orderByDesc('tong_tien')->get();

        // ─── 5. BẢNG CHI TIẾT ────────────────────────────────────────
        $tableRows = (clone $baseQ)->selectRaw('
            DATE(tt.ngay_thanh_toan)   AS ngay,
            COUNT(tt.id)               AS so_don,
            SUM(tt.tong_tien_sau_giam) AS doanh_thu,
            SUM(tt.tien_mat)           AS thu_tien_mat,
            SUM(tt.tien_online)        AS thu_online
        ')->groupBy('ngay')->orderBy('ngay')->get();

        $rowCount = count($tableRows);
        $formattedTable = $tableRows->map(function ($row) use ($totalCogs, $rowCount) {
            $cogs = $rowCount > 0 ? round($totalCogs / $rowCount) : 0;
            return [
                'date'   => Carbon::parse($row->ngay)->format('d/m/Y'),
                'orders' => (int)   $row->so_don,
                'revenue'=> (float) $row->doanh_thu,
                'cogs'   => $cogs,
                'profit' => max(0, (float) $row->doanh_thu - $cogs),
                'cash'   => (float) $row->thu_tien_mat,
                'online' => (float) $row->thu_online,
            ];
        });

        // ─── 6. DANH SÁCH DỊCH VỤ FILTER ────────────────────────────
        $services = DB::table('dich_vus')
            ->select('id', 'ten')
            ->where('trang_thai', 'kinh_doanh')
            ->orderBy('ten')
            ->get();

        // ─── 7. LABEL MAP CHO DONUT ──────────────────────────────────
        $paymentLabelMap = [
            'tien_mat' => ['label' => 'Tiền mặt', 'color' => '#16a34a'],
            'vnpay'    => ['label' => 'VNPay',    'color' => '#8b5cf6'],
            'momo'     => ['label' => 'MoMo',     'color' => '#ec4899'],
            'ket_hop'  => ['label' => 'Kết hợp',  'color' => '#f59e0b'],
        ];

        // ─── EXPORT EXCEL ─────────────────────────────────────────
        if ($request->get('export') === 'excel') {
            return $this->exportExcel($formattedTable, $from, $to, $totalRevenue, $totalProfit, $totalOrders, $totalCogs);
        }

        return response()->json([
            'status' => true,
            'data'   => [
                'period'  => ['start' => $from->toDateString(), 'end' => $to->toDateString()],
                'summary' => [
                    'total_revenue'  => $totalRevenue,
                    'total_profit'   => $totalProfit,
                    'total_orders'   => $totalOrders,
                    'aov'            => $aov,
                    'total_cogs'     => $totalCogs,
                    'total_discount' => (float) ($summary->total_discount ?? 0),
                ],
                'chart' => [
                    'categories' => $chartCategories,
                    'revenue'    => $chartRevenue,
                    'profit'     => $chartProfit,
                ],
                'donut' => [
                    'by_payment' => $this->formatDonut($paymentRows, $totalRevenue, $paymentLabelMap, 'phuong_thuc'),
                    'by_service' => $this->formatDonut($serviceRows, $totalRevenue, [], 'ten_khoa'),
                ],
                'table'    => $formattedTable,
                'services' => $services,
            ],
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

    private function exportExcel($tableData, $from, $to, $totalRevenue, $totalProfit, $totalOrders, $totalCogs)
    {
        $col = fn(int $c) => Coordinate::stringFromColumnIndex($c);
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Doanh thu');

        $period  = $from->format('d/m/Y') . ' – ' . $to->format('d/m/Y');
        $lastRow = count($tableData) + 4;

        // ── Tiêu đề ──────────────────────────────────────────────────
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'BÁO CÁO DOANH THU — ' . strtoupper($period));
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold'=>true,'size'=>15,'color'=>['rgb'=>'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0D9488']],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Tóm tắt (dòng 2) ─────────────────────────────────────────
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2',
            'Tổng DT: ' . number_format($totalRevenue) . 'đ  |  ' .
            'Lợi nhuận: ' . number_format($totalProfit) . 'đ  |  ' .
            'Số đơn: ' . $totalOrders . '  |  ' .
            'Chi phí: ' . number_format($totalCogs) . 'đ'
        );
        $sheet->getStyle('A2')->applyFromArray([
            'font'      => ['size'=>10,'color'=>['rgb'=>'064E3B']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'ECFDF5']],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->mergeCells('A3:G3');
        $sheet->getRowDimension(3)->setRowHeight(6);

        // ── Header (dòng 4) ───────────────────────────────────────────
        $headers = [1=>'Ngày', 2=>'Số đơn', 3=>'Doanh thu (đ)', 4=>'Giá vốn (đ)', 5=>'Lợi nhuận (đ)', 6=>'Tiền mặt (đ)', 7=>'Online (đ)'];
        foreach ($headers as $c => $h) $sheet->setCellValue($col($c).'4', $h);
        $sheet->getStyle('A4:G4')->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER,'vertical'=>Alignment::VERTICAL_CENTER],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F766E']],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(22);

        // ── Data ──────────────────────────────────────────────────────
        foreach ($tableData as $idx => $row) {
            $r = $idx + 5;
            $rowArr = is_array($row) ? $row : (array)$row;
            $sheet->setCellValue($col(1).$r, $rowArr['date']    ?? '');
            $sheet->setCellValue($col(2).$r, $rowArr['orders']  ?? 0);
            $sheet->setCellValue($col(3).$r, $rowArr['revenue'] ?? 0);
            $sheet->setCellValue($col(4).$r, $rowArr['cogs']    ?? 0);
            $sheet->setCellValue($col(5).$r, $rowArr['profit']  ?? 0);
            $sheet->setCellValue($col(6).$r, $rowArr['cash']    ?? 0);
            $sheet->setCellValue($col(7).$r, $rowArr['online']  ?? 0);

            $bg = ($r % 2 === 0) ? 'F0FDFA' : 'FFFFFF';
            $sheet->getStyle("A{$r}:G{$r}")->applyFromArray([
                'fill' => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>$bg]],
            ]);
            $sheet->getRowDimension($r)->setRowHeight(18);
        }

        // ── Format số ────────────────────────────────────────────────
        foreach ([3,4,5,6,7] as $c) {
            $sheet->getStyle($col($c).'5:'.$col($c).$lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("B5:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ── Border ───────────────────────────────────────────────────
        $sheet->getStyle("A4:G{$lastRow}")->applyFromArray([
            'borders' => ['allBorders'=>['borderStyle'=>Border::BORDER_THIN,'color'=>['rgb'=>'D1D5DB']]],
        ]);

        // ── Dòng tổng ────────────────────────────────────────────────
        $sumRow = $lastRow + 1;
        $sheet->setCellValue("A{$sumRow}", 'TỔNG CỘNG');
        $tableArr = is_object($tableData) ? $tableData->toArray() : (array)$tableData;
        $sheet->setCellValue($col(2).$sumRow, array_sum(array_column($tableArr, 'orders')));
        $sheet->setCellValue($col(3).$sumRow, array_sum(array_column($tableArr, 'revenue')));
        $sheet->setCellValue($col(4).$sumRow, array_sum(array_column($tableArr, 'cogs')));
        $sheet->setCellValue($col(5).$sumRow, array_sum(array_column($tableArr, 'profit')));
        $sheet->setCellValue($col(6).$sumRow, array_sum(array_column($tableArr, 'cash')));
        $sheet->setCellValue($col(7).$sumRow, array_sum(array_column($tableArr, 'online')));
        foreach ([3,4,5,6,7] as $c) {
            $sheet->getStyle($col($c).$sumRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        $sheet->getStyle("A{$sumRow}:G{$sumRow}")->applyFromArray([
            'font'      => ['bold'=>true,'color'=>['rgb'=>'FFFFFF']],
            'fill'      => ['fillType'=>Fill::FILL_SOLID,'startColor'=>['rgb'=>'134E4A']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension($sumRow)->setRowHeight(22);

        // ── Độ rộng cột ──────────────────────────────────────────────
        foreach (['A'=>14,'B'=>10,'C'=>18,'D'=>18,'E'=>18,'F'=>18,'G'=>18] as $c=>$w) {
            $sheet->getColumnDimension($c)->setWidth($w);
        }

        $filename = 'bao-cao-doanh-thu-' . $from->format('Y-m') . '.xlsx';
        $writer   = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    private function formatDonut($collection, float $total, array $labelMap, string $keyField): array
    {
        $colors = ['#009689', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#ef4444', '#16a34a'];
        $result = [];
        foreach ($collection as $idx => $item) {
            $key        = $item->$keyField ?? 'khac';
            $meta       = $labelMap[$key] ?? null;
            $label      = $meta['label'] ?? ucfirst(str_replace('_', ' ', (string) $key));
            $color      = $meta['color'] ?? $colors[$idx % count($colors)];
            $value      = (float) $item->tong_tien;
            $percentage = $total > 0 ? round($value / $total * 100, 1) : 0;
            $result[]   = compact('key', 'label', 'value', 'percentage', 'color');
        }
        return $result;
    }
}