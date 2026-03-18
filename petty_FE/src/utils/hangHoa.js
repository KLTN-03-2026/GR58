import client from "./api";

const BASE = "/hang-hoa";

/**
 * Lấy danh sách tất cả hàng hóa
 */
export async function listHangHoa(params = {}) {
  const res = await client.get(BASE, { params });
  return res.data && res.data.data ? res.data.data : [];
}

/**
 * Lấy chi tiết một hàng hóa
 */
export async function getHangHoa(id) {
  const res = await client.get(`${BASE}/${id}`);
  return res.data && res.data.data ? res.data.data : null;
}

/**
 * Tạo hàng hóa mới
 */
export async function createHangHoa(payload) {
  // Map frontend keys to backend expected keys
  const body = {
    ma_hang_hoa: payload.code || payload.ma_hang_hoa || null,
    ten_mat_hang: payload.name || payload.ten_mat_hang || null,
    don_vi_tinh: payload.unit || payload.don_vi_tinh || null,
    gia_von: payload.costPrice || payload.gia_von || 0,
    gia_ban: payload.salePrice || payload.gia_ban || 0,
    dinh_muc_toi_thieu: payload.minStock || payload.dinh_muc_toi_thieu || 0,
    anh_san_pham: payload.imageUrl || payload.anh_san_pham || null,
    tinh_trang: payload.status || "hoat_dong",
    danh_muc_hang_hoa_id:
      payload.categoryId || payload.danh_muc_hang_hoa_id || null,
  };

  const res = await client.post(BASE, body);
  return res.data && res.data.data ? res.data.data : null;
}

/**
 * Cập nhật hàng hóa
 */
export async function updateHangHoa(id, payload) {
  const body = {
    ma_hang_hoa: payload.code || payload.ma_hang_hoa || null,
    ten_mat_hang: payload.name || payload.ten_mat_hang || null,
    don_vi_tinh: payload.unit || payload.don_vi_tinh || null,
    gia_von: payload.costPrice || payload.gia_von || 0,
    gia_ban: payload.salePrice || payload.gia_ban || 0,
    dinh_muc_toi_thieu: payload.minStock || payload.dinh_muc_toi_thieu || 0,
    anh_san_pham: payload.imageUrl || payload.anh_san_pham || null,
    tinh_trang: payload.status || "hoat_dong",
    danh_muc_hang_hoa_id:
      payload.categoryId || payload.danh_muc_hang_hoa_id || null,
  };

  const res = await client.put(`${BASE}/${id}`, body);
  return res.data && res.data.data ? res.data.data : null;
}

/**
 * Xóa hàng hóa
 */
export async function deleteHangHoa(id) {
  const res = await client.delete(`${BASE}/${id}`);
  return res.data || null;
}

/**
 * Tải lên hình ảnh hàng hóa
 */
export async function uploadHangHoaImage(file) {
  const formData = new FormData();
  formData.append("file", file);

  const res = await client.post("/upload", formData, {
    headers: {
      "Content-Type": "multipart/form-data",
    },
  });

  return res.data && res.data.data ? res.data.data : null;
}

/**
 * Đổi trạng thái hàng hóa
 */
export async function changeHangHoaStatus(id, tinhTrang) {
  const body = {
    tinh_trang: tinhTrang,
  };

  const res = await client.patch(`${BASE}/${id}/trang-thai`, body);
  return res.data && res.data.data ? res.data.data : null;
}

export default {
  listHangHoa,
  getHangHoa,
  createHangHoa,
  updateHangHoa,
  deleteHangHoa,
  uploadHangHoaImage,
  changeHangHoaStatus,
};
