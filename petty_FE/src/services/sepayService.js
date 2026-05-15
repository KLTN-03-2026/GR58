import api from "@/utils/api";

export const sepayService = {
  async createPayment(lichHenId, ghiChu = null) {
    const res = await api.post("/thanh-toan/chuyen-khoan", {
      lich_hen_id: lichHenId,
      ghi_chu: ghiChu,
    });
    return res.data;
  },

  async checkStatus(thanhToanId) {
    const res = await api.get(`/thanh-toan/${thanhToanId}/trang-thai`);
    return res.data;
  },

  async confirmManual(thanhToanId) {
    const res = await api.post(`/thanh-toan/${thanhToanId}/confirm`);
    return res.data;
  },
};

export default sepayService;
