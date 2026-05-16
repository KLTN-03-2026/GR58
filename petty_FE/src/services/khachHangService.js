import api from "@/utils/api";

export const khachHangService = {
  async search(query, perPage = 10) {
    const res = await api.get("/khach-hang", {
      params: { search: query, per_page: perPage },
    });
    return res.data;
  },

  async createCustomer(data) {
    const password = Math.random().toString(36).slice(-10) + "A1!";
    const payload = { ...data, password };
    const res = await api.post("/khach-hang/dang-ki", payload);
    return res.data;
  },
};

export default khachHangService;
