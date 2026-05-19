# Chatbot AI Test Cases

## 1. Chatbot hiện đang làm được gì

Dựa trên code hiện tại, chatbot đang có các khả năng sau:

- Hiển thị widget chat ở các route public/customer như `/`, `/customer/*`, `/services/*`, `/forum/*`.
- Ẩn widget ở các route đăng nhập và khu vực staff như `/customer/login`, `/customer/register`, `/admin/*`, `/doctor/*`, `/nurse/*`, `/staff/*`.
- Nhận tin nhắn văn bản.
- Nhận tối đa 4 ảnh theo request.
- Trả lời các câu hỏi về chăm sóc thú cưng, hành vi, dinh dưỡng, vệ sinh, môi trường sống.
- Từ chối lịch sự các câu hỏi không liên quan đến thú cưng.
- Với câu hỏi y khoa, bot chỉ tư vấn tham khảo và phải nhắc đi bác sĩ thú y khi có dấu hiệu nghiêm trọng.
- Nếu khách hàng chưa đăng nhập: chỉ tư vấn chung, không truy xuất dữ liệu cá nhân.
- Nếu khách hàng đã đăng nhập và là `KhachHang`: bot có thể dùng tool để:
  - Lấy danh sách thú cưng của khách hàng.
  - Lấy 3 lần khám gần nhất của một thú cưng.
  - Lấy lịch hẹn sắp tới.
  - Kiểm tra khung giờ còn trống theo ngày.
  - Lấy danh sách dịch vụ đang kinh doanh.
  - Đặt lịch hẹn và trả về action card "Đặt lịch thành công".
- Bot gửi kèm tối đa 10 tin lịch sử hội thoại gần nhất lên backend để giữ ngữ cảnh.

## 2. Điều kiện dữ liệu nên chuẩn bị trước khi test

Để test đủ luồng, nên có sẵn các dữ liệu sau:

- `Guest`: người dùng chưa đăng nhập.
- `Customer A`: đã đăng nhập, có ít nhất 2 thú cưng.
- Một thú cưng của `Customer A` đã có ít nhất 3 phiếu khám.
- `Customer A` có ít nhất 1 lịch hẹn sắp tới.
- `Customer B`: đã đăng nhập nhưng chưa có thú cưng.
- Ít nhất 2 dịch vụ đang ở trạng thái kinh doanh.
- 1 ngày tương lai có bác sĩ làm `ca_sang`.
- 1 ngày tương lai không có lịch bác sĩ.
- 1 khung giờ đã full chỗ.
- 1 thú cưng đã có lịch `confirmed` hoặc `in-progress` để test trùng giờ.

## 3. Test cases thủ công

