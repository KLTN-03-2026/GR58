import api from "@/utils/api";

export const thongBaoService = {
  async getAll(page = 1) {
    const res = await api.get("/thong-bao", { params: { page } });
    return res.data;
  },

  async getUnreadCount() {
    const res = await api.get("/thong-bao/chua-doc/count");
    return res.data;
  },

  async markAsRead(id) {
    const res = await api.patch(`/thong-bao/${id}/da-doc`);
    return res.data;
  },

  async markAllAsRead() {
    const res = await api.patch("/thong-bao/da-doc-tat-ca");
    return res.data;
  },
};
