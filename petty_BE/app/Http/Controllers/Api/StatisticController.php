<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
 
class StatisticController extends Controller
{
    // ----------------------------------------------------------------
    // Helper: parse date range, mặc định 30 ngày gần nhất
    // ----------------------------------------------------------------
    private function getDateRange(Request $request): array
    {
        $to   = $request->filled('to')
            ? Carbon::parse($request->to)->endOfDay()
            : Carbon::now()->endOfDay();
 
        $from = $request->filled('from')
            ? Carbon::parse($request->from)->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();
 
        // Kỳ trước cùng độ dài để tính % thay đổi
        $days     = (int) $from->diffInDays($to) + 1;
        $prevTo   = (clone $from)->subSecond();
        $prevFrom = (clone $prevTo)->subDays($days - 1)->startOfDay();
 
        return compact('from', 'to', 'prevFrom', 'prevTo', 'days');
    }
 
    // Helper: tính % thay đổi
    private function calcChange($current, $previous): float
    {
        if ($previous > 0) {
            return round((($current - $previous) / $previous) * 100, 1);
        }
        return $current > 0 ? 100.0 : 0.0;
    }
 
    // ----------------------------------------------------------------
    // MAIN ENTRY POINT
    // ----------------------------------------------------------------
    public function getDashboardData(Request $request)
    {
        // Chỉ Admin mới được xem dashboard
        $user = $request->user();
        if (!$user || !($user instanceof Admin)) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ Admin mới có quyền xem báo cáo.',
            ], 403);
        }
 
        [
            'from'     => $from,
            'to'       => $to,
            'prevFrom' => $prevFrom,
            'prevTo'   => $prevTo,
            'days'     => $days,
        ] = $this->getDateRange($request);
 
        return response()->json([
            'success' => true,
            'data' => [
                'kpi'                 => $this->getKpi($from, $to, $prevFrom, $prevTo),
                'revenue_chart'       => $this->getRevenueChart($from, $to, $days),
                'appointment_status'  => $this->getAppointmentStatus($from, $to),
                'top_services'        => $this->getTopServices($from, $to),
                'staff_performance'   => $this->getStaffPerformance($from, $to),
                'new_customers_chart' => $this->getNewCustomersChart(),
                'customer_rank'       => $this->getCustomerRank(),
                'inventory_alerts'    => $this->getInventoryAlerts(),
            ],
            'meta' => [
                'from' => $from->toDateString(),
                'to'   => $to->toDateString(),
                'days' => $days,
            ],
        ]);
    }
 
    // ----------------------------------------------------------------
    // 1. KPI CARDS (4 thẻ tổng quan)
    // ----------------------------------------------------------------
    private function getKpi($from, $to, $prevFrom, $prevTo): array
    {
        // Doanh thu = lich_hens completed × dich_vus.gia_tien
        $revenue     = $this->calcRevenue($from, $to);
        $prevRevenue = $this->calcRevenue($prevFrom, $prevTo);
 
        // Tổng lịch hẹn trong kỳ
        $lichHen     = DB::table('lich_hens')
            ->whereBetween('ngay_gio', [$from, $to])
            ->count();
        $prevLichHen = DB::table('lich_hens')
            ->whereBetween('ngay_gio', [$prevFrom, $prevTo])
            ->count();
 
        // Khách hàng mới đăng ký trong kỳ
        $khachHangMoi     = DB::table('khach_hangs')
            ->whereBetween('created_at', [$from, $to])
            ->count();
        $prevKhachHangMoi = DB::table('khach_hangs')
            ->whereBetween('created_at', [$prevFrom, $prevTo])
            ->count();
 
        // Chi phí từ phiếu chi trong kỳ
        $chiPhi     = (float) DB::table('phieu_chis')
            ->whereBetween('ngay_chi', [$from->toDateString(), $to->toDateString()])
            ->sum('tong_so_tien');
        $prevChiPhi = (float) DB::table('phieu_chis')
            ->whereBetween('ngay_chi', [$prevFrom->toDateString(), $prevTo->toDateString()])
            ->sum('tong_so_tien');
 
        return [
            'doanh_thu' => [
                'value'  => $revenue,
                'change' => $this->calcChange($revenue, $prevRevenue),
            ],
            'lich_hen' => [
                'value'  => (int) $lichHen,
                'change' => $this->calcChange($lichHen, $prevLichHen),
            ],
            'khach_hang_moi' => [
                'value'  => (int) $khachHangMoi,
                'change' => $this->calcChange($khachHangMoi, $prevKhachHangMoi),
            ],
            'chi_phi' => [
                'value'  => $chiPhi,
                'change' => $this->calcChange($chiPhi, $prevChiPhi),
            ],
        ];
    }
 
    // Helper dùng chung để tính doanh thu
    // ── TRƯỚC (tính từ lich_hens) ──
