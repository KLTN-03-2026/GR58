# ❌ ISSUE: Doctor Name Not Loading in Appointment Details

## Problem Description

Cột "Phụ trách" (Assigned Staff) in the appointment list and appointment detail modal is not showing doctor name even when a doctor is assigned.

## Root Cause Analysis

### Frontend Issue Detection

Added comprehensive logging to trace the issue:

**File: `src/components/Admin/VanHanh/QuanLyLichHen/index.vue`**

- Line 457-458: Logs raw appointment data
- Line 459-460: Logs `nhan_vien` and `nhan_vien_id` values
- Line 467: Warns if `nhan_vien_id` exists but `nhan_vien` object is empty

**File: `src/components/Admin/VanHanh/QuanLyLichHen/ChiTietLichHen/index.vue`**

- Line 232: Logs `data.nhan_vien` to console
- Line 233: Logs `data.nhan_vien_id` to console
- Line 235: Logs success if doctor found
- Line 241: Logs error if `nhan_vien` not in response

### Expected Backend Response

Backend should return appointment with EAGER LOADED relationship:

```json
{
  "id": "LH001237",
  "ngay_gio": "2025-11-19T15:00:00",
  "nhan_vien_id": 5,
  "nhan_vien": {
    "id": 5,
    "ho_ten": "Nguyễn Văn A",
    "chuc_vu": "Bác Sĩ Chuyên Khoa",
    ...
  },
  "khach_hang": {...},
  "thu_cung": {...},
  "dich_vu": {...},
  "thanh_toan": {...}
}
```

## Solution

### For Backend Developer

#### Option 1: Eager Load in LichHenController@show

```php
public function show(LichHen $lichHen)
{
    $lichHen->load('nhan_vien', 'khach_hang', 'thu_cung', 'dich_vu', 'thanh_toan');
    return response()->json([
        'status' => true,
        'data' => $lichHen
    ]);
}
```

#### Option 2: Eager Load in LichHenController@index

```php
public function index(Request $request)
{
    $query = LichHen::query()
        ->with(['nhan_vien', 'khach_hang', 'thu_cung', 'dich_vu', 'thanh_toan']);

    if (!$request->filled('all')) {
        $query->where('khach_hang_id', Auth::id());
    }

    $appointments = $query->get();

    return response()->json([
        'status' => true,
        'data' => $appointments
    ]);
}
```

#### Option 3: Use withTrashed for soft-deleted records

If nhan_vien is soft-deleted, need:

```php
$lichHen->load('nhan_vien:id,ho_ten,chuc_vu'); // Specify columns
// OR
LichHen::with('nhan_vien')->withoutGlobalScopes()->get();
```

### Frontend Verification

1. **Open browser DevTools Console (F12)**

2. **Navigate to appointment list:**

   - Should see logs like:

   ```
   📌 Raw appointment data: {...}
   📌 nhan_vien: {id: 5, ho_ten: "Nguyễn Văn A", ...}
   ✅ Assigned Staff created: {initial: "N", name: "Nguyễn Văn A", department: "Bác Sĩ"}
   ```

3. **If you see:**

   ```
   ❌ No doctor assigned - nhan_vien and nhan_vien_id are both empty
   ```

   → Doctor was never assigned to appointment

4. **If you see:**

   ```
   ⚠️ Backend returned nhan_vien_id but not nhan_vien object. Relationships not loaded!
   ```

   → Backend has `nhan_vien_id` but didn't eager load `nhan_vien` relationship

5. **Click detail modal and check console:**
   - Should see additional logs:
   ```
   🔍 Checking nhan_vien: {id: 5, ho_ten: "Nguyễn Văn A", ...}
   ✅ Doctor found: Nguyễn Văn A
   ```

## Field Name Fallback Chain

Frontend code handles multiple field name variations:

**For Doctor Name:**

- `data.nhan_vien.ho_ten` (Vietnamese)
- `data.nhan_vien.name` (English)
- `data.nhan_vien.ten` (Shortened)
- `data.nhan_vien.full_name` (Alternative)
- Default: "Bác Sĩ"

**For Department:**

- `data.nhan_vien.chuc_vu` (Vietnamese)
- Default: "Bác Sĩ"

## Testing Checklist

After backend fix:

- [ ] Open appointment list page
- [ ] Check browser console for logs
- [ ] Verify `nhan_vien` object is present in logs
- [ ] Verify doctor name displays in "Phụ trách" column
- [ ] Click doctor assignment modal to change doctor
- [ ] Verify new doctor name updates in list
- [ ] Click detail modal (eye icon)
- [ ] Verify doctor name displays in detail view
- [ ] Check console logs show doctor info loading correctly

## File Locations

**Frontend Files:**

- `src/components/Admin/VanHanh/QuanLyLichHen/index.vue` - Main list component
- `src/components/Admin/VanHanh/QuanLyLichHen/ChiTietLichHen/index.vue` - Detail modal component
- `src/components/Admin/VanHanh/QuanLyLichHen/PhanCongBacSi/index.vue` - Doctor assignment modal

**Backend Endpoints:**

- `GET /api/lich-hen` - List appointments (with `?all=1` for admin)
- `GET /api/lich-hen/{id}` - Get single appointment detail
- `PUT/PATCH /api/lich-hen/{id}` - Update appointment with doctor assignment

## Status

✅ Frontend logging added and ready for testing
⏳ Waiting for backend to eager load `nhan_vien` relationship
