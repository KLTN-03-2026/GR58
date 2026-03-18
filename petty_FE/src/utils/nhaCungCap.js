import api from "./api";

/**
 * Lấy danh sách nhà cung cấp
 */
export async function getNhaCungCaps() {
  const response = await api.get("/nha-cung-cap");
  return response.data;
}

/**
 * Thêm nhà cung cấp mới
 */
export async function createNhaCungCap(data) {
  const response = await api.post("/nha-cung-cap", data);
  return response.data;
}

/**
 * Lấy thông tin chi tiết nhà cung cấp
 */
export async function getNhaCungCap(id) {
  const response = await api.get(`/nha-cung-cap/${id}`);
  return response.data;
}

/**
 * Cập nhật thông tin nhà cung cấp
 */
export async function updateNhaCungCap(id, data) {
  const response = await api.put(`/nha-cung-cap/${id}`, data);
  return response.data;
}

/**
 * Xóa nhà cung cấp
 */
export async function deleteNhaCungCap(id) {
  const response = await api.delete(`/nha-cung-cap/${id}`);
  return response.data;
}

/**
 * Đổi trạng thái nhà cung cấp
 */
export async function changeNhaCungCapStatus(id) {
  const response = await api.patch(`/nha-cung-cap/${id}/trang-thai`);
  return response.data;
}
