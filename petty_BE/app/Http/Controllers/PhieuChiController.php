<?php

namespace App\Http\Controllers;

use App\Models\PhieuChi;
use App\Models\NhaCungCap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class PhieuChiController extends Controller
{
    /**
     * Lấy danh sách phiếu chi
     */
    public function index(Request $request)
    {
        $query = PhieuChi::query();

        // Eager loading
        $query->with([
            'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
            'nhanVien:id,full_name',
            'admin:id,ho_ten,email',
        ]);

        // Lọc theo loại phiếu chi
        if ($request->has('loai_phieu_chi')) {
            $query->where('loai_phieu_chi', $request->loai_phieu_chi);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo nhà cung cấp
        if ($request->has('nha_cung_cap_id')) {
            $query->where('nha_cung_cap_id', $request->nha_cung_cap_id);
        }

        // Lọc theo khoảng thời gian
        if ($request->has('tu_ngay')) {
            $query->whereDate('ngay_chi', '>=', $request->tu_ngay);
        }

        if ($request->has('den_ngay')) {
            $query->whereDate('ngay_chi', '<=', $request->den_ngay);
        }

        // Tìm kiếm theo mã phiếu chi
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ma_phieu_chi', 'like', "%{$search}%")
                    ->orWhere('ly_do_chi', 'like', "%{$search}%")
                    ->orWhere('doi_tuong_nhan_tien', 'like', "%{$search}%");
            });
        }

        // Sắp xếp
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Phân trang
        $perPage = $request->get('per_page', 15);
        $phieuChis = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'message' => 'Lấy danh sách phiếu chi thành công',
            'data' => $phieuChis->map(function ($phieu) {
                return [
                    'id' => $phieu->id,
                    'ma_phieu_chi' => $phieu->ma_phieu_chi,
                    'loai_phieu_chi' => $phieu->loai_phieu_chi,
                    'loai_phieu_chi_label' => $phieu->loai_phieu_chi_label,
                    'ly_do_chi' => $phieu->ly_do_chi,
                    'tong_so_tien' => $phieu->tong_so_tien,
                    'so_tien_thanh_toan_ngay' => $phieu->so_tien_thanh_toan_ngay,
                    'so_tien_con_no' => $phieu->so_tien_con_no,
                    'tien_mat' => $phieu->tien_mat,
                    'tien_chuyen_khoan' => $phieu->tien_chuyen_khoan,
                    'doi_tuong_nhan_tien' => $phieu->doi_tuong_nhan_tien,
                    'trang_thai' => $phieu->trang_thai,
                    'trang_thai_label' => $phieu->trang_thai_label,
                    'ngay_chi' => $phieu->ngay_chi->format('Y-m-d'),
                    'ghi_chu' => $phieu->ghi_chu,
                    'anh_chung_tu' => $phieu->anh_chung_tu,
                    'nha_cung_cap' => $phieu->nhaCungCap ? [
                        'id' => $phieu->nhaCungCap->id,
                        'ten_nha_cung_cap' => $phieu->nhaCungCap->ten_nha_cung_cap,
                        'ma_nha_cung_cap' => $phieu->nhaCungCap->ma_nha_cung_cap,
                    ] : null,
                    'nguoi_tao' => $phieu->nguoi_tao_info,
                    'created_at' => $phieu->created_at,
                    'updated_at' => $phieu->updated_at,
                ];
            }),
            'pagination' => [
                'current_page' => $phieuChis->currentPage(),
                'per_page' => $phieuChis->perPage(),
                'total' => $phieuChis->total(),
                'last_page' => $phieuChis->lastPage(),
            ],
        ]);
    }

    /**
     * Thêm mới phiếu chi
     */
    public function store(Request $request)
    {
        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'loai_phieu_chi' => 'required|in:chi_nhap_hang,chi_van_hanh',
            'ly_do_chi' => 'required|string',
            'tong_so_tien' => 'required|numeric|min:0',
            'so_tien_thanh_toan_ngay' => 'nullable|numeric|min:0',
            'tien_mat' => 'nullable|numeric|min:0',
            'tien_chuyen_khoan' => 'nullable|numeric|min:0',
            'doi_tuong_nhan_tien' => 'required_if:loai_phieu_chi,chi_van_hanh|string|nullable',
            'nha_cung_cap_id' => 'required_if:loai_phieu_chi,chi_nhap_hang|exists:nha_cung_caps,id|nullable',
            'ngay_chi' => 'required|date',
            'ghi_chu' => 'nullable|string',
            'anh_chung_tu' => 'nullable|array',
            'anh_chung_tu.*' => 'nullable|string', // Base64 hoặc URL
        ], [
            'loai_phieu_chi.required' => 'Vui lòng chọn loại phiếu chi',
            'loai_phieu_chi.in' => 'Loại phiếu chi không hợp lệ',
            'ly_do_chi.required' => 'Vui lòng nhập lý do chi',
            'tong_so_tien.required' => 'Vui lòng nhập tổng số tiền',
            'tong_so_tien.numeric' => 'Tổng số tiền phải là số',
            'tong_so_tien.min' => 'Tổng số tiền phải lớn hơn hoặc bằng 0',
            'doi_tuong_nhan_tien.required_if' => 'Vui lòng nhập đối tượng nhận tiền khi chi vận hành',
            'nha_cung_cap_id.required_if' => 'Vui lòng chọn nhà cung cấp khi chi nhập hàng',
            'nha_cung_cap_id.exists' => 'Nhà cung cấp không tồn tại',
            'ngay_chi.required' => 'Vui lòng chọn ngày chi',
            'ngay_chi.date' => 'Ngày chi không hợp lệ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Tạo mã phiếu chi tự động
            $maPhieuChi = $this->generateMaPhieuChi($request->loai_phieu_chi);

            // Xử lý ảnh chứng từ
            $anhChungTu = [];
            if ($request->has('anh_chung_tu') && is_array($request->anh_chung_tu)) {
                foreach ($request->anh_chung_tu as $anh) {
                    if (!empty($anh)) {
                        // Nếu là base64
                        if (preg_match('/^data:image\/(\w+);base64,/', $anh, $type)) {
                            $anh = substr($anh, strpos($anh, ',') + 1);
                            $type = strtolower($type[1]);
                            $anh = base64_decode($anh);

                            $fileName = 'phieu_chi_' . uniqid() . '.' . $type;
                            $filePath = 'phieu_chi/' . $fileName;

                            Storage::disk('public')->put($filePath, $anh);
                            $anhChungTu[] = $filePath;
                        } else {
                            // Nếu là URL hoặc đường dẫn
                            $anhChungTu[] = $anh;
                        }
                    }
                }
            }

            // Tạo phiếu chi
            $phieuChi = PhieuChi::create([
                'ma_phieu_chi' => $maPhieuChi,
                'loai_phieu_chi' => $request->loai_phieu_chi,
                'ly_do_chi' => $request->ly_do_chi,
                'tong_so_tien' => $request->tong_so_tien,
                'so_tien_thanh_toan_ngay' => $request->so_tien_thanh_toan_ngay ?? 0,
                'tien_mat' => $request->tien_mat ?? 0,
                'tien_chuyen_khoan' => $request->tien_chuyen_khoan ?? 0,
                'doi_tuong_nhan_tien' => $request->doi_tuong_nhan_tien,
                'nha_cung_cap_id' => $request->nha_cung_cap_id,
                'ngay_chi' => $request->ngay_chi,
                'ghi_chu' => $request->ghi_chu,
                'anh_chung_tu' => $anhChungTu,
            ]);
            // Lưu ý: admin_id, nhan_vien_id, nguoi_tao_type, so_tien_con_no, trang_thai
            // sẽ được tự động xử lý trong model boot()

            DB::commit();

            // Load relationships
            $phieuChi->load([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thêm phiếu chi thành công',
                'data' => [
                    'id' => $phieuChi->id,
                    'ma_phieu_chi' => $phieuChi->ma_phieu_chi,
                    'loai_phieu_chi' => $phieuChi->loai_phieu_chi,
                    'loai_phieu_chi_label' => $phieuChi->loai_phieu_chi_label,
                    'ly_do_chi' => $phieuChi->ly_do_chi,
                    'tong_so_tien' => $phieuChi->tong_so_tien,
                    'so_tien_thanh_toan_ngay' => $phieuChi->so_tien_thanh_toan_ngay,
                    'so_tien_con_no' => $phieuChi->so_tien_con_no,
                    'tien_mat' => $phieuChi->tien_mat,
                    'tien_chuyen_khoan' => $phieuChi->tien_chuyen_khoan,
                    'doi_tuong_nhan_tien' => $phieuChi->doi_tuong_nhan_tien,
                    'trang_thai' => $phieuChi->trang_thai,
                    'trang_thai_label' => $phieuChi->trang_thai_label,
                    'ngay_chi' => $phieuChi->ngay_chi->format('Y-m-d'),
                    'ghi_chu' => $phieuChi->ghi_chu,
                    'anh_chung_tu' => $phieuChi->anh_chung_tu,
                    'nha_cung_cap' => $phieuChi->nhaCungCap ? [
                        'id' => $phieuChi->nhaCungCap->id,
                        'ten_nha_cung_cap' => $phieuChi->nhaCungCap->ten_nha_cung_cap,
                        'ma_nha_cung_cap' => $phieuChi->nhaCungCap->ma_nha_cung_cap,
                    ] : null,
                    'nguoi_tao' => $phieuChi->nguoi_tao_info,
                    'created_at' => $phieuChi->created_at,
                    'updated_at' => $phieuChi->updated_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi thêm phiếu chi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xem chi tiết phiếu chi
     */
    public function show($id)
    {
        $phieuChi = PhieuChi::with([
            'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap,so_dien_thoai,email,dia_chi',
            'nhanVien:id,full_name,email,so_dien_thoai',
            'admin:id,ho_ten,email',
        ])->find($id);

        if (!$phieuChi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phiếu chi',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Lấy thông tin phiếu chi thành công',
            'data' => [
                'id' => $phieuChi->id,
                'ma_phieu_chi' => $phieuChi->ma_phieu_chi,
                'loai_phieu_chi' => $phieuChi->loai_phieu_chi,
                'loai_phieu_chi_label' => $phieuChi->loai_phieu_chi_label,
                'ly_do_chi' => $phieuChi->ly_do_chi,
                'tong_so_tien' => $phieuChi->tong_so_tien,
                'so_tien_thanh_toan_ngay' => $phieuChi->so_tien_thanh_toan_ngay,
                'so_tien_con_no' => $phieuChi->so_tien_con_no,
                'tien_mat' => $phieuChi->tien_mat,
                'tien_chuyen_khoan' => $phieuChi->tien_chuyen_khoan,
                'doi_tuong_nhan_tien' => $phieuChi->doi_tuong_nhan_tien,
                'trang_thai' => $phieuChi->trang_thai,
                'trang_thai_label' => $phieuChi->trang_thai_label,
                'ngay_chi' => $phieuChi->ngay_chi->format('Y-m-d'),
                'ghi_chu' => $phieuChi->ghi_chu,
                'anh_chung_tu' => $phieuChi->anh_chung_tu,
                'nha_cung_cap' => $phieuChi->nhaCungCap ? [
                    'id' => $phieuChi->nhaCungCap->id,
                    'ten_nha_cung_cap' => $phieuChi->nhaCungCap->ten_nha_cung_cap,
                    'ma_nha_cung_cap' => $phieuChi->nhaCungCap->ma_nha_cung_cap,
                    'so_dien_thoai' => $phieuChi->nhaCungCap->so_dien_thoai,
                    'email' => $phieuChi->nhaCungCap->email,
                    'dia_chi' => $phieuChi->nhaCungCap->dia_chi,
                ] : null,
                'nguoi_tao' => $phieuChi->nguoi_tao_info,
                'created_at' => $phieuChi->created_at,
                'updated_at' => $phieuChi->updated_at,
            ],
        ]);
    }

    /**
     * Tạo mã phiếu chi tự động
     */
    private function generateMaPhieuChi($loaiPhieuChi)
    {
        $prefix = $loaiPhieuChi === 'chi_nhap_hang' ? 'PCN' : 'PCV';
        $date = now()->format('ymd');

        // Lấy số thứ tự phiếu trong ngày
        $count = PhieuChi::where('ma_phieu_chi', 'like', "{$prefix}{$date}%")
            ->count() + 1;

        // Tạo mã phiếu với format: PCN/PCV + yymmdd + 4 số
        $maPhieuChi = $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);

        // Kiểm tra nếu mã đã tồn tại (tránh trùng lặp)
        while (PhieuChi::where('ma_phieu_chi', $maPhieuChi)->exists()) {
            $count++;
            $maPhieuChi = $prefix . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
        }

        return $maPhieuChi;
    }

    /**
     * Cập nhật phiếu chi
     */
    public function update(Request $request, $id)
    {
        $phieuChi = PhieuChi::find($id);

        if (!$phieuChi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phiếu chi',
            ], 404);
        }

        // Validate dữ liệu
        $validator = Validator::make($request->all(), [
            'loai_phieu_chi' => 'nullable|in:chi_nhap_hang,chi_van_hanh',
            'ly_do_chi' => 'nullable|string',
            'tong_so_tien' => 'nullable|numeric|min:0',
            'so_tien_thanh_toan_ngay' => 'nullable|numeric|min:0',
            'tien_mat' => 'nullable|numeric|min:0',
            'tien_chuyen_khoan' => 'nullable|numeric|min:0',
            'doi_tuong_nhan_tien' => 'nullable|string',
            'nha_cung_cap_id' => 'nullable|exists:nha_cung_caps,id',
            'ngay_chi' => 'nullable|date',
            'ghi_chu' => 'nullable|string',
            'anh_chung_tu' => 'nullable|array',
            'anh_chung_tu.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Xử lý ảnh chứng từ nếu có
            $anhChungTu = $phieuChi->anh_chung_tu ?? [];
            if ($request->has('anh_chung_tu') && is_array($request->anh_chung_tu)) {
                $anhChungTu = [];
                foreach ($request->anh_chung_tu as $anh) {
                    if (!empty($anh)) {
                        // Nếu là base64
                        if (preg_match('/^data:image\/(\w+);base64,/', $anh, $type)) {
                            $anh = substr($anh, strpos($anh, ',') + 1);
                            $type = strtolower($type[1]);
                            $anh = base64_decode($anh);

                            $fileName = 'phieu_chi_' . uniqid() . '.' . $type;
                            $filePath = 'phieu_chi/' . $fileName;

                            Storage::disk('public')->put($filePath, $anh);
                            $anhChungTu[] = $filePath;
                        } else {
                            // Nếu là URL hoặc đường dẫn
                            $anhChungTu[] = $anh;
                        }
                    }
                }
            }

            // Cập nhật phiếu chi
            $phieuChi->update([
                'loai_phieu_chi' => $request->loai_phieu_chi ?? $phieuChi->loai_phieu_chi,
                'ly_do_chi' => $request->ly_do_chi ?? $phieuChi->ly_do_chi,
                'tong_so_tien' => $request->tong_so_tien ?? $phieuChi->tong_so_tien,
                'so_tien_thanh_toan_ngay' => $request->so_tien_thanh_toan_ngay ?? $phieuChi->so_tien_thanh_toan_ngay,
                'tien_mat' => $request->tien_mat ?? $phieuChi->tien_mat,
                'tien_chuyen_khoan' => $request->tien_chuyen_khoan ?? $phieuChi->tien_chuyen_khoan,
                'doi_tuong_nhan_tien' => $request->doi_tuong_nhan_tien ?? $phieuChi->doi_tuong_nhan_tien,
                'nha_cung_cap_id' => $request->nha_cung_cap_id ?? $phieuChi->nha_cung_cap_id,
                'ngay_chi' => $request->ngay_chi ?? $phieuChi->ngay_chi,
                'ghi_chu' => $request->ghi_chu ?? $phieuChi->ghi_chu,
                'anh_chung_tu' => $anhChungTu,
            ]);
            // Lưu ý: so_tien_con_no và trang_thai sẽ được tự động cập nhật trong model boot()

            DB::commit();

            // Load relationships
            $phieuChi->load([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Cập nhật phiếu chi thành công',
                'data' => [
                    'id' => $phieuChi->id,
                    'ma_phieu_chi' => $phieuChi->ma_phieu_chi,
                    'loai_phieu_chi' => $phieuChi->loai_phieu_chi,
                    'loai_phieu_chi_label' => $phieuChi->loai_phieu_chi_label,
                    'ly_do_chi' => $phieuChi->ly_do_chi,
                    'tong_so_tien' => $phieuChi->tong_so_tien,
                    'so_tien_thanh_toan_ngay' => $phieuChi->so_tien_thanh_toan_ngay,
                    'so_tien_con_no' => $phieuChi->so_tien_con_no,
                    'tien_mat' => $phieuChi->tien_mat,
                    'tien_chuyen_khoan' => $phieuChi->tien_chuyen_khoan,
                    'doi_tuong_nhan_tien' => $phieuChi->doi_tuong_nhan_tien,
                    'trang_thai' => $phieuChi->trang_thai,
                    'trang_thai_label' => $phieuChi->trang_thai_label,
                    'ngay_chi' => $phieuChi->ngay_chi->format('Y-m-d'),
                    'ghi_chu' => $phieuChi->ghi_chu,
                    'anh_chung_tu' => $phieuChi->anh_chung_tu,
                    'nha_cung_cap' => $phieuChi->nhaCungCap ? [
                        'id' => $phieuChi->nhaCungCap->id,
                        'ten_nha_cung_cap' => $phieuChi->nhaCungCap->ten_nha_cung_cap,
                        'ma_nha_cung_cap' => $phieuChi->nhaCungCap->ma_nha_cung_cap,
                    ] : null,
                    'nguoi_tao' => $phieuChi->nguoi_tao_info,
                    'created_at' => $phieuChi->created_at,
                    'updated_at' => $phieuChi->updated_at,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật phiếu chi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa phiếu chi
     */
    public function destroy($id)
    {
        $phieuChi = PhieuChi::find($id);

        if (!$phieuChi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phiếu chi',
            ], 404);
        }

        // Xóa ảnh chứng từ nếu có
        if ($phieuChi->anh_chung_tu && is_array($phieuChi->anh_chung_tu)) {
            foreach ($phieuChi->anh_chung_tu as $anh) {
                if (Storage::disk('public')->exists($anh)) {
                    Storage::disk('public')->delete($anh);
                }
            }
        }

        $phieuChi->delete();

        return response()->json([
            'status' => true,
            'message' => 'Xóa phiếu chi thành công',
        ]);
    }

    /**
     * Xuất file PDF phiếu chi
     */
    public function exportPdf($id)
    {
        $phieuChi = PhieuChi::with([
            'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap,so_dien_thoai,email,dia_chi',
            'nhanVien:id,full_name,email,so_dien_thoai',
            'admin:id,ho_ten,email',
            'lichSuThanhToan.admin:id,ho_ten,email',
            'lichSuThanhToan.nhanVien:id,full_name,email',
        ])->find($id);

        if (!$phieuChi) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy phiếu chi',
            ], 404);
        }

        // Chuẩn bị dữ liệu cho PDF
        $data = [
            'phieuChi' => $phieuChi,
            'ngayXuat' => now()->format('d/m/Y H:i'),
        ];

        // Tạo PDF
        $pdf = Pdf::loadView('pdf.phieu-chi', $data);

        // Cấu hình PDF
        $pdf->setPaper('A4', 'portrait');

        // Tên file
        $fileName = 'PhieuChi_' . $phieuChi->ma_phieu_chi . '_' . now()->format('YmdHis') . '.pdf';

        // Trả về file PDF để download
        return $pdf->download($fileName);
    }

    /**
     * Lấy lịch sử thanh toán của phiếu chi
     */
    public function getLichSuThanhToan($id)
    {
        try {
            $phieuChi = PhieuChi::findOrFail($id);

            $lichSu = $phieuChi->lichSuThanhToan()
                ->with(['admin:id,ho_ten,email', 'nhanVien:id,full_name,email'])
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Lấy lịch sử thanh toán thành công',
                'data' => $lichSu->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'so_tien_thanh_toan' => $item->so_tien_thanh_toan,
                        'hinh_thuc_thanh_toan' => $item->hinh_thuc_thanh_toan,
                        'hinh_thuc_thanh_toan_label' => $item->hinh_thuc_thanh_toan_label,
                        'tien_mat' => $item->tien_mat,
                        'tien_chuyen_khoan' => $item->tien_chuyen_khoan,
                        'ghi_chu' => $item->ghi_chu,
                        'so_tien_da_tra_truoc_do' => $item->so_tien_da_tra_truoc_do,
                        'so_tien_con_no_sau_thanh_toan' => $item->so_tien_con_no_sau_thanh_toan,
                        'ngay_thanh_toan' => $item->ngay_thanh_toan,
                        'nguoi_thanh_toan' => $item->nguoi_thanh_toan_info,
                        'created_at' => $item->created_at,
                    ];
                })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi lấy lịch sử thanh toán',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thanh toán thêm cho phiếu chi
     */
    public function thanhToanThem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'so_tien_thanh_toan' => 'required|numeric|min:0',
            'hinh_thuc_thanh_toan' => 'required|in:tien_mat,chuyen_khoan,ca_hai',
            'tien_mat' => 'nullable|numeric|min:0',
            'tien_chuyen_khoan' => 'nullable|numeric|min:0',
            'ghi_chu' => 'nullable|string',
        ], [
            'so_tien_thanh_toan.required' => 'Vui lòng nhập số tiền thanh toán',
            'so_tien_thanh_toan.numeric' => 'Số tiền phải là số',
            'so_tien_thanh_toan.min' => 'Số tiền phải lớn hơn 0',
            'hinh_thuc_thanh_toan.required' => 'Vui lòng chọn hình thức thanh toán',
            'hinh_thuc_thanh_toan.in' => 'Hình thức thanh toán không hợp lệ',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $phieuChi = PhieuChi::findOrFail($id);

            // Kiểm tra số tiền thanh toán không vượt quá số tiền còn nợ
            if ($request->so_tien_thanh_toan > $phieuChi->so_tien_con_no) {
                return response()->json([
                    'status' => false,
                    'message' => 'Số tiền thanh toán không được vượt quá số tiền còn nợ',
                ], 422);
            }

            // Lưu số tiền đã trả trước đó
            $soTienDaTraTruocDo = $phieuChi->so_tien_thanh_toan_ngay;

            // Cập nhật phiếu chi
            $phieuChi->so_tien_thanh_toan_ngay += $request->so_tien_thanh_toan;
            $phieuChi->so_tien_con_no = $phieuChi->tong_so_tien - $phieuChi->so_tien_thanh_toan_ngay;

            // Cập nhật trạng thái
            if ($phieuChi->so_tien_con_no <= 0) {
                $phieuChi->trang_thai = 'da_hoan_thanh';
            }

            // Cập nhật tiền mặt và chuyển khoản
            if ($request->hinh_thuc_thanh_toan === 'tien_mat') {
                $phieuChi->tien_mat += $request->so_tien_thanh_toan;
            } elseif ($request->hinh_thuc_thanh_toan === 'chuyen_khoan') {
                $phieuChi->tien_chuyen_khoan += $request->so_tien_thanh_toan;
            } else {
                $phieuChi->tien_mat += $request->tien_mat ?? 0;
                $phieuChi->tien_chuyen_khoan += $request->tien_chuyen_khoan ?? 0;
            }

            $phieuChi->save();

            // Tạo lịch sử thanh toán
            $lichSu = \App\Models\LichSuThanhToanPhieuChi::create([
                'phieu_chi_id' => $phieuChi->id,
                'so_tien_thanh_toan' => $request->so_tien_thanh_toan,
                'hinh_thuc_thanh_toan' => $request->hinh_thuc_thanh_toan,
                'tien_mat' => $request->hinh_thuc_thanh_toan === 'ca_hai' ? ($request->tien_mat ?? 0) : ($request->hinh_thuc_thanh_toan === 'tien_mat' ? $request->so_tien_thanh_toan : 0),
                'tien_chuyen_khoan' => $request->hinh_thuc_thanh_toan === 'ca_hai' ? ($request->tien_chuyen_khoan ?? 0) : ($request->hinh_thuc_thanh_toan === 'chuyen_khoan' ? $request->so_tien_thanh_toan : 0),
                'ghi_chu' => $request->ghi_chu ?? 'Thanh toán thêm',
                'so_tien_da_tra_truoc_do' => $soTienDaTraTruocDo,
                'so_tien_con_no_sau_thanh_toan' => $phieuChi->so_tien_con_no,
                'ngay_thanh_toan' => now(),
            ]);

            DB::commit();

            // Load relationships
            $phieuChi->load([
                'nhaCungCap:id,ten_nha_cung_cap,ma_nha_cung_cap',
                'nhanVien:id,full_name',
                'admin:id,ho_ten,email',
                'lichSuThanhToan.admin:id,ho_ten,email',
                'lichSuThanhToan.nhanVien:id,full_name,email',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Thanh toán thành công',
                'data' => [
                    'phieu_chi' => [
                        'id' => $phieuChi->id,
                        'ma_phieu_chi' => $phieuChi->ma_phieu_chi,
                        'tong_so_tien' => $phieuChi->tong_so_tien,
                        'so_tien_thanh_toan_ngay' => $phieuChi->so_tien_thanh_toan_ngay,
                        'so_tien_con_no' => $phieuChi->so_tien_con_no,
                        'trang_thai' => $phieuChi->trang_thai,
                        'trang_thai_label' => $phieuChi->trang_thai_label,
                    ],
                    'lich_su_thanh_toan' => [
                        'id' => $lichSu->id,
                        'so_tien_thanh_toan' => $lichSu->so_tien_thanh_toan,
                        'hinh_thuc_thanh_toan' => $lichSu->hinh_thuc_thanh_toan,
                        'hinh_thuc_thanh_toan_label' => $lichSu->hinh_thuc_thanh_toan_label,
                        'tien_mat' => $lichSu->tien_mat,
                        'tien_chuyen_khoan' => $lichSu->tien_chuyen_khoan,
                        'ghi_chu' => $lichSu->ghi_chu,
                        'so_tien_da_tra_truoc_do' => $lichSu->so_tien_da_tra_truoc_do,
                        'so_tien_con_no_sau_thanh_toan' => $lichSu->so_tien_con_no_sau_thanh_toan,
                        'ngay_thanh_toan' => $lichSu->ngay_thanh_toan,
                        'nguoi_thanh_toan' => $lichSu->nguoi_thanh_toan_info,
                    ],
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Có lỗi xảy ra khi thanh toán',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
