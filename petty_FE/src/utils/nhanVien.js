import client from "./api";

const BASE = "/nhan-vien";

function normalizeRole(selectedRoles) {
  if (!selectedRoles) return null;
  const first = Array.isArray(selectedRoles) ? selectedRoles[0] : selectedRoles;
  if (!first) return null;
  const raw = String(first).trim().toLowerCase();

  // Map common UI labels / variants to backend slugs
  if (
    raw.includes("bác") ||
    raw.includes("bac") ||
    raw.includes("bác sĩ") ||
    raw.includes("bac si")
  )
    return "bac_si";
  if (raw.includes("y") || raw.includes("y tá") || raw.includes("y ta"))
    return "y_ta";

  // fallback: return raw with underscores replacing spaces
  return raw.replace(/\s+/g, "_");
}

export async function createNhanVien(payload) {
  // Build payload using backend field names expected by NhanVienRequest
  const body = {
    full_name: payload.fullName || payload.full_name || null,
    email: payload.email || null,
    // backend expects 'phone' (10 digits)
    phone:
      payload.phone || payload.so_dien_thoai || payload.phone_number || null,
    address: payload.address || payload.dia_chi || null,
    // avatar / image field
    anh_dai_dien: payload.avatar || payload.anh_dai_dien || null,
    // role normalized to 'bac_si' | 'y_ta'
    vai_tro: normalizeRole(payload.selectedRoles) || payload.vai_tro || null,
    // professional data
    chuc_danh: payload.position || payload.chuc_danh || null,
    nam_kinh_nghiem:
      payload.yearsOfExperience || payload.nam_kinh_nghiem || null,
    chung_chi_hanh_nghe:
      payload.practiceCertificate || payload.chung_chi_hanh_nghe || null,
    bang_cap_chuyen_mon:
      payload.professionalDegree || payload.bang_cap_chuyen_mon || null,
    // password (and confirmation)
    password: payload.password || payload.mat_khau || null,
    password_confirmation:
      payload.passwordConfirmation || payload.password_confirmation || null,
    // map frontend status to backend accepted values 'hoat_dong'|'da_khoa'
    trang_thai:
      (payload.status === "active" && "hoat_dong") ||
      (payload.status === "locked" && "da_khoa") ||
      payload.trang_thai ||
      payload.status ||
      null,
  };

  const res = await client.post(BASE, body);
  return res.data || null;
}

export async function listNhanVien(params = {}) {
  const res = await client.get(BASE, { params });
  // backend returns { status: true, data: [...] }
  return res.data && res.data.data ? res.data.data : [];
}

export async function dangXuat() {
  const res = await client.post("/nhan-vien/dang-xuat");
  return res.data || null;
}

export default {
  createNhanVien,
  listNhanVien,
  dangXuat,
};
