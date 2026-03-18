# Hướng Dẫn Chi Tiết Phiếu Nhập Kho

## Tổng Quan

Component `ChiTietPhieuNhap` đã được cập nhật để hiển thị đầy đủ thông tin chi tiết của phiếu nhập kho từ Backend API.

## Các Thay Đổi Chính

### 1. **Cấu Trúc Dữ Liệu**

#### Props

```javascript
props: {
  receipt: {
    type: Object,
    required: true
    // Nhận object receipt từ parent component
    // Bao gồm: id, code, supplier, date, user, status, _original
  }
}
```

#### Data State

- `isLoading`: Trạng thái loading khi fetch data
- `receiptData`: Thông tin phiếu nhập đã được format
- `detailItems`: Danh sách chi tiết hàng hóa trong phiếu

### 2. **Luồng Dữ Liệu**

#### Khi Component Mount

1. **Check dữ liệu từ props:**

   - Nếu `props.receipt._original.chi_tiet` tồn tại → Sử dụng trực tiếp
   - Nếu không → Gọi API `getChiTietPhieuNhapKho(id)`

2. **Map dữ liệu Backend → Frontend:**

**Thông tin phiếu nhập:**

```javascript
receiptData = {
  id: data.id,
  code: data.ma_phieu_nhap,
  supplier: data.nha_cung_cap?.ten_nha_cung_cap,
  date: data.ngay_nhap,
  user: data.nhan_vien?.full_name || data.admin?.ho_ten,
  status: data.trang_thai,
  total: data.tong_tien,
  _original: data, // Lưu dữ liệu gốc
};
```

**Chi tiết items:**

```javascript
detailItems = data.chi_tiet.map((item) => ({
  productName: item.hang_hoa?.ten_mat_hang,
  productCode: item.hang_hoa?.ma_hang_hoa,
  unit: item.hang_hoa?.don_vi_tinh,
  quantity: item.so_luong,
  unitPrice: item.don_gia,
  lotNumber: item.so_lo,
  expiryDate: item.han_su_dung,
  total: item.so_luong * item.don_gia,
  notes: item.ghi_chu,
}));
```

### 3. **Hiển Thị Dữ Liệu**

#### Header Section

- **Mã phiếu nhập**: `receiptData.code`
- **Trạng thái**: Badge với màu sắc tương ứng
  - `cho_duyet`: Chờ duyệt (vàng)
  - `da_duyet`: Đã duyệt (xanh dương)
  - `da_nhap_kho`: Đã nhập kho (xanh lá)
  - `huy`: Đã hủy (đỏ)

#### Thông Tin Chung

- **Nhà cung cấp**: Từ `nha_cung_cap.ten_nha_cung_cap`
- **Ngày nhập**: Format dd/mm/yyyy
- **Người nhập**: Ưu tiên `nhanVien.full_name`, nếu không có thì `admin.ho_ten`
- **Ghi chú**: Hiển thị nếu có

#### Bảng Chi Tiết Hàng Hóa

Mỗi dòng hiển thị:

- STT
- Tên hàng hóa + Mã hàng
- Đơn vị tính
- Số lượng
- Đơn giá (format currency)
- Số lô + Hạn sử dụng
- Thành tiền (format currency)

#### Tổng Kết

- **Tổng số lượng**: Sum của tất cả quantity
- **Tổng tiền hàng**: Sum của tất cả total

### 4. **Helper Functions**

#### formatCurrency(amount)

```javascript
// Chuyển số thành format VNĐ
// VD: 1000000 → "1.000.000 ₫"
```

#### formatDate(dateString)

```javascript
// Chuyển date thành dd/mm/yyyy
// VD: "2024-12-05" → "05/12/2024"
```

#### getStatusText(status)

```javascript
// Map status key → Tên hiển thị
// VD: "cho_duyet" → "Chờ duyệt"
```

#### getStatusBadgeClass(status)

```javascript
// Trả về class CSS cho badge status
```

### 5. **API Integration**

#### Endpoint

```
GET /phieu-nhap-kho/{id}
```

#### Response Structure

