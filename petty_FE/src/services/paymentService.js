import api from "@/utils/api";

export const paymentService = {
  /**
   * Lấy danh sách lịch hẹn / hóa đơn của khách hàng đang đăng nhập
   * @param {Object} params - Optional query params
   */
  async getInvoices(params = {}) {
    const res = await api.get("/lich-hen", { params });
    return res.data;
  },

  /**
   * Tạo thanh toán MoMo
   * @param {Object} data - { order_id, amount }
   */
  async createMoMoPayment(data) {
    const res = await api.post("/payment/momo/create", data);
    return res.data;
  },

  /**
   * Cập nhật trạng thái thanh toán
   * @param {Object} data - { order_id, result_code }
   */
  async updatePaymentStatus(data) {
    const res = await api.post("/payment/update-status", data);
    return res.data;
  },

  /**
   * Lấy chi tiết lịch hẹn theo ID
   * @param {number} id
   */
  async getInvoiceById(id) {
    const res = await api.get(`/lich-hen/${id}`);
    return res.data;
  },
};

export default paymentService;
