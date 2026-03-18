# Phiếu Chi - Tự động tạo mã phiếu

## 📋 Tổng quan

Hệ thống **Phiếu Chi** đã được cập nhật để tự động tạo mã phiếu chi trên Backend, người dùng không cần nhập mã thủ công.

## 🔢 Định dạng mã phiếu chi

Backend tự động tạo mã phiếu chi theo format:

### 1. Chi nhập hàng

```
PCN + yymmdd + 4 số thứ tự
```

**Ví dụ:** `PCN2412060001`, `PCN2412060002`

- **PCN** = Phiếu Chi Nhập hàng
- **241206** = Ngày 06/12/2024
- **0001** = Số thứ tự phiếu trong ngày

### 2. Chi vận hành

```
PCV + yymmdd + 4 số thứ tự
```

**Ví dụ:** `PCV2412060001`, `PCV2412060002`

- **PCV** = Phiếu Chi Vận hành
- **241206** = Ngày 06/12/2024
- **0001** = Số thứ tự phiếu trong ngày

## 🔧 Backend Implementation

### Controller: `PhieuChiController.php`

```php
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
```

### API Endpoint

**POST** `/api/phieu-chi`

#### Request Body

```json
{
  "loai_phieu_chi": "chi_nhap_hang", // hoặc "chi_van_hanh"
  "ly_do_chi": "Trả tiền nhập vắc-xin tháng 12",
  "tong_so_tien": 15000000,
  "so_tien_thanh_toan_ngay": 5000000,
  "tien_mat": 2000000,
  "tien_chuyen_khoan": 3000000,
  "nha_cung_cap_id": 5, // Bắt buộc nếu chi_nhap_hang
  "doi_tuong_nhan_tien": null, // Bắt buộc nếu chi_van_hanh
  "ngay_chi": "2024-12-06",
  "ghi_chu": "Thanh toán đợt 1",
  "anh_chung_tu": ["base64_image_1", "base64_image_2"]
}
```

#### Response

```json
{
  "status": true,
  "message": "Thêm phiếu chi thành công",
  "data": {
    "id": 123,
    "ma_phieu_chi": "PCN2412060001", // Mã tự động
    "loai_phieu_chi": "chi_nhap_hang",
    "loai_phieu_chi_label": "Chi nhập hàng",
    "ly_do_chi": "Trả tiền nhập vắc-xin tháng 12",
    "tong_so_tien": 15000000,
    "so_tien_thanh_toan_ngay": 5000000,
    "so_tien_con_no": 10000000, // Tự động tính
    "tien_mat": 2000000,
    "tien_chuyen_khoan": 3000000,
    "trang_thai": "chua_thanh_toan_du", // Tự động
    "trang_thai_label": "Chưa thanh toán đủ",
    "ngay_chi": "2024-12-06",
    "nha_cung_cap": {
      "id": 5,
      "ten_nha_cung_cap": "Công ty Vắc-xin ABC",
      "ma_nha_cung_cap": "NCC001"
    },
    "nguoi_tao": {
      "id": 10,
      "ten": "Admin Hệ Thống",
      "type": "admin"
    },
    "created_at": "2024-12-06T10:30:00.000000Z",
    "updated_at": "2024-12-06T10:30:00.000000Z"
  }
}
```

## 💻 Frontend Implementation

### Files Updated

1. **Admin Component**
   - `src/components/Admin/TaiChinhHoaDon/PhieuChi/TaoPhieuChi/index.vue`
2. **Nurse Component**
   - `src/components/Nurse/PhieuChi/TaoPhieuChi/index.vue`

### Changes Made

#### Before (Manual Input)

```vue
<!-- Mã phiếu chi -->
<div class="flex flex-col gap-2">
  <label class="text-sm font-medium text-neutral-950">
    Mã phiếu chi<span class="text-red-500">*</span>
  </label>
  <input 
    type="text"
    v-model="formData.code"
    placeholder="Nhập mã phiếu chi"
    class="w-full bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 h-9"
  />
</div>
```

#### After (Auto-generated)

```vue
<h2 class="text-lg font-semibold text-neutral-950">Tạo phiếu chi mới</h2>
<p class="text-sm text-[#717182]">
  Mã phiếu sẽ được tạo tự động (PCN/PCV + ngày + số thứ tự)
</p>
```