| ID | Mục tiêu | Tiền điều kiện | Bước test | Kết quả mong đợi |
|---|---|---|---|---|
| CBT-01 | Widget hiển thị ở trang public | Mở trang chủ | Vào `/` | Thấy nút nổi mở chatbot |
| CBT-02 | Widget hiển thị ở khu vực customer/forum/services | Không | Vào lần lượt `/customer/...`, `/services/...`, `/forum/...` | Widget xuất hiện |
| CBT-03 | Widget bị ẩn ở khu vực staff/login | Không | Vào `/customer/login`, `/customer/register`, `/admin/...`, `/doctor/...`, `/nurse/...`, `/staff/...` | Widget không xuất hiện |
| CBT-04 | Mở và đóng chatbot | Ở trang có widget | Nhấn FAB để mở, nhấn nút đóng | Panel mở ra rồi đóng lại bình thường |
| CBT-05 | Có lời chào mặc định | Mở chatbot | Quan sát tin nhắn đầu tiên | Có lời chào của Petty AI và nội dung mời người dùng hỏi về thú cưng |
| CBT-06 | Gửi câu hỏi chăm sóc thú cưng cơ bản | Guest hoặc customer | Hỏi: `Chó con 2 tháng tuổi nên ăn gì?` | Bot trả lời đúng chủ đề thú cưng, dễ hiểu, có gợi ý hành động |
| CBT-07 | Từ chối câu hỏi ngoài phạm vi | Guest hoặc customer | Hỏi: `Viết giúp tôi email xin nghỉ phép` | Bot từ chối lịch sự và kéo người dùng về chủ đề thú cưng |
| CBT-08 | Có cảnh báo an toàn với triệu chứng nặng | Guest hoặc customer | Hỏi: `Mèo bị co giật và bỏ ăn, tôi nên làm gì?` | Bot không chẩn đoán chắc chắn, khuyên đưa đi bác sĩ thú y sớm/khẩn cấp, có câu nhắc AI chỉ tham khảo |
| CBT-09 | Gửi ảnh kèm câu hỏi | Guest hoặc customer | Đính kèm 1 ảnh thú cưng, hỏi `Da bé bị đỏ thế này có sao không?` | Ảnh preview hiển thị trước khi gửi, bot phản hồi dựa trên ảnh và nội dung hỏi |
| CBT-10 | Gửi chỉ ảnh, không có text | Guest hoặc customer | Chỉ upload ảnh rồi gửi | Request được gửi thành công, bot vẫn phản hồi |
| CBT-11 | Xóa ảnh trước khi gửi | Guest hoặc customer | Upload ảnh rồi bấm nút xóa trên preview | Ảnh biến mất khỏi preview, không còn được gửi đi |
| CBT-12 | Backend chặn quá 4 ảnh | Guest hoặc customer | Chọn 5 ảnh rồi gửi | FE hiển thị phản hồi lỗi chung `Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại sau.` do backend validate tối đa 4 ảnh |
| CBT-13 | Guest không truy xuất dữ liệu cá nhân | Chưa đăng nhập | Hỏi: `Cho tôi xem lịch hẹn sắp tới của tôi` | Bot không trả dữ liệu cá nhân thực tế, chỉ xin đăng nhập hoặc trả lời chung |
| CBT-14 | Customer xem danh sách thú cưng | Đăng nhập `Customer A` | Hỏi: `Tôi có những thú cưng nào?` | Bot trả đúng danh sách thú cưng thuộc tài khoản đang đăng nhập |
| CBT-15 | Customer hỏi thông tin thú cưng theo ngữ cảnh có sẵn | Đăng nhập `Customer A`, có pet tên cụ thể | Hỏi: `Bé Miu nhà tôi bao nhiêu tuổi?` | Bot trả đúng theo context của tài khoản |
| CBT-16 | Customer xem lịch sử khám thú cưng | Đăng nhập `Customer A`, pet có lịch sử khám | Hỏi: `Cho tôi xem lịch sử khám gần đây của bé Miu` | Bot trả về tối đa 3 lần khám gần nhất của đúng thú cưng |
| CBT-17 | Customer xem lịch hẹn sắp tới | Đăng nhập `Customer A`, có lịch tương lai | Hỏi: `Tôi có lịch hẹn nào sắp tới không?` | Bot trả đúng danh sách lịch hẹn chưa hủy và còn trong tương lai |
| CBT-18 | Customer xem dịch vụ hiện có | Đăng nhập `Customer A` | Hỏi: `Phòng khám hiện có những dịch vụ gì?` | Bot trả danh sách dịch vụ đang kinh doanh |
| CBT-19 | Customer hỏi giờ trống đặt lịch | Đăng nhập `Customer A`, có ngày bác sĩ làm việc | Hỏi: `Ngày 2026-05-25 còn giờ nào trống để khám?` | Bot trả danh sách khung giờ trống của ngày đó |
| CBT-20 | Ngày không có lịch bác sĩ | Đăng nhập `Customer A`, ngày test không có bác sĩ | Hỏi: `Ngày 2026-05-26 còn giờ nào trống?` | Bot báo phòng khám không có lịch làm việc cho ngày đó hoặc không có slot |
| CBT-21 | Không cho đặt lịch ngày quá khứ | Đăng nhập `Customer A` | Hỏi bot đặt lịch vào một ngày giờ đã qua | Bot từ chối và báo phải chọn thời gian tương lai |
| CBT-22 | Đặt lịch thành công | Đăng nhập `Customer A`, có pet hợp lệ, dịch vụ hợp lệ, slot còn chỗ | Hỏi kiểu tự nhiên: `Đặt lịch tiêm phòng cho bé Miu vào 09:00 ngày 2026-05-25` | Bot tự tìm dịch vụ phù hợp, tạo lịch thành công, hiển thị action card `Đặt lịch thành công` với thú cưng, dịch vụ, thời gian |
| CBT-23 | Sau khi đặt lịch, lịch mới xuất hiện trong truy vấn lịch hẹn | Vừa pass CBT-22 | Hỏi tiếp: `Cho tôi xem lịch hẹn sắp tới` | Lịch vừa tạo xuất hiện trong danh sách |
| CBT-24 | Không yêu cầu user nhập service ID khi đặt lịch | Đăng nhập `Customer A` | Hỏi: `Đặt lịch khám tổng quát cho bé Miu sáng mai` | Bot không hỏi `dich_vu_id`, mà tự suy ra bằng tool danh sách dịch vụ hoặc chỉ hỏi lại khi tên dịch vụ thật sự mơ hồ |
| CBT-25 | Chặn đặt lịch cho thú cưng không thuộc tài khoản | Đăng nhập `Customer A`, biết tên/id pet của tài khoản khác | Cố hỏi đặt lịch cho pet không thuộc tài khoản | Bot không tạo lịch, báo thú cưng không hợp lệ hoặc không thể thực hiện |
| CBT-26 | Chặn đặt lịch trùng giờ cho cùng thú cưng | Đăng nhập `Customer A`, pet đã có lịch `confirmed` hoặc `in-progress` cùng khung giờ | Hỏi bot đặt thêm lịch cùng giờ cho chính pet đó | Bot từ chối, báo thú cưng đã có lịch trong khung giờ này |
| CBT-27 | Chặn đặt lịch khi full slot | Đăng nhập `Customer A`, khung giờ đã đủ capacity | Hỏi bot đặt lịch đúng khung giờ đã full | Bot từ chối, báo khung giờ đã hết chỗ |
| CBT-28 | Customer không có thú cưng vẫn chat được | Đăng nhập `Customer B`, không có pet | Hỏi tư vấn chung hoặc hỏi dữ liệu cá nhân | Bot vẫn tư vấn chung được; với câu hỏi cần dữ liệu pet thì không có dữ liệu để trả đúng cá nhân hóa |
| CBT-29 | Lỗi cấu hình AI key | Môi trường test tạm bỏ `GROQ_API_KEY` | Gửi một tin nhắn bất kỳ | FE hiển thị thông báo lỗi chung; backend trả lỗi cấu hình |
| CBT-30 | Lịch sử hội thoại còn tác dụng trong nhiều lượt chat | Guest hoặc customer | Hỏi liên tiếp 3-5 câu có tham chiếu đại từ như `bé đó`, `triệu chứng trên`, `lịch vừa đặt` | Bot giữ được ngữ cảnh tương đối nhất quán |

