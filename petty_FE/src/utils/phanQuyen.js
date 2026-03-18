import apiClient from "./api";

export const phanQuyenAPI = {
  // Lấy danh sách tất cả vai trò
  getAll() {
    return apiClient.get("/phan-quyen");
  },

  // Lấy chi tiết một vai trò
  getById(id) {
    return apiClient.get(`/phan-quyen/${id}`);
  },

  // Cập nhật quyền cho vai trò
  update(id, data) {
    return apiClient.patch(`/phan-quyen/${id}`, data);
  },

  // Lấy danh sách tất cả quyền có thể gán
  getAllPermissions() {
    return apiClient.get("/phan-quyen/danh-sach/tat-ca-quyen");
  },
};
