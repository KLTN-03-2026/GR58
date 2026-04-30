<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\KhachHang;
use App\Models\ThuCung;
use App\Models\NhanVien;
use App\Models\DichVu;
use Carbon\Carbon;

class LichHenSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lich_hens')->truncate();
        DB::table('phieu_khams')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Lấy IDs thực từ DB
        $khachHangs = KhachHang::limit(5)->pluck('id')->toArray();
        $nhanViens  = NhanVien::where('vai_tro', 'bac_si')->pluck('id')->toArray();
        if (empty($nhanViens)) {
            $nhanViens = NhanVien::limit(5)->pluck('id')->toArray();
        }
        $dichVuId = DichVu::first()?->id;

        if (empty($khachHangs)) {
            $this->command->warn('⚠️ Chưa có khách hàng. Hãy chạy KhachHangSeeder trước.');
            return;
        }
        if (empty($nhanViens)) {
            $this->command->warn('⚠️ Chưa có nhân viên. Hãy chạy NhanVienSeeder trước.');
            return;
        }

        // Lấy / tạo thu_cungs kèm khach_hang_id
        $thuCungs = ThuCung::whereNotNull('khach_hang_id')->limit(8)->get();

        // Nếu thu_cungs chưa có khach_hang_id → gán tự động
        if ($thuCungs->isEmpty()) {
            $allThuCungs = ThuCung::limit(8)->get();
            foreach ($allThuCungs as $i => $tc) {
                $tc->khach_hang_id = $khachHangs[$i % count($khachHangs)];
                $tc->save();
            }
            $thuCungs = ThuCung::whereNotNull('khach_hang_id')->limit(8)->get();
        }

        if ($thuCungs->isEmpty()) {
            $this->command->warn('⚠️ Không có thú cưng trong DB.');
            return;
        }

        $lichHenInserts  = [];
        $phieuKhamInserts = [];

        foreach ($thuCungs as $i => $pet) {
            $kh        = $pet->khach_hang_id;
            $nhanVien  = $nhanViens[$i % count($nhanViens)];
            $ngayKham1 = Carbon::now()->subDays(rand(5, 60));
            $ngayKham2 = Carbon::now()->subDays(rand(1, 4));

            // Lấy giá dịch vụ
            $dichVu = DichVu::find($dichVuId);
            $giaCoSo = $dichVu && $dichVu->gia ? $dichVu->gia : 200000;
            $giaDichVu1 = $giaCoSo + rand(-50000, 50000);
            $giaDichVu2 = $giaCoSo + rand(-30000, 100000);

            // Lịch hẹn 1 (cũ hơn) - Đã hoàn thành và đã thanh toán
            $lichHenInserts[] = [
                'ngay_gio'               => $ngayKham1,
                'trang_thai'             => 'hoan_thanh',
                'khach_hang_id'          => $kh,
                'thu_cung_id'            => $pet->id,
                'nhan_vien_id'           => $nhanVien,
                'dich_vu_id'             => $dichVuId,
                'thoi_gian_hoan_thanh'   => $ngayKham1->copy()->addHour(),
                'tong_tien'              => $giaDichVu1,
                'da_thanh_toan'          => true,
                'phuong_thuc_thanh_toan' => 'cash',
                'thoi_gian_thanh_toan'   => $ngayKham1->copy()->addHours(2),
                'ghi_chu'                => null,
                'nguon_goc'              => 'online',
                'created_at'             => $ngayKham1,
                'updated_at'             => $ngayKham1,
            ];

            // Lịch hẹn 2 (gần đây) - Đã xác nhận, đã thanh toán trước
            $lichHenInserts[] = [
                'ngay_gio'               => $ngayKham2,
                'trang_thai'             => 'da_xac_nhan',
                'khach_hang_id'          => $kh,
                'thu_cung_id'            => $pet->id,
                'nhan_vien_id'           => $nhanVien,
                'dich_vu_id'             => $dichVuId,
                'thoi_gian_hoan_thanh'   => null,
                'tong_tien'              => $giaDichVu2,
                'da_thanh_toan'          => true,
                'phuong_thuc_thanh_toan' => 'momo',
                'thoi_gian_thanh_toan'   => $ngayKham2->copy()->subHours(2),
                'ghi_chu'                => null,
                'nguon_goc'              => 'online',
                'created_at'             => $ngayKham2,
                'updated_at'             => $ngayKham2,
            ];
        }

        // Thêm một vài lịch hẹn chưa thanh toán cho khách hàng đầu tiên
        if (!empty($khachHangs) && !empty($thuCungs)) {
            $firstCustomer = $khachHangs[0];
            $firstPet = $thuCungs->first();
            $dichVu = DichVu::find($dichVuId);
            $giaDichVu = $dichVu && $dichVu->gia ? $dichVu->gia : 250000;

            // Lịch hẹn chờ xác nhận - chưa thanh toán
            $lichHenInserts[] = [
                'ngay_gio'               => Carbon::now()->addDays(3),
                'trang_thai'             => 'cho_xac_nhan',
                'khach_hang_id'          => $firstCustomer,
                'thu_cung_id'            => $firstPet->id,
                'nhan_vien_id'           => $nhanViens[0],
                'dich_vu_id'             => $dichVuId,
                'thoi_gian_hoan_thanh'   => null,
                'tong_tien'              => $giaDichVu,
                'da_thanh_toan'          => false,
                'phuong_thuc_thanh_toan' => null,
                'thoi_gian_thanh_toan'   => null,
                'ghi_chu'                => 'Cần thanh toán trước khi khám',
                'nguon_goc'              => 'online',
                'created_at'             => Carbon::now(),
                'updated_at'             => Carbon::now(),
            ];

            // Thêm thêm lịch hẹn cho tất cả khách hàng để đảm bảo mọi người đều có dữ liệu
            foreach ($khachHangs as $khId) {
                $petForKh = ThuCung::where('khach_hang_id', $khId)->first();
                if (!$petForKh) {
                    // Tạo thú cưng mới nếu khách hàng chưa có
                    $petForKh = ThuCung::create([
                        'ten_thu_cung' => 'Pet_' . $khId,
                        'loai_thu_cung' => 'cho',
                        'giong_thu_cung' => 'Mixed',
                        'tuoi_thu_cung' => Carbon::now()->subYears(rand(1, 10))->format('Y-m-d'),
                        'can_nang' => rand(5, 30),
                        'gioi_tinh' => rand(0, 1) ? 'duc' : 'cai',
                        'khach_hang_id' => $khId,
                    ]);
                }

                // Tạo 2 lịch hẹn cho mỗi khách hàng
                $lichHenInserts[] = [
                    'ngay_gio'               => Carbon::now()->subDays(rand(10, 30)),
                    'trang_thai'             => 'hoan_thanh',
                    'khach_hang_id'          => $khId,
                    'thu_cung_id'            => $petForKh->id,
                    'nhan_vien_id'           => $nhanViens[0],
                    'dich_vu_id'             => $dichVuId,
                    'thoi_gian_hoan_thanh'   => Carbon::now()->subDays(rand(10, 30))->addHour(),
                    'tong_tien'              => rand(150000, 400000),
                    'da_thanh_toan'          => true,
                    'phuong_thuc_thanh_toan' => 'cash',
                    'thoi_gian_thanh_toan'   => Carbon::now()->subDays(rand(10, 30))->addHours(2),
                    'ghi_chu'                => null,
                    'nguon_goc'              => 'online',
                    'created_at'             => Carbon::now()->subDays(rand(10, 30)),
                    'updated_at'             => Carbon::now()->subDays(rand(10, 30)),
                ];

                $lichHenInserts[] = [
                    'ngay_gio'               => Carbon::now()->addDays(rand(1, 5)),
                    'trang_thai'             => 'cho_xac_nhan',
                    'khach_hang_id'          => $khId,
                    'thu_cung_id'            => $petForKh->id,
                    'nhan_vien_id'           => $nhanViens[0],
                    'dich_vu_id'             => $dichVuId,
                    'thoi_gian_hoan_thanh'   => null,
                    'tong_tien'              => rand(200000, 500000),
                    'da_thanh_toan'          => false,
                    'phuong_thuc_thanh_toan' => null,
                    'thoi_gian_thanh_toan'   => null,
                    'ghi_chu'                => 'Cần thanh toán',
                    'nguon_goc'              => 'online',
                    'created_at'             => Carbon::now(),
                    'updated_at'             => Carbon::now(),
                ];
            }
        }

        // Insert lich_hens
        DB::table('lich_hens')->insert($lichHenInserts);

        // Lấy lại IDs vừa insert
        $lichHenIds = DB::table('lich_hens')->orderBy('id')->pluck('id')->toArray();

        // Dữ liệu mẫu phiếu khám
        $danhSachKham = [
            ['ly_do' => 'Khám tổng quát',       'trieu_chung' => 'Không có triệu chứng',        'chan_doan' => 'Sức khỏe bình thường',    'loai' => 'hen_tai_kham'],
            ['ly_do' => 'Tiêm phòng định kỳ',   'trieu_chung' => 'Không có',                    'chan_doan' => 'Đã tiêm phòng đầy đủ',   'loai' => 'hen_tai_kham'],
            ['ly_do' => 'Nôn mửa, bỏ ăn',       'trieu_chung' => 'Nôn 3-4 lần, bỏ ăn 1 ngày', 'chan_doan' => 'Viêm dạ dày',            'loai' => 'don_thuoc'],
            ['ly_do' => 'Ngứa da, rụng lông',   'trieu_chung' => 'Gãi nhiều, lông thưa',       'chan_doan' => 'Viêm da dị ứng',         'loai' => 'don_thuoc'],
            ['ly_do' => 'Tiêu chảy',             'trieu_chung' => 'Đi tiêu nhiều, phân lỏng',  'chan_doan' => 'Rối loạn tiêu hóa',      'loai' => 'don_thuoc'],
            ['ly_do' => 'Khám mắt',              'trieu_chung' => 'Chảy nước mắt nhiều',        'chan_doan' => 'Viêm kết mạc nhẹ',      'loai' => 'chi_dinh_can_lam_sang'],
            ['ly_do' => 'Ho kéo dài',            'trieu_chung' => 'Ho khan, hắt hơi',           'chan_doan' => 'Viêm đường hô hấp',      'loai' => 'chi_dinh_can_lam_sang'],
            ['ly_do' => 'Tẩy giun định kỳ',     'trieu_chung' => 'Không có',                    'chan_doan' => 'Đã tẩy giun',            'loai' => 'don_thuoc'],
            ['ly_do' => 'Khám hậu môn',          'trieu_chung' => 'Chạy vòng tròn, cắn đuôi',  'chan_doan' => 'Viêm tuyến hậu môn',    'loai' => 'chi_dinh_can_lam_sang'],
            ['ly_do' => 'Kiểm tra sau phẫu thuật', 'trieu_chung' => 'Vết mổ lành tốt',         'chan_doan' => 'Hồi phục tốt',           'loai' => 'hen_tai_kham'],
            ['ly_do' => 'Chích ngừa',            'trieu_chung' => 'Không có',                    'chan_doan' => 'Hoàn thành mũi nhắc lại', 'loai' => 'hen_tai_kham'],
            ['ly_do' => 'Đau khớp',              'trieu_chung' => 'Đi khập khiễng, đau khi chạm', 'chan_doan' => 'Viêm khớp nhẹ',       'loai' => 'don_thuoc'],
            ['ly_do' => 'Ăn không ngon',         'trieu_chung' => 'Giảm cân, lờ đờ',            'chan_doan' => 'Thiếu dinh dưỡng',       'loai' => 'don_thuoc'],
            ['ly_do' => 'Kiểm tra nha khoa',     'trieu_chung' => 'Chảy nước dãi nhiều',        'chan_doan' => 'Cao răng, cần vệ sinh',  'loai' => 'chi_dinh_can_lam_sang'],
            ['ly_do' => 'Sốt cao',               'trieu_chung' => 'Mệt mỏi, lười di chuyển',   'chan_doan' => 'Nhiễm trùng nhẹ',        'loai' => 'chi_dinh_can_lam_sang'],
            ['ly_do' => 'Khám định kỳ',          'trieu_chung' => 'Không có',                    'chan_doan' => 'Sức khỏe ổn định',       'loai' => 'hen_tai_kham'],
        ];

        $vitals = [
            [38.5, 4.5,  98,  18],
            [38.2, 3.2,  110, 20],
            [39.1, 28.0, 88,  24],
            [38.8, 2.8,  120, 28],
            [38.0, 9.0,  95,  16],
            [37.9, 2.0,  105, 22],
            [38.3, 4.0,  100, 19],
            [38.6, 22.0, 92,  17],
            [38.1, 3.5,  115, 21],
            [37.8, 11.0, 88,  16],
        ];

        foreach ($lichHenIds as $idx => $lichHenId) {
            $khamData      = $danhSachKham[$idx % count($danhSachKham)];
            $vital         = $vitals[$idx % count($vitals)];
            $lichHen       = DB::table('lich_hens')->where('id', $lichHenId)->first();
            $createdAt     = Carbon::parse($lichHen->ngay_gio)->addHour();

            $phieuKhamInserts[] = [
                'lich_hen_id'    => $lichHenId,
                'nhan_vien_id'   => $lichHen->nhan_vien_id,
                'nhiet_do'       => $vital[0],
                'can_nang'       => $vital[1],
                'nhip_tim'       => $vital[2],
                'nhip_tho'       => $vital[3],
                'ly_do_den_kham' => $khamData['ly_do'],
                'trieu_chung'    => $khamData['trieu_chung'],
                'chan_doan'      => $khamData['chan_doan'],
                'loai_chi_dinh'  => $khamData['loai'],
                'ghi_chu'        => null,
                'created_at'     => $createdAt,
                'updated_at'     => $createdAt,
            ];
        }

        DB::table('phieu_khams')->insert($phieuKhamInserts);

        $this->command->info('✓ Đã seed ' . count($lichHenInserts) . ' lịch hẹn và ' . count($phieuKhamInserts) . ' phiếu khám!');
    }
}
