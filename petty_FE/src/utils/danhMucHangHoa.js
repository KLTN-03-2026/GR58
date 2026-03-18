import client from "./api";

const BASE = "/danh-muc-hang-hoa";

export async function listDanhMucHangHoa(params = {}) {
  const res = await client.get(BASE, { params });
  return res.data && res.data.data ? res.data.data : [];
}

export async function getDanhMucHangHoa(id) {
  const res = await client.get(`${BASE}/${id}`);
  return res.data && res.data.data ? res.data.data : null;
}

export async function createDanhMucHangHoa(payload) {
  // Map frontend-friendly keys to backend expected keys
  const body = {
    // backend expects 'ten_danh_muc_hang_hoa' for the name
    ten_danh_muc_hang_hoa:
      payload.ten_danh_muc_hang_hoa || payload.name || payload.ten || null,
    // common field name for description in Vietnamese (adjust if different)
    mo_ta: payload.mo_ta || payload.description || null,
  };

  const res = await client.post(BASE, body);
  return res.data && res.data.data ? res.data.data : null;
}

export async function updateDanhMucHangHoa(id, payload) {
  const body = {
    ten_danh_muc_hang_hoa:
      payload.ten_danh_muc_hang_hoa || payload.name || payload.ten || null,
    mo_ta: payload.mo_ta || payload.description || null,
  };

  const res = await client.put(`${BASE}/${id}`, body);
  return res.data && res.data.data ? res.data.data : null;
}

export async function deleteDanhMucHangHoa(id) {
  const res = await client.delete(`${BASE}/${id}`);
  return res.data || null;
}

export default {
  listDanhMucHangHoa,
  getDanhMucHangHoa,
  createDanhMucHangHoa,
  updateDanhMucHangHoa,
  deleteDanhMucHangHoa,
};