private function calcRevenue($from, $to): float
{
    return (float) DB::table('thanh_toans')
        ->where('trang_thai', 'da_thanh_toan')
        ->whereBetween('ngay_thanh_toan', [$from, $to])
        ->sum('tong_tien_sau_giam');
}
 
// ── SAU (tính từ thanh_toans — chính xác hơn, có khuyến mãi) ──
 
 
 
    // ----------------------------------------------------------------
    // 2. BIỂU ĐỒ DOANH THU THEO NGÀY / THÁNG
    // ----------------------------------------------------------------
    private function getRevenueChart($from, $to, int $days): array
{
    $byDay = $days <= 60;
 
    $rows = DB::table('thanh_toans')
        ->where('trang_thai', 'da_thanh_toan')
        ->whereBetween('ngay_thanh_toan', [$from, $to])
        ->select(
            DB::raw($byDay
                ? "DATE(ngay_thanh_toan) as label"
                : "DATE_FORMAT(ngay_thanh_toan, '%Y-%m') as label"),
            DB::raw('SUM(tong_tien_sau_giam) as total')
        )
        ->groupBy('label')
        ->orderBy('label')
        ->get()
        ->keyBy('label');
 
    $result = [];
    if ($byDay) {
        $cursor = $from->copy()->startOfDay();
        while ($cursor->lte($to)) {
            $key      = $cursor->toDateString();
            $result[] = [
                'label' => $key,
                'value' => isset($rows[$key]) ? (float) $rows[$key]->total : 0,
            ];
            $cursor->addDay();
        }
    } else {
        $cursor = $from->copy()->startOfMonth();
        while ($cursor->lte($to)) {
            $key      = $cursor->format('Y-m');
            $result[] = [
                'label' => $cursor->format('m/Y'),
                'value' => isset($rows[$key]) ? (float) $rows[$key]->total : 0,
            ];
            $cursor->addMonth();
        }
    }
 
    return ['data' => $result, 'group_by' => $byDay ? 'day' : 'month'];
}
 
    // ----------------------------------------------------------------
    // 3. TRẠNG THÁI LỊCH HẸN (Donut chart)
    // ----------------------------------------------------------------
    private function getAppointmentStatus($from, $to): array
    {
        $rows = DB::table('lich_hens')
            ->whereBetween('ngay_gio', [$from, $to])
            ->select('trang_thai', DB::raw('COUNT(*) as total'))
            ->groupBy('trang_thai')
            ->get()
            ->keyBy('trang_thai');
 
        $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
        $result   = [];
 
        foreach ($statuses as $status) {
            $result[$status] = isset($rows[$status]) ? (int) $rows[$status]->total : 0;
        }
 
        return $result;
    }
 
    // ----------------------------------------------------------------
    // 4. TOP 5 DỊCH VỤ PHỔ BIẾN
    // ----------------------------------------------------------------
    private function getTopServices($from, $to): array
    {
        return DB::table('lich_hens')
            ->join('dich_vus', 'lich_hens.dich_vu_id', '=', 'dich_vus.id')
            ->whereBetween('lich_hens.ngay_gio', [$from, $to])
            ->whereNotNull('lich_hens.dich_vu_id')
            ->select(
                'dich_vus.ten as name',
                DB::raw('COUNT(lich_hens.id) as total_appointments'),
                DB::raw("SUM(CASE WHEN lich_hens.trang_thai = 'completed' THEN dich_vus.gia_tien ELSE 0 END) as revenue")
            )
            ->groupBy('dich_vus.id', 'dich_vus.ten')
            ->orderByDesc('total_appointments')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'name'               => $r->name,
                'total_appointments' => (int)   $r->total_appointments,
                'revenue'            => (float) $r->revenue,
            ])
            ->values()
            ->toArray();
    }
 
    // ----------------------------------------------------------------
    // 5. HIỆU SUẤT NHÂN VIÊN (Top 8 bác sĩ / y tá)
    // ----------------------------------------------------------------
    private function getStaffPerformance($from, $to): array
    {
        return DB::table('lich_hens')
            ->join('nhan_viens', 'lich_hens.nhan_vien_id', '=', 'nhan_viens.id')
            ->whereBetween('lich_hens.ngay_gio', [$from, $to])
            ->whereNotNull('lich_hens.nhan_vien_id')
            ->select(
                'nhan_viens.full_name as name',
                'nhan_viens.vai_tro',
                DB::raw('COUNT(lich_hens.id) as total'),
                DB::raw("SUM(CASE WHEN lich_hens.trang_thai = 'completed' THEN 1 ELSE 0 END) as completed")
            )
            ->groupBy('nhan_viens.id', 'nhan_viens.full_name', 'nhan_viens.vai_tro')
            ->orderByDesc('total')
            ->limit(8)
            ->get()
            ->map(fn($r) => [
                'name'            => $r->name,
                'vai_tro'         => $r->vai_tro === 'bac_si' ? 'Bác sĩ' : 'Y tá',
                'total'           => (int) $r->total,
                'completed'       => (int) $r->completed,
                'completion_rate' => $r->total > 0
                    ? round(($r->completed / $r->total) * 100, 1)
                    : 0,
            ])
            ->values()
            ->toArray();
    }
 
    // ----------------------------------------------------------------
    // 6. KHÁCH HÀNG MỚI THEO THÁNG (6 tháng gần nhất)
    // ----------------------------------------------------------------
    private function getNewCustomersChart(): array
    {
        $rows = DB::table('khach_hangs')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_key"),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');
 
        $result = [];
        for ($i = 5; $i >= 0; $i--) {
            $date     = Carbon::now()->subMonths($i);
            $key      = $date->format('Y-m');
            $result[] = [
                'label' => 'T' . $date->format('n') . '/' . $date->format('y'),
                'value' => isset($rows[$key]) ? (int) $rows[$key]->total : 0,
            ];
        }
 
        return $result;
    }
 
    // ----------------------------------------------------------------
    // 7. PHÂN HẠNG KHÁCH HÀNG (Silver / Gold / Diamond)
    // ----------------------------------------------------------------
    private function getCustomerRank(): array
    {
        $rows = DB::table('khach_hangs')
            ->where('trang_thai', 'active')
            ->select('rank', DB::raw('COUNT(*) as total'))
            ->groupBy('rank')
            ->get()
            ->keyBy('rank');
 
        $grandTotal = $rows->sum('total');
 
        return collect(['Silver', 'Gold', 'Diamond'])->map(function ($rank) use ($rows, $grandTotal) {
            $count = isset($rows[$rank]) ? (int) $rows[$rank]->total : 0;
            return [
                'rank'       => $rank,
                'count'      => $count,
                'percentage' => $grandTotal > 0 ? round(($count / $grandTotal) * 100, 1) : 0,
            ];
        })->values()->toArray();
    }
 
    // ----------------------------------------------------------------
    // 8. CẢNH BÁO KHO (tồn kho ≤ định mức tối thiểu)
    // ----------------------------------------------------------------
    private function getInventoryAlerts(): array
    {
        // Tính tồn kho từ phiếu nhập đã duyệt
        $stockSub = DB::table('chi_tiet_phieu_nhap_khos')
            ->join('phieu_nhap_khos', 'chi_tiet_phieu_nhap_khos.phieu_nhap_kho_id', '=', 'phieu_nhap_khos.id')
            ->where('phieu_nhap_khos.trang_thai', 'da_duyet')
            ->select(
                'chi_tiet_phieu_nhap_khos.hang_hoa_id',
                DB::raw('SUM(chi_tiet_phieu_nhap_khos.so_luong) as ton_kho')
            )
            ->groupBy('chi_tiet_phieu_nhap_khos.hang_hoa_id');
 
        return DB::table('hang_hoas')
            ->leftJoinSub($stockSub, 'stock', 'hang_hoas.id', '=', 'stock.hang_hoa_id')
            ->where('hang_hoas.tinh_trang', 'hoat_dong')
            ->whereRaw('COALESCE(stock.ton_kho, 0) <= hang_hoas.dinh_muc_toi_thieu')
            ->select(
                'hang_hoas.ma_hang_hoa',
                'hang_hoas.ten_mat_hang as name',
                'hang_hoas.don_vi_tinh',
                'hang_hoas.dinh_muc_toi_thieu',
                DB::raw('COALESCE(stock.ton_kho, 0) as ton_kho')
            )
            ->orderByRaw('COALESCE(stock.ton_kho, 0) ASC')
            ->limit(10)
            ->get()
            ->map(fn($r) => [
                'ma_hang_hoa'        => $r->ma_hang_hoa,
                'name'               => $r->name,
                'don_vi_tinh'        => $r->don_vi_tinh,
                'ton_kho'            => (int) $r->ton_kho,
                'dinh_muc_toi_thieu' => (int) $r->dinh_muc_toi_thieu,
            ])
            ->values()
            ->toArray();
    }
}
 