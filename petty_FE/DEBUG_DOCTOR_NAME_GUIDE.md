# 🔧 Debug Guide: Doctor Name Not Loading

## Issue

Column "Phụ trách" shows "Chưa xác định" instead of doctor's name.

## Root Cause

Backend might be returning doctor info with a **different field name** than expected.

## Solution Implemented

### Frontend Changes

Added **dynamic field scanning** to find doctor name regardless of backend field naming:

**Files Updated:**

1. `src/components/Admin/VanHanh/QuanLyLichHen/index.vue` (Appointment List)
2. `src/components/Admin/VanHanh/QuanLyLichHen/ChiTietLichHen/index.vue` (Detail Modal)

### How It Works

```javascript
// Fallback chain:
const doctorName =
  nhan_vien.ho_ten || // Vietnamese standard
  nhan_vien.name || // English
  nhan_vien.ten || // Short name
  nhan_vien.full_name || // Alternative
  dynamicScan() || // Scan all string fields
  "Bác Sĩ"; // Default
```

## Debug Steps

### Step 1: Open Browser Console

- Press **F12** on browser
- Go to **Console** tab

### Step 2: View Raw Data

When appointment list loads, you'll see logs like:

```
📌 Raw appointment data: {...}
📌 nhan_vien: {id: 5, ho_ten: "Nguyễn Văn A", ...}
🔍 nhan_vien keys: ["id", "ho_ten", "chuc_vu", ...]
```

### Step 3: Check Doctor Field Names

Look for lines with doctor field values:

```
   - ho_ten: "Nguyễn Văn A"
   - name: undefined
   - ten: undefined
   - full_name: undefined
```

If all are `undefined` and you see this log:

```
🔍 Found dynamic name field 'some_field_name': "Nguyễn Văn A"
```

Then backend field name is `some_field_name`.

### Step 4: Open Detail Modal

Click the eye icon on an appointment to open detail modal and check console:

```
🔍 nhan_vien full object: {
  "id": 5,
  "ho_ten": "Nguyễn Văn A",
  "chuc_vu": "Bác Sĩ Chuyên Khoa"
}
✅ Doctor found: Nguyễn Văn A | Department: Bác Sĩ Chuyên Khoa
```

## Expected Console Output

### ✅ Success Case

```
📋 Appointments from API: [...]
📌 Raw appointment data: {id: "LH001237", nhan_vien: {...}}
🔍 nhan_vien keys: ["id", "ho_ten", "chuc_vu"]
✅ Assigned Staff created: {initial: "N", name: "Nguyễn Văn A", department: "Bác Sĩ"}
```

### ❌ Missing Relationship

```
⚠️ Backend returned nhan_vien_id but not nhan_vien object. Relationships not loaded!
```

→ Backend needs to eager load `nhan_vien` relationship

### ❌ Wrong Field Name

```
🔍 nhan_vien keys: ["id", "ten_bs", "nam_sinh"]
   - ho_ten: undefined
   - name: undefined
   - ten: undefined
```

→ Backend field might be `ten_bs` instead of `ho_ten`

### ❌ No Doctor Assigned

```
❌ No doctor assigned - nhan_vien and nhan_vien_id are both empty
```

→ Appointment doesn't have doctor assigned yet

## Quick Fixes

### If Backend Field is Not Standard

Once you identify the actual field name, add it to the fallback chain:

**File:** `src/components/Admin/VanHanh/QuanLyLichHen/index.vue`

Find this line around 500:

```javascript
const doctorName =
  data.nhan_vien?.ho_ten ||
  data.nhan_vien?.name ||
  data.nhan_vien?.ten ||
  data.nhan_vien?.full_name ||
```

Add your field:

```javascript
const doctorName =
  data.nhan_vien?.ho_ten ||
  data.nhan_vien?.name ||
  data.nhan_vien?.ten ||
  data.nhan_vien?.full_name ||
  data.nhan_vien?.ten_bs ||  // Add actual backend field
```

Do the same in `ChiTietLichHen/index.vue` around line 310.

## Report Template

When reporting the issue, include:

1. **Console logs output** (copy entire nhan_vien object)
2. **Which logs appeared:**
   - ✅ Successfully loaded?
   - ❌ "Chưa xác định"?
   - ❌ "Chưa gán"?
3. **Appointment that fails:**
   - Appointment ID
   - Doctor assignment status (assigned/not assigned)

## Testing Checklist

- [ ] Open appointment list page
- [ ] Check console - do you see appointment data?
- [ ] Do you see nhan_vien object keys?
- [ ] What are the actual field names in nhan_vien?
- [ ] Does doctor name display correctly?
- [ ] Click detail modal - is doctor name there?
- [ ] Copy console logs and report

## Files Modified

```
src/components/Admin/VanHanh/QuanLyLichHen/
├── index.vue                    ← Enhanced with dynamic field scanning
├── ChiTietLichHen/
│   └── index.vue               ← Enhanced with dynamic field scanning + separate fetch
└── PhanCongBacSi/
    └── index.vue               ← Doctor assignment modal
```

## Next Steps

1. **Test with current changes** - dynamic scanning should find the name
2. **If still shows "Chưa xác định"** - check console to see actual field names
3. **If shows "Chưa gán"** - appointment not assigned yet, click to assign
4. **If still not working** - provide console logs for further debugging