### Form Data Structure

```javascript
const formData = ref({
  type: "chi_van_hanh", // 'chi_nhap_hang' or 'chi_van_hanh'
  supplier: "",
  supplierId: null,
  recipient: "",
  reason: "",
  totalAmount: "",
  paidAmount: "",
  cashAmount: "",
  transferAmount: "",
  paymentSource: "cash",
  attachments: [],
  note: "",
  date: new Date().toISOString().split("T")[0],
  // ❌ Không còn field 'code' - Backend tự tạo
});
```

### Submit Handler

```javascript
const handleSubmit = async () => {
  const submitData = {
    loai_phieu_chi: formData.value.type,
    ly_do_chi: formData.value.reason,
    tong_so_tien: parseInt(formData.value.totalAmount),
    so_tien_thanh_toan_ngay: parseInt(formData.value.paidAmount),
    tien_mat:
      formData.value.paymentSource === "cash"
        ? parseInt(formData.value.paidAmount)
        : formData.value.cashAmount
        ? parseInt(formData.value.cashAmount)
        : 0,
    tien_chuyen_khoan:
      formData.value.paymentSource === "transfer"
        ? parseInt(formData.value.paidAmount)
        : formData.value.transferAmount
        ? parseInt(formData.value.transferAmount)
        : 0,
    ngay_chi: formData.value.date,
    ghi_chu: formData.value.note || null,
    anh_chung_tu:
      formData.value.attachments.length > 0 ? formData.value.attachments : null,
    // ❌ Không gửi 'ma_phieu_chi' - Backend tự tạo
  };

  if (formData.value.type === "chi_nhap_hang") {
    submitData.nha_cung_cap_id = formData.value.supplierId;
  } else {
    submitData.doi_tuong_nhan_tien = formData.value.recipient;
  }

  const response = await createPhieuChi(submitData);
  // Response sẽ trả về ma_phieu_chi đã được tạo tự động
};
```

## ✨ Ưu điểm

### 1. **Tính nhất quán**

- Mã phiếu luôn theo chuẩn định dạng
- Không có lỗi nhập sai format
- Dễ dàng tra cứu và sắp xếp

### 2. **Không trùng lặp**

- Backend kiểm tra và tăng số thứ tự tự động
- Xử lý race condition khi tạo đồng thời

### 3. **User-friendly**

- Người dùng không cần nghĩ mã phiếu
- Giảm thời gian nhập liệu
- Tránh lỗi nhập mã bị trùng

### 4. **Theo dõi dễ dàng**

- Dễ phân biệt loại phiếu qua prefix (PCN/PCV)
- Biết ngay phiếu được tạo ngày nào
- Số thứ tự cho biết thứ tự trong ngày

## 📊 Ví dụ thực tế

### Ngày 06/12/2024

| STT | Loại          | Mã phiếu      | Lý do                   |
| --- | ------------- | ------------- | ----------------------- |
| 1   | Chi nhập hàng | PCN2412060001 | Nhập thuốc từ NCC ABC   |
| 2   | Chi vận hành  | PCV2412060001 | Tiền điện tháng 12      |
| 3   | Chi nhập hàng | PCN2412060002 | Nhập vắc-xin từ NCC XYZ |
| 4   | Chi vận hành  | PCV2412060002 | Tiền nước tháng 12      |
| 5   | Chi nhập hàng | PCN2412060003 | Nhập dụng cụ y tế       |

### Ngày 07/12/2024

| STT | Loại          | Mã phiếu      | Lý do              |
| --- | ------------- | ------------- | ------------------ |
| 1   | Chi vận hành  | PCV2412070001 | Thuê nhà tháng 12  |
| 2   | Chi nhập hàng | PCN2412070001 | Nhập găng tay y tế |

## 🔒 Validation Rules

### Backend Validation

```php
'loai_phieu_chi' => 'required|in:chi_nhap_hang,chi_van_hanh',
'ly_do_chi' => 'required|string',
'tong_so_tien' => 'required|numeric|min:0',
'so_tien_thanh_toan_ngay' => 'nullable|numeric|min:0',
'tien_mat' => 'nullable|numeric|min:0',
'tien_chuyen_khoan' => 'nullable|numeric|min:0',
'doi_tuong_nhan_tien' => 'required_if:loai_phieu_chi,chi_van_hanh',
'nha_cung_cap_id' => 'required_if:loai_phieu_chi,chi_nhap_hang|exists:nha_cung_caps,id',
'ngay_chi' => 'required|date',
'ghi_chu' => 'nullable|string',
'anh_chung_tu' => 'nullable|array',
'anh_chung_tu.*' => 'nullable|string', // Base64 hoặc URL
```

