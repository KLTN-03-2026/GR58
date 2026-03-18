# Hướng Dẫn Xuất PDF Phiếu Nhập Kho

## 🎯 Tổng Quan

Tính năng xuất PDF phiếu nhập kho cho phép người dùng tải xuống phiếu nhập dưới dạng file PDF để in ấn hoặc lưu trữ.

## 🔧 Backend API

### Endpoint

```
GET /phieu-nhap-kho/{id}/pdf
```

### Middleware

- `EnsureAdmin` - Chỉ Admin mới có quyền xuất PDF

### Response

- **Content-Type**: `application/pdf`
- **Return**: File PDF để download
- **Filename**: `phieu-nhap-kho-{ma_phieu_nhap}.pdf`

### Backend Controller

```php
public function exportPdf(PhieuNhapKho $phieuNhapKho)
{
    // Load relationships
    $phieuNhapKho->load([
        'nhaCungCap',
        'nhanVien',
        'admin',
        'phieuChi',
        'chiTietPhieuNhapKhos.hangHoa'
    ]);

    // Tạo PDF
    $pdf = Pdf::loadView('pdf.phieu-nhap-kho', $data)
        ->setPaper('a4', 'portrait');

    return $pdf->download('phieu-nhap-kho-' . $phieuNhapKho->ma_phieu_nhap . '.pdf');
}
```

## 💻 Frontend Implementation

### 1. Utils Function (`utils/phieuNhapKho.js`)

```javascript
/**
 * Xuất PDF phiếu nhập kho
 * @param {number} id - ID của phiếu nhập kho
 * @returns {Promise<Blob>} File PDF
 */
export const exportPhieuNhapKhoPdf = async (id) => {
  try {
    const response = await api.get(`/phieu-nhap-kho/${id}/pdf`, {
      responseType: "blob", // Quan trọng: Để nhận file blob
    });
    return response.data;
  } catch (error) {
    console.error("Error exporting phieu nhap kho PDF:", error);
    throw error;
  }
};
```

**Lưu ý quan trọng:**

- `responseType: "blob"` là BẮT BUỘC để nhận file PDF dạng binary
- Không set này sẽ nhận về string bị corrupt

### 2. Component Integration

#### Import

```javascript
import { exportPhieuNhapKhoPdf } from "@/utils/phieuNhapKho";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
```

#### State

```javascript
const isPrinting = ref(false); // Trạng thái đang xuất PDF
```

#### Handle Print Function

```javascript
const handlePrint = async () => {
  if (!receiptData.value || !receiptData.value.id) {
    showErrorToast("Không tìm thấy thông tin phiếu nhập");
    return;
  }

  try {
    isPrinting.value = true;

    // Gọi API để lấy PDF Blob
    const pdfBlob = await exportPhieuNhapKhoPdf(receiptData.value.id);

    // Tạo URL từ blob
    const url = window.URL.createObjectURL(pdfBlob);

    // Tạo link ẩn để download
    const link = document.createElement("a");
    link.href = url;
    link.download = `phieu-nhap-kho-${receiptData.value.code}.pdf`;
    document.body.appendChild(link);
    link.click();

    // Cleanup
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);

    showSuccessToast("Đã tải xuống phiếu nhập kho");
    emit("print", receiptData.value);
  } catch (error) {
    console.error("Error printing receipt:", error);
    showErrorToast("Không thể xuất PDF phiếu nhập kho");
  } finally {
    isPrinting.value = false;
  }
};
```

### 3. UI Button

```vue
<button
  @click="handlePrint"
  class="bg-[#009689] rounded-lg h-9 px-3 py-2 flex items-center gap-2 hover:bg-[#007d72] transition-colors"
  :disabled="isPrinting || isLoading"
  :class="{
    'opacity-50 cursor-not-allowed': isPrinting || isLoading,
  }"
>
  <!-- Loading spinner khi đang xuất PDF -->
  <div
    v-if="isPrinting"
    class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"
  ></div>
  <img v-else :src="iconPrint" alt="" class="w-4 h-4" />
  <span class="font-nunito font-medium text-sm leading-5 text-white tracking-tight">
    {{ isPrinting ? "Đang xuất PDF..." : "In phiếu nhập" }}
  </span>
</button>
```

## 🎨 UI/UX Features

### Loading State

- **Spinner animation**: Hiển thị khi đang xuất PDF
- **Button disabled**: Không cho click nhiều lần
- **Text dynamic**: "In phiếu nhập" → "Đang xuất PDF..."

### User Feedback

- **Success toast**: "Đã tải xuống phiếu nhập kho"
- **Error toast**: "Không thể xuất PDF phiếu nhập kho"

## 🔄 Luồng Hoạt Động

```
1. User click "In phiếu nhập"
   ↓
2. isPrinting = true
   ↓
3. Gọi API: exportPhieuNhapKhoPdf(id)
   ↓
4. Backend tạo PDF và trả về Blob
   ↓
5. Frontend tạo URL từ Blob
   ↓
6. Tự động download file PDF
   ↓
7. Cleanup URL và DOM
   ↓
8. Hiển thị toast success
   ↓
9. isPrinting = false
```

## 📦 File Download Process

### 1. Nhận Blob từ API