## 4. Test case nên ưu tiên vì dễ lộ bug

### CBT-R1. Lệch slot `16:00`

Mục tiêu:
Phát hiện lệch logic giữa hàm xem slot trống và hàm đặt lịch.

Tiền điều kiện:

- Có 1 ngày tương lai chỉ có bác sĩ `ca_sang`.
- Không có `ca_chieu` ngày đó.

Bước test:

1. Hỏi chatbot: `Ngày YYYY-MM-DD còn giờ nào trống?`
2. Nếu bot trả về có `16:00`, tiếp tục yêu cầu đặt lịch đúng `16:00`.

Kết quả mong đợi:

- Hệ thống lý tưởng: nếu đã hiện `16:00` là phải đặt được.
- Với code hiện tại, rất có khả năng bot báo còn slot `16:00` nhưng đặt lịch lại thất bại.

Ý nghĩa:

- Đây là một bug/risk hợp lý vì logic capacity ở bước xem slot và bước đặt lịch đang không đồng nhất.

### CBT-R2. Sai field tên dịch vụ trong context lịch hẹn

Mục tiêu:
Kiểm tra bot có đọc đúng tên dịch vụ trong phần lịch hẹn sắp tới đã inject vào context hay không.

Tiền điều kiện:

- `Customer A` có lịch hẹn sắp tới với dịch vụ cụ thể, dễ nhận biết.

Bước test:

1. Đăng nhập `Customer A`.
2. Hỏi: `Lịch hẹn sắp tới của tôi là dịch vụ gì?`

Kết quả mong đợi:

- Bot nên đọc đúng tên dịch vụ.
- Nếu bot trả `Khám tổng quát` dù DB là dịch vụ khác, cần kiểm tra lại phần build context.

## 5. Ghi chú khi chạy test

- Widget FE chỉ lấy token bằng `getToken('customer')`, nên muốn test tool dữ liệu cá nhân thì phải đăng nhập đúng luồng khách hàng.
- FE hiện không hiển thị chi tiết lỗi backend, nên nhiều lỗi validation/runtime sẽ chỉ hiện một câu lỗi chung trên chat UI.
- Các test đặt lịch phụ thuộc mạnh vào dữ liệu lịch bác sĩ và trạng thái lịch hẹn hiện có.
