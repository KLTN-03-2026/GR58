<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BaiViet;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BaiVietSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Xóa dữ liệu cũ và reset auto increment
        DB::table('bai_viets')->truncate();

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $articles = [
            [
                'ten_bai_viet' => 'Cách chăm sóc thú cưng trong mùa hè',
                'slug' => 'cach-cham-soc-thu-cung-trong-mua-he',
                'noi_dung' => 'Mùa hè là thời kì khó khăn cho thú cưng của bạn. Dưới đây là những lời khuyên hữu ích để giúp thú cưng của bạn thoải mái hơn trong mùa nóng: 1. Cung cấp nước sạch đủ lượng. 2. Tìm chỗ mát mẻ cho thú cưng. 3. Không để thú cưng dưới ánh nắng trực tiếp quá lâu. 4. Tắm cho thú cưng thường xuyên. 5. Cắt lông cho thú cưng để giảm nhiệt độ cơ thể.',
                'mo_ta' => 'Những lời khuyên cơ bản để chăm sóc thú cưng trong mùa hè nóng bức.',
                'anh_bai_viet' => '/images/thu-cung-mua-he.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 1,
                'phan_loai_bai_viet_id' => 1,
            ],
            [
                'ten_bai_viet' => 'Các dấu hiệu sức khỏe cần chú ý ở chó',
                'slug' => 'cac-dau-hieu-suc-khoe-can-chu-y-o-cho',
                'noi_dung' => 'Để bảo vệ sức khỏe của thú cưng, bạn cần biết những dấu hiệu cảnh báo. Các dấu hiệu như: tiêu chảy, nôn, chán ăn, mệt mỏi, hay thay đổi hành vi có thể là dấu hiệu của bệnh. Hãy đưa thú cưng đến bệnh viện thú y ngay lập tức nếu bạn nhận thấy những dấu hiệu này.',
                'mo_ta' => 'Nhận biết những dấu hiệu sức khỏe quan trọng ở chó để phát hiện bệnh sớm.',
                'anh_bai_viet' => '/images/dau-hieu-benh-cho.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 2,
                'phan_loai_bai_viet_id' => 1,
            ],
            [
                'ten_bai_viet' => 'Hướng dẫn tiêm vắc xin cho mèo',
                'slug' => 'huong-dan-tiem-vac-xin-cho-meo',
                'noi_dung' => 'Tiêm vắc xin là một phần quan trọng trong việc chăm sóc mèo. Mèo cần được tiêm vắc xin thường xuyên để phòng ngừa các bệnh nguy hiểm. Lịch tiêm vắc xin cho mèo thường bắt đầu từ tuần thứ 6-8 tuổi. Hãy tham khảo ý kiến bác sĩ thú y để có lịch tiêm phù hợp.',
                'mo_ta' => 'Hướng dẫn chi tiết về lịch tiêm vắc xin cho mèo để đảm bảo sức khỏe.',
                'anh_bai_viet' => '/images/tiem-vac-xin-meo.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 1,
                'phan_loai_bai_viet_id' => 2,
            ],
            [
                'ten_bai_viet' => 'Dinh dưỡng tối ưu cho chó con',
                'slug' => 'dinh-duong-toi-uu-cho-cho-con',
                'noi_dung' => 'Chó con cần có chế độ dinh dưỡng đặc biệt để phát triển khỏe mạnh. Thức ăn cho chó con phải chứa đủ protein, chất béo, vitamin và khoáng chất. Hãy lựa chọn thức ăn chuyên biệt dành cho chó con có tuổi thích hợp. Ngoài ra, cần cho chó con ăn theo lịch trình đều đặn mỗi ngày.',
                'mo_ta' => 'Những kiến thức cơ bản về dinh dưỡng cho chó con phát triển toàn diện.',
                'anh_bai_viet' => '/images/dinh-duong-cho-con.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 2,
                'phan_loai_bai_viet_id' => 1,
            ],
            [
                'ten_bai_viet' => 'Cách chọn thức ăn cho thú cưng đúng cách',
                'slug' => 'cach-chon-thuc-an-cho-thu-cung-dung-cach',
                'noi_dung' => 'Lựa chọn thức ăn phù hợp là chìa khóa để duy trì sức khỏe của thú cưng. Bạn cần xem xét tuổi, kích thước, tình trạng sức khỏe của thú cưng. Thành phần thức ăn cũng rất quan trọng - hãy chọn thức ăn chứa đủ protein, chất béo và các chất dinh dưỡng khác. Tránh những thức ăn có chứa các chất gây hại.',
                'mo_ta' => 'Hướng dẫn chi tiết cách chọn thức ăn tốt nhất cho thú cưng của bạn.',
                'anh_bai_viet' => '/images/chon-thuc-an.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 1,
                'phan_loai_bai_viet_id' => 3,
            ],
            [
                'ten_bai_viet' => 'Huấn luyện thú cưng: Những điều cơ bản',
                'slug' => 'huan-luyen-thu-cung-nhung-dieu-co-ban',
                'noi_dung' => 'Huấn luyện thú cưng là một quá trình dài và cần kiên nhẫn. Bắt đầu từ những lệnh đơn giản như "nằm", "đứng", "đi". Sử dụng phần thưởng để khuyến khích thú cưng tuân theo lệnh. Huấn luyện thường xuyên mỗi ngày để đạt kết quả tốt nhất. Hãy chọn thời gian phù hợp khi thú cưng tỉnh táo và tỏ ra hứng thú.',
                'mo_ta' => 'Những nguyên tắc cơ bản khi huấn luyện thú cưng của bạn.',
                'anh_bai_viet' => '/images/huan-luyen-thu-cung.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 2,
                'phan_loai_bai_viet_id' => 2,
            ],
            [
                'ten_bai_viet' => 'Phòng ngừa và điều trị ve trong cho thú cưng',
                'slug' => 'phong-ngua-va-dieu-tri-ve-trong-cho-thu-cung',
                'noi_dung' => 'Ve trong là một vấn đề phổ biến ở thú cưng. Để phòng ngừa, hãy sử dụng những sản phẩm chuyên dụng như xịt, bột hoặc viên uống. Kiểm tra thú cưng thường xuyên để phát hiện ve sớm. Nếu phát hiện ve, sử dụng những loại thuốc khử trùng phù hợp và vệ sinh môi trường sống của thú cưng.',
                'mo_ta' => 'Hướng dẫn phòng ngừa và điều trị ve cho thú cưng hiệu quả.',
                'anh_bai_viet' => '/images/ve-trong-thu-cung.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 1,
                'phan_loai_bai_viet_id' => 1,
            ],
            [
                'ten_bai_viet' => 'Chăm sóc dữ đặt cho các loài chim cảnh',
                'slug' => 'cham-soc-dau-tiet-cho-cac-loai-chim-canh',
                'noi_dung' => 'Các loài chim cảnh cần được chăm sóc đặc biệt. Lồng chim phải đủ lớn và thoáng khí. Cho chim ăn hạt chim chuyên dụng kết hợp với rau xanh và hoa quả. Cung cấp nước sạch hàng ngày. Vệ sinh lồng chim thường xuyên để tránh bệnh. Cho chim tắm nắng một thời gian mỗi ngày để duy trì sức khỏe.',
                'mo_ta' => 'Những kiến thức chi tiết về chăm sóc các loài chim cảnh trong nhà.',
                'anh_bai_viet' => '/images/chim-canh.jpg',
                'trang_thai' => 'hidden',
                'nhan_vien_id' => 2,
                'phan_loai_bai_viet_id' => 3,
            ],
            [
                'ten_bai_viet' => 'Sơ cứu thú cưng: Những kỹ năng cần thiết',
                'slug' => 'so-cuu-thu-cung-nhung-ky-nang-can-thiet',
                'noi_dung' => 'Mỗi chủ thú cưng nên biết những kỹ năng sơ cứu cơ bản. Biết cách cấp cứu khi thú cưng bị sốc, chảy máu hoặc bị chấn thương. Chuẩn bị một hộp sơ cứu với các dụng cụ cần thiết. Giữ số điện thoại bác sĩ thú y gần tay. Trong trường hợp khẩn cấp, hãy gọi ngay bác sĩ thú y.',
                'mo_ta' => 'Những kỹ năng sơ cứu quan trọng để xử lý tình huống khẩn cấp với thú cưng.',
                'anh_bai_viet' => '/images/so-cuu-thu-cung.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 1,
                'phan_loai_bai_viet_id' => 2,
            ],
            [
                'ten_bai_viet' => 'Cách giữ vệ sinh cho thú cưng của bạn',
                'slug' => 'cach-giu-ve-sinh-cho-thu-cung-cua-ban',
                'noi_dung' => 'Vệ sinh là rất quan trọng để duy trì sức khỏe của thú cưng. Tắm cho thú cưng thường xuyên với nước ấm và xà phòng chuyên dụng. Cắt móng cho thú cưng mỗi tháng một lần. Vệ sinh tai và mắt thường xuyên. Đánh răng cho thú cưng ít nhất 2-3 lần một tuần. Làm sạch vùng hậu môn thường xuyên để tránh bệnh.',
                'mo_ta' => 'Hướng dẫn giữ vệ sinh cơ bản cho thú cưng để chúng luôn khỏe mạnh.',
                'anh_bai_viet' => '/images/ve-sinh-thu-cung.jpg',
                'trang_thai' => 'published',
                'nhan_vien_id' => 2,
                'phan_loai_bai_viet_id' => 1,
            ],
        ];

        foreach ($articles as $article) {
            BaiViet::create($article);
        }
    }
}