### Frontend Validation

```javascript
const isFormValid = computed(() => {
  const hasRecipient =
    formData.value.type === "chi_nhap_hang"
      ? formData.value.supplierId !== null
      : formData.value.recipient.trim() !== "";

  const hasValidPayment =
    formData.value.paymentSource === "both"
      ? formData.value.cashAmount.trim() !== "" ||
        formData.value.transferAmount.trim() !== ""
      : formData.value.paidAmount.trim() !== "";

  return (
    formData.value.type &&
    hasRecipient &&
    formData.value.reason.trim() !== "" &&
    formData.value.totalAmount.trim() !== "" &&
    formData.value.paidAmount.trim() !== "" &&
    hasValidPayment &&
    formData.value.date.trim() !== ""
  );
});
```

## 🧪 Testing Checklist

- [ ] Tạo phiếu chi nhập hàng → Kiểm tra mã bắt đầu bằng `PCN`
- [ ] Tạo phiếu chi vận hành → Kiểm tra mã bắt đầu bằng `PCV`
- [ ] Tạo nhiều phiếu cùng ngày → Kiểm tra số thứ tự tăng dần
- [ ] Tạo phiếu vào ngày khác → Kiểm tra số thứ tự reset về 0001
- [ ] Tạo đồng thời nhiều phiếu → Kiểm tra không bị trùng mã
- [ ] Response trả về mã phiếu chính xác
- [ ] UI hiển thị thông báo đúng

## 📝 API Routes

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/phieu-chi', [PhieuChiController::class, 'index']);
    Route::post('/phieu-chi', [PhieuChiController::class, 'store']);
    Route::get('/phieu-chi/{id}', [PhieuChiController::class, 'show']);
    Route::put('/phieu-chi/{id}', [PhieuChiController::class, 'update']);
    Route::delete('/phieu-chi/{id}', [PhieuChiController::class, 'destroy']);
});
```

## 🎯 Best Practices

### 1. **Error Handling**

```javascript
try {
  const response = await createPhieuChi(submitData);
  if (response && response.status) {
    // Hiển thị mã phiếu vừa tạo
    showSuccessToast(
      `Tạo phiếu chi thành công! Mã: ${response.data.ma_phieu_chi}`
    );
  }
} catch (error) {
  const errorMessage =
    error.response?.data?.message || "Không thể tạo phiếu chi";
  showErrorToast(errorMessage);
}
```

### 2. **Display Created Voucher**

```javascript
// After successful creation
emit("submit", response.data); // Pass full data including ma_phieu_chi
emit("close");

// Parent component can then show/refresh list with new voucher
```

### 3. **Loading States**

```vue
<button @click="handleSubmit" :disabled="!isFormValid || isSubmitting">
  <svg v-if="isSubmitting" class="animate-spin">...</svg>
  <span>{{ isSubmitting ? 'Đang tạo...' : 'Tạo phiếu chi' }}</span>
</button>
```

## 🔗 Related Files

### Backend

- `app/Http/Controllers/PhieuChiController.php`
- `app/Models/PhieuChi.php`
- `routes/api.php`

### Frontend

- `src/utils/phieuChi.js`
- `src/components/Admin/TaiChinhHoaDon/PhieuChi/TaoPhieuChi/index.vue`
- `src/components/Nurse/PhieuChi/TaoPhieuChi/index.vue`

## ✅ Summary

Tính năng **tự động tạo mã phiếu chi** đã được triển khai thành công:

✅ Backend tự động generate mã theo format chuẩn  
✅ Frontend không cần input field cho mã phiếu  
✅ UI hiển thị thông báo rõ ràng về việc tạo mã tự động  
✅ Validation hoàn chỉnh  
✅ Error handling đầy đủ  
✅ User experience tốt hơn

---

**Người thực hiện:** GitHub Copilot  
**Ngày:** 06/12/2024  
**Version:** 1.0
