import api from "@/utils/api";

export const hoTroService = {
  /**
   * Gửi yêu cầu hỗ trợ (public hoặc authenticated customer)
   * @param {Object} data - { ho_ten, email, so_dien_thoai, chu_de, noi_dung }
   */
  async submitRequest(data) {
    const res = await api.post("/yeu-cau-ho-tro", data);
    return res.data;
  },

  /**
   * Lấy danh sách yêu cầu (staff only)
   * @param {Object} params
   */
  async getAll(params = {}) {
    const res = await api.get("/yeu-cau-ho-tro", { params });
    return res.data;
  },

  /**
   * Cập nhật trạng thái / phản hồi (staff only)
   * @param {number} id
   * @param {Object} data - { trang_thai, phan_hoi }
   */
  async update(id, data) {
    const res = await api.patch(`/yeu-cau-ho-tro/${id}`, data);
    return res.data;
  },
};

export default hoTroService;