```json
{
  "status": true,
  "message": "Success",
  "data": {
    "id": 1,
    "ma_phieu_nhap": "PN20241205123456",
    "ngay_nhap": "2024-12-05",
    "trang_thai": "cho_duyet",
    "tong_tien": 5000000,
    "ghi_chu": "Nhập hàng tháng 12",
    "nha_cung_cap": {
      "id": 1,
      "ten_nha_cung_cap": "Công ty ABC"
    },
    "nhan_vien": {
      "id": 1,
      "full_name": "Nguyễn Văn A"
    },
    "chi_tiet": [
      {
        "id": 1,
        "hang_hoa_id": 1,
        "so_luong": 50,
        "don_gia": 50000,
        "so_lo": "LOT2024001",
        "han_su_dung": "2025-12-15",
        "ghi_chu": "",
        "hang_hoa": {
          "id": 1,
          "ma_hang_hoa": "MED001",
          "ten_mat_hang": "Zoletil 50",
          "don_vi_tinh": "Lọ"
        }
      }
    ]
  }
}
```

### 6. **Utils Function Added**

Đã thêm alias trong `src/utils/phieuNhapKho.js`:

```javascript
export const getChiTietPhieuNhapKho = getPhieuNhapKho;
```

## Cách Sử Dụng

### Từ Parent Component

```vue
<template>
  <ChiTietPhieuNhap
    v-if="isChiTietPhieuNhapModalOpen"
    :receipt="selectedReceiptForDetail"
    @close="isChiTietPhieuNhapModalOpen = false"
  />
</template>

<script setup>
const selectedReceiptForDetail = ref(null);
const isChiTietPhieuNhapModalOpen = ref(false);

const handleOpenChiTietPhieuNhap = (record) => {
  // record có cấu trúc:
  // {
  //   id, code, supplier, date, user, total, status,
  //   _original: { ...dữ liệu gốc từ backend }
  // }
  selectedReceiptForDetail.value = record;
  isChiTietPhieuNhapModalOpen.value = true;
};
</script>
```

## Loading State

Component có loading indicator khi đang fetch dữ liệu:

- Hiển thị spinner animation
- Text "Đang tải dữ liệu..."
- Không hiển thị nội dung cho đến khi load xong

## Error Handling

- Nếu lỗi khi load: Hiển thị toast error
- Log lỗi vào console để debug
- Component vẫn có thể đóng được

## Tính Năng In Phiếu

Button "In phiếu nhập" emit event `print` với dữ liệu:

```javascript
handlePrint() {
  emit('print', receiptData.value)
}
```

Parent component có thể handle để implement chức năng in.

## Notes

1. **Dữ liệu tối ưu**: Component ưu tiên sử dụng dữ liệu từ props nếu đã có đầy đủ, tránh gọi API không cần thiết.

2. **Fallback values**: Tất cả fields đều có fallback 'N/A' nếu không có dữ liệu.

3. **Status mapping**: Trạng thái được map từ backend key sang text hiển thị tiếng Việt.

4. **Date formatting**: Tất cả ngày tháng được format sang dd/mm/yyyy cho người Việt dễ đọc.

5. **Currency formatting**: Số tiền được format theo chuẩn VN (dấu chấm ngăn cách hàng nghìn).

## Testing Checklist

- [ ] Modal mở/đóng chính xác
- [ ] Dữ liệu hiển thị đầy đủ và chính xác
- [ ] Status badge hiển thị đúng màu
- [ ] Format ngày tháng, tiền tệ chính xác
- [ ] Tổng số lượng và tổng tiền tính đúng
- [ ] Loading state hoạt động
- [ ] Error handling hoạt động
- [ ] Responsive trên các kích thước màn hình

## Troubleshooting

### Không hiển thị dữ liệu

- Check API endpoint có trả về dữ liệu đúng không
- Check structure của `receipt._original`
- Xem console log có error không

### Status không hiển thị đúng

- Check giá trị `trang_thai` từ backend
- Đảm bảo mapping trong `getStatusText()` có đầy đủ

### Format không đúng

- Check helper functions `formatCurrency()` và `formatDate()`
- Đảm bảo dữ liệu input đúng type

---

**Cập nhật lần cuối**: 05/12/2024
