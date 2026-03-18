import axios from "axios";
import { getToken } from "./auth";

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const client = axios.create({
  baseURL: API_BASE,
});

// Attach token to requests
client.interceptors.request.use(
  (config) => {
    const token = getToken();
    if (token) {
      config.headers["Authorization"] = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Khuyến mãi API
export const khuyenMaiAPI = {
  // Lấy danh sách khuyến mãi
  getAll: (params = {}) => {
    return client.get("/khuyen-mai", { params });
  },

  // Lấy chi tiết khuyến mãi
  getById: (id) => {
    return client.get(`/khuyen-mai/${id}`);
  },

  // Tạo khuyến mãi mới
  create: (data) => {
    return client.post("/khuyen-mai", data);
  },

  // Cập nhật khuyến mãi
  update: (id, data) => {
    return client.put(`/khuyen-mai/${id}`, data);
  },

  // Xóa khuyến mãi
  delete: (id) => {
    return client.delete(`/khuyen-mai/${id}`);
  },

  // Đổi trạng thái khuyến mãi
  changeStatus: (id) => {
    return client.patch(`/khuyen-mai/${id}/trang-thai`);
  },

  // Kiểm tra mã code
  checkCode: (data) => {
    return client.post("/khuyen-mai/check-code", data);
  },
};

// Dịch vụ API
export const dichVuAPI = {
  // Lấy danh sách dịch vụ
  getAll: (params = {}) => {
    return client.get("/dich-vu", { params });
  },

  // Lấy chi tiết dịch vụ
  getById: (id) => {
    return client.get(`/dich-vu/${id}`);
  },
};

export default client;
