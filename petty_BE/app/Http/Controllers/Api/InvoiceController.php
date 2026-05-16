<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * GET /api/invoices
     *
     * Query params:
     *   - search      : Mã HĐ, tên KH, SĐT
     *   - period      : today | 7days | 30days | this_month | custom
     *   - start       : Y-m-d (khi period=custom)
     *   - end         : Y-m-d (khi period=custom)
     *   - trang_thai  : cho_thanh_toan | da_thanh_toan | hoan_tien | all
     *   - hinh_thuc   : tien_mat | vnpay | momo | ket_hop | all
     *   - page        : số trang (mặc định 1)
     *   - per_page    : số bản ghi/trang (mặc định 20)
     */
    public function index(Request $request)
    {
        [$from, $to] = $this->resolveDateRange($request);

        $query = DB::table('thanh_toans as tt')
            ->leftJoin('lich_hens as lh', 'tt.lich_hen_id',   '=', 'lh.id')
            ->leftJoin('khach_hangs as kh', 'tt.khach_hang_id', '=', 'kh.id')
            ->leftJoin('thu_cungs as tc',   'lh.thu_cung_id',   '=', 'tc.id')
            ->leftJoin('nhan_viens as nv',  'tt.nhan_vien_id',  '=', 'nv.id')
            ->leftJoin('admins as ad',      'tt.admin_id',      '=', 'ad.id')
            // ✅ Dùng COALESCE: ưu tiên ngay_thanh_toan, fallback về created_at
            // giống cách dashboard tính doanh thu
            ->whereBetween(
                DB::raw('COALESCE(tt.ngay_thanh_toan, tt.created_at)'),
                [$from, $to]
            )
            ->select(
                'tt.id',
                'tt.ma_thanh_toan',
                'tt.tong_tien_goc',
                'tt.so_tien_giam',
                'tt.tong_tien_sau_giam',
                'tt.hinh_thuc_thanh_toan',
                'tt.trang_thai',
                'tt.ngay_thanh_toan',
                'tt.ghi_chu',
                'tt.created_at',
                'kh.full_name as khach_hang_ten',
                'kh.phone as khach_hang_phone',
                'tc.ten_thu_cung',
                'lh.ngay_gio as lich_hen_ngay_gio',
                'nv.full_name as nhan_vien_ten',
                'ad.ho_ten as admin_ten'
            );

        // Filter tìm kiếm
        if ($search = trim($request->get('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('tt.ma_thanh_toan', 'like', "%{$search}%")
                  ->orWhere('kh.full_name',   'like', "%{$search}%")
                  ->orWhere('kh.phone',        'like', "%{$search}%");
            });
        }

        // Filter trạng thái
        $trangThai = $request->get('trang_thai');
        if ($trangThai && $trangThai !== 'all') {
            $query->where('tt.trang_thai', $trangThai);
        }

        // Filter hình thức
        $hinhThuc = $request->get('hinh_thuc');
        if ($hinhThuc && $hinhThuc !== 'all') {
            $query->where('tt.hinh_thuc_thanh_toan', $hinhThuc);
        }

        $total   = $query->count();
        $perPage = max(1, (int) $request->get('per_page', 20));
        $page    = max(1, (int) $request->get('page', 1));

        $invoices = $query
            ->orderByDesc(DB::raw('COALESCE(tt.ngay_thanh_toan, tt.created_at)'))
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get()
            ->map(fn($row) => $this->formatInvoice($row));

        return response()->json([
            'status' => true,
            'data'   => [
                'summary'    => $this->getSummary(),
                'invoices'   => $invoices,
                'pagination' => [
                    'total'        => $total,
                    'per_page'     => $perPage,
                    'current_page' => $page,
                    'last_page'    => (int) ceil($total / max($perPage, 1)),
                ],
            ],
        ]);
    }

    /**
     * GET /api/invoices/{id}
     */
    public function show($id)
    {
        $row = DB::table('thanh_toans as tt')
            ->leftJoin('lich_hens as lh',   'tt.lich_hen_id',   '=', 'lh.id')
            ->leftJoin('khach_hangs as kh',  'tt.khach_hang_id', '=', 'kh.id')
            ->leftJoin('thu_cungs as tc',    'lh.thu_cung_id',   '=', 'tc.id')
            ->leftJoin('nhan_viens as nv',   'tt.nhan_vien_id',  '=', 'nv.id')
            ->leftJoin('admins as ad',       'tt.admin_id',      '=', 'ad.id')
            ->leftJoin('khuyen_mais as km',  'tt.khuyen_mai_id', '=', 'km.id')
            ->where('tt.id', $id)
            ->select(
                'tt.*',
                'kh.full_name as khach_hang_ten',
                'kh.phone as khach_hang_phone',
                'kh.email as khach_hang_email',
                'tc.ten_thu_cung',
                'tc.loai_thu_cung',
                'lh.ngay_gio as lich_hen_ngay_gio',
                'nv.full_name as nhan_vien_ten',
                'ad.ho_ten as admin_ten',
                'km.ten_khuyen_mai'
            )
            ->first();

        if (!$row) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy hóa đơn'], 404);
        }

        // Dịch vụ trong lịch hẹn
        $items = [];
        if (!empty($row->lich_hen_id)) {
            $items = DB::table('lich_hen_dich_vu as ldv')
                ->join('dich_vus as dv', 'ldv.dich_vu_id', '=', 'dv.id')
                ->where('ldv.lich_hen_id', $row->lich_hen_id)
                ->select('dv.ten as name', 'ldv.so_luong as quantity', 'ldv.don_gia as price', 'ldv.thanh_tien as total')
                ->get()
                ->toArray();
        }

        $invoice              = $this->formatInvoice($row);
        $invoice['email']     = $row->khach_hang_email ?? '';
        $invoice['petType']   = $row->loai_thu_cung    ?? '';
        $invoice['items']     = $items;
        $invoice['promotion'] = $row->ten_khuyen_mai ? [
            'code'        => $row->ma_giam_gia ?? '',
            'description' => $row->ten_khuyen_mai,
            'discount'    => -(float) ($row->so_tien_giam ?? 0),
        ] : null;

        return response()->json(['status' => true, 'data' => $invoice]);
    }

    // ─── Private helpers ──────────────────────────────────────────────

    private function formatInvoice(object $row): array
    {
        // Ưu tiên ngay_thanh_toan (giống dashboard), fallback về lich_hen hoặc created_at
        $ngayGio  = $row->ngay_thanh_toan ?? $row->lich_hen_ngay_gio ?? $row->created_at;
        $dt       = Carbon::parse($ngayGio);
        $collector = $row->nhan_vien_ten ?? $row->admin_ten ?? '—';

        return [
            'id'            => $row->id,
            'code'          => $row->ma_thanh_toan        ?? '—',
            'time'          => $dt->format('H:i'),
            'date'          => $dt->format('d/m/Y'),
            'customer'      => $row->khach_hang_ten        ?? 'Khách lẻ',
            'phone'         => $row->khach_hang_phone      ?? '',
            'petName'       => $row->ten_thu_cung          ?? '—',
            'totalAmount'   => (float) ($row->tong_tien_goc      ?? 0),
            'discount'      => (float) ($row->so_tien_giam       ?? 0),
            'paidAmount'    => (float) ($row->tong_tien_sau_giam ?? 0),
            'paymentMethod' => $this->mapHinhThuc($row->hinh_thuc_thanh_toan ?? ''),
            'status'        => $this->mapTrangThai($row->trang_thai          ?? ''),
            'collector'     => $collector,
            'ghi_chu'       => $row->ghi_chu ?? '',
        ];
    }

    private function getSummary(): array
    {
        $today = Carbon::today();

        return [
            // Doanh thu hôm nay: đã thanh toán, tính theo ngay_thanh_toan (giống dashboard)
            'doanh_thu_hom_nay' => (float) DB::table('thanh_toans')
                ->where('trang_thai', 'da_thanh_toan')
                ->whereDate('ngay_thanh_toan', $today)
                ->sum('tong_tien_sau_giam'),

            // Tổng chưa thanh toán (không giới hạn ngày)
            'chua_thanh_toan' => (float) DB::table('thanh_toans')
                ->where('trang_thai', 'cho_thanh_toan')
                ->sum('tong_tien_sau_giam'),

            // Tổng đã hoàn tiền (không giới hạn ngày)
            'da_hoan_tien' => (float) DB::table('thanh_toans')
                ->where('trang_thai', 'hoan_tien')
                ->sum('tong_tien_sau_giam'),
        ];
    }

    private function mapHinhThuc(string $value): string
    {
        return match ($value) {
            'tien_mat'      => 'cash',
            'chuyen_khoan'  => 'transfer',
            'vnpay'         => 'vnpay',
            'momo'          => 'momo',
            'ket_hop'       => 'transfer',
            default         => 'cash',
        };
    }

    private function mapTrangThai(string $value): string
    {
        return match ($value) {
            'da_thanh_toan'  => 'paid',
            'cho_thanh_toan' => 'unpaid',
            'hoan_tien'      => 'refunded',
            default          => 'unpaid',
        };
    }

    private function resolveDateRange(Request $request): array
    {
        $period = $request->get('period', 'today');
        return match ($period) {
            'today'      => [Carbon::today()->startOfDay(),            Carbon::today()->endOfDay()],
            '7days'      => [Carbon::now()->subDays(6)->startOfDay(),  Carbon::now()->endOfDay()],
            '30days'     => [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()],
            'this_month' => [Carbon::now()->startOfMonth(),            Carbon::now()->endOfMonth()],
            'custom'     => [
                Carbon::parse($request->get('start', now()->startOfMonth()))->startOfDay(),
                Carbon::parse($request->get('end',   now()))->endOfDay(),
            ],
            default => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
        };
    }
}