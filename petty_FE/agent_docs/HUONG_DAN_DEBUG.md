# Hướng Dẫn Debug Lỗi Danh Mục Không Hiển Thị

## 1. Kiểm tra Console Log

1. Mở DevTools: **F12** hoặc **Right Click → Inspect**
2. Tab **Console**
3. Vào trang Quản lý Bài viết
4. Tìm dòng sau trong console:
   ```
   "Post data:" {...}
   "Category data:" {...}
   ```

## 2. Kiểm tra API Response

Có 3 chỗ để check:

### A. Network Tab

1. F12 → Tab **Network**
2. Reload trang
3. Tìm request `/bai-viet`
4. Click vào → Tab **Response**
5. Kiểm tra xem `phanLoai` object có đúng không:
   ```json
   {
     "id": 1,
     "ten_bai_viet": "...",
     "phanLoai": {
       "id": 1,
       "ten_phan_loai": "Kiến thức"
     }
   }
   ```

### B. Console Log (Dễ nhất)

Chạy trong Console:

```javascript
// Sẽ in ra tất cả posts từ state
const { posts } = await fetch("/api/bai-viet").then((r) => r.json());
console.table(
  posts.map((p) => ({
    id: p.id,
    title: p.ten_bai_viet,
    phanLoai: p.phanLoai?.ten_phan_loai || "KHÔNG CÓ",
  }))
);
```

## 3. Xác Định Vấn Đề

### Nếu `phanLoai` KHÔNG hiển thị trong Response:

- ❌ **Lỗi Backend**: BaiVietController không eager load đúng
- **Sửa**: Kiểm tra dòng 17 của `BaiVietController.php`:
  ```php
  $baiViets = BaiViet::with(['nhanVien', 'phanLoai'])->get();
  ```

### Nếu `phanLoai` CÓ trong Response nhưng Frontend vẫn hiển thị "Không xác định":

- ❌ **Lỗi Frontend**: JavaScript không lấy được `ten_phan_loai`
- **Sửa**: Kiểm tra trong `index.vue` dòng ~240:
  ```javascript
  categoryLabel: post.phanLoai?.ten_phan_loai || "Không xác định",
  ```

### Nếu Database KHÔNG có link bài viết và danh mục:

- ❌ **Lỗi Database**: `phan_loai_bai_viet_id` NULL
- **Sửa**: Tạo bài viết mới và chọn danh mục

## 4. Kiểm Tra Database

```sql
-- Kiểm tra bảng bai_viets
SELECT id, ten_bai_viet, phan_loai_bai_viet_id FROM bai_viets LIMIT 5;

-- Kiểm tra bảng phan_loai_bai_viets
SELECT id, ten_phan_loai FROM phan_loai_bai_viets;

-- Join để xem đầy đủ
SELECT
    b.id,
    b.ten_bai_viet,
    p.id as phan_loai_id,
    p.ten_phan_loai
FROM bai_viets b
LEFT JOIN phan_loai_bai_viets p ON b.phan_loai_bai_viet_id = p.id;
```

## 5. Các Bước Fix (Theo Thứ Tự)

### Bước 1: Kiểm tra Database

```bash
# SSH vào MySQL
cd C:\xampp\mysql\bin
mysql -u root

# Chạy query trên
SELECT * FROM phan_loai_bai_viets;
SELECT * FROM bai_viets WHERE phan_loai_bai_viet_id IS NOT NULL LIMIT 5;
```

### Bước 2: Kiểm tra Backend eager load

- Xem dòng 17 của `BaiVietController.php`
- Đảm bảo có: `->with(['nhanVien', 'phanLoai'])`

### Bước 3: Kiểm tra Model Relationship

Mở `app/Models/BaiViet.php`:

```php
public function phanLoai()
{
    return $this->belongsTo(PhanLoaiBaiViet::class, 'phan_loai_bai_viet_id');
}
```

### Bước 4: Xóa cache Laravel

```bash
cd C:\xampp\htdocs\PETTY_VCMS_BE
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Bước 5: Test lại API

Mở Postman hoặc browser:

```
GET http://localhost:8000/api/bai-viet
```

Kiểm tra Response có `phanLoai` object không

## 6. Console Log Frontend Sẽ In Ra:

✅ **Đúng** (sẽ hiển thị danh mục):

```
Post data: {id: 1, ten_bai_viet: "...", phanLoai: {id: 1, ten_phan_loai: "Kiến thức"}}
Category data: {id: 1, ten_phan_loai: "Kiến thức"}
```

❌ **Sai** (sẽ hiển thị "Không xác định"):

```
Post data: {id: 1, ten_bai_viet: "...", phanLoai: null}
Category data: null
```

hoặc:

```
Post data: {id: 1, ten_bai_viet: "...", phanLoai: {}}
Category data: {}
```

---

**Bảng Kiểm Tra Nhanh:**

| Vấn Đề                                       | Nguyên Nhân                                   | Giải Pháp                                     |
| -------------------------------------------- | --------------------------------------------- | --------------------------------------------- |
| Console log `phanLoai: null`                 | Backend không load relationship               | Thêm `.with(['phanLoai'])`                    |
| Console log `phanLoai: {}`                   | Relationship rỗng, bài viết không có danh mục | Tạo bài viết mới với danh mục                 |
| Danh mục hiển thị "Không xác định"           | Frontend không lấy `ten_phan_loai`            | Kiểm tra path: `post.phanLoai?.ten_phan_loai` |
| Mặc dù API có dữ liệu nhưng vẫn hiển thị sai | Cache stale hoặc file chưa save               | `php artisan cache:clear` + refresh trang     |
