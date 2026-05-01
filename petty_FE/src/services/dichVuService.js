import api from "@/utils/api";

export const dichVuService = {
  async getAll(params = {}) {
    const res = await api.get("/dich-vu", { params });
    return res.data;
  },

  async getById(id) {
    const res = await api.get(`/dich-vu/${id}`);
    return res.data;
  },

  async getCategories() {
    const res = await api.get("/danh-muc-dich-vu");
    return res.data;
  },
};

export default dichVuService;