```javascript
const pdfBlob = await exportPhieuNhapKhoPdf(receiptData.value.id);
```

### 2. Tạo Object URL

```javascript
const url = window.URL.createObjectURL(pdfBlob);
// url sẽ có dạng: blob:http://localhost:5173/abc-123-def
```

### 3. Tạo Link Download

```javascript
const link = document.createElement("a");
link.href = url;
link.download = `phieu-nhap-kho-${receiptData.value.code}.pdf`;
```

### 4. Trigger Download

```javascript
document.body.appendChild(link);
link.click();
```

### 5. Cleanup (Quan trọng!)

```javascript
document.body.removeChild(link);
window.URL.revokeObjectURL(url); // Giải phóng memory
```

## 🛡️ Error Handling

### Các trường hợp lỗi

#### 1. Không có receipt ID

```javascript
if (!receiptData.value || !receiptData.value.id) {
  showErrorToast("Không tìm thấy thông tin phiếu nhập");
  return;
}
```

#### 2. API Error

- Network error
- Server error 500
- Permission denied 403
- Not found 404

#### 3. Blob Creation Error

- Browser không hỗ trợ
- Memory không đủ

### Try-Catch Block

```javascript
try {
  // Export logic
} catch (error) {
  console.error("Error printing receipt:", error);
  showErrorToast("Không thể xuất PDF phiếu nhập kho");
} finally {
  isPrinting.value = false; // Luôn reset state
}
```

## 🎯 Best Practices

### 1. Memory Management

✅ **PHẢI** revoke object URL sau khi dùng xong

```javascript
window.URL.revokeObjectURL(url);
```

❌ **KHÔNG** để object URL tồn tại vô thời hạn → Memory leak

### 2. State Management

✅ **PHẢI** reset `isPrinting` trong `finally` block

```javascript
finally {
  isPrinting.value = false;
}
```

### 3. File Naming

✅ **NÊN** dùng tên file có ý nghĩa

```javascript
link.download = `phieu-nhap-kho-${receiptData.value.code}.pdf`;
// VD: phieu-nhap-kho-PN20241205123456.pdf
```

### 4. User Feedback

✅ **PHẢI** có loading indicator
✅ **PHẢI** có success/error message
✅ **PHẢI** disable button khi đang xử lý

## 🧪 Testing Checklist

- [ ] Click button "In phiếu nhập"
- [ ] Loading spinner hiển thị
- [ ] Button disabled khi đang xuất
- [ ] PDF download tự động
- [ ] Tên file PDF đúng format
- [ ] Success toast hiển thị
- [ ] Button enabled lại sau khi xong
- [ ] Test với phiếu nhập có nhiều items
- [ ] Test với phiếu nhập có ít items
- [ ] Test error khi network offline
- [ ] Test error khi ID không tồn tại
- [ ] Memory không leak sau nhiều lần export

## 🐛 Troubleshooting

### PDF không download

**Nguyên nhân:**

- `responseType: "blob"` không được set
- Browser block popup/download

**Giải pháp:**

- Check axios config có `responseType: "blob"`
- Cho phép download trong browser settings

### File PDF bị corrupt

**Nguyên nhân:**

- Backend trả về HTML error thay vì PDF
- `responseType` không đúng

**Giải pháp:**

- Check response trong Network tab
- Đảm bảo backend trả về binary PDF

### Memory leak

**Nguyên nhân:**

- Không revoke object URL
- Không remove link khỏi DOM

**Giải pháp:**

```javascript
// Luôn cleanup
document.body.removeChild(link);
window.URL.revokeObjectURL(url);
```

## 📱 Browser Support

### Supported

✅ Chrome/Edge 89+
✅ Firefox 88+
✅ Safari 14.1+

### API Used

- `window.URL.createObjectURL()` - Supported all modern browsers
- `Blob` - Supported all modern browsers
- `document.createElement('a')` - Universal support

## 🔐 Security Considerations

### 1. Authorization

- Backend check quyền Admin
- Token authentication required

### 2. Data Validation

- Validate receipt ID
- Check receipt exists before export

### 3. Rate Limiting

- Backend nên có rate limit để tránh spam export

## 📊 Performance

### Tối ưu hóa

1. **Backend**: Cache PDF nếu cùng ID trong thời gian ngắn
2. **Frontend**: Disable button khi đang export
3. **Network**: Compress response nếu có thể

### Ước lượng thời gian

- Small receipt (<5 items): ~1-2 giây
- Medium receipt (5-20 items): ~2-3 giây
- Large receipt (>20 items): ~3-5 giây

## 🎓 Learning Points

### Blob vs Base64

**Blob** (sử dụng):

- ✅ Hiệu quả hơn với file lớn
- ✅ Ít tốn memory
- ✅ Native browser support

**Base64**:

- ❌ File size lớn hơn ~33%
- ❌ Tốn memory khi encode/decode
- ✅ Có thể embed trong HTML/JSON

### Object URL

```javascript
// Tạo temporary URL từ Blob
const url = window.URL.createObjectURL(blob);
// url: blob:http://localhost:5173/abc-123

// PHẢI revoke khi không dùng nữa
window.URL.revokeObjectURL(url);
```

---

**Cập nhật lần cuối**: 05/12/2024
**Version**: 1.0.0
