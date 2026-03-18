import axios from "axios";
import { getToken } from "./auth";

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const client = axios.create({
  baseURL: API_BASE,
});

// Interceptor để tự động thêm token
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

/**
 * Lấy danh sách kiểm kê
 * @param {Object} params - Query parameters (hang_hoa_id, trang_thai, tu_ngay, den_ngay, sort_by, sort_order, per_page)
 */
export async function getKiemKes(params = {}) {
  try {
    const response = await client.get("/kiem-ke", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching kiem ke:", error);
    throw error;
  }
}

/**
 * Lấy chi tiết một kiểm kê
 * @param {number} id - ID của kiểm kê
 */
export async function getKiemKeById(id) {
  try {
    const response = await client.get(`/kiem-ke/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching kiem ke detail:", error);
    throw error;
  }
}

/**
 * Tạo mới kiểm kê
 * @param {Object} data - Dữ liệu kiểm kê
 */
export async function createKiemKe(data) {
  try {
    const response = await client.post("/kiem-ke", data);
    return response.data;
  } catch (error) {
    console.error("Error creating kiem ke:", error);
    throw error;
  }
}

/**
 * Cập nhật kiểm kê
 * @param {number} id - ID của kiểm kê
 * @param {Object} data - Dữ liệu cập nhật
 */
export async function updateKiemKe(id, data) {
  try {
    const response = await client.put(`/kiem-ke/${id}`, data);
    return response.data;
  } catch (error) {
    console.error("Error updating kiem ke:", error);
    throw error;
  }
}

/**
 * Xóa kiểm kê
 * @param {number} id - ID của kiểm kê
 */
export async function deleteKiemKe(id) {
  try {
    const response = await client.delete(`/kiem-ke/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error deleting kiem ke:", error);
    throw error;
  }
}
