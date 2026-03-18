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
 * Lấy danh sách phiếu chi
 * @param {Object} params - Query parameters
 */
export async function getPhieuChis(params = {}) {
  try {
    const response = await client.get("/phieu-chi", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching phieu chi:", error);
    throw error;
  }
}

/**
 * Lấy chi tiết một phiếu chi
 * @param {number} id - ID của phiếu chi
 */
export async function getPhieuChiById(id) {
  try {
    const response = await client.get(`/phieu-chi/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching phieu chi detail:", error);
    throw error;
  }
}

/**
 * Tạo mới phiếu chi
 * @param {Object} data - Dữ liệu phiếu chi
 */
export async function createPhieuChi(data) {
  try {
    const response = await client.post("/phieu-chi", data);
    return response.data;
  } catch (error) {
    console.error("Error creating phieu chi:", error);
    throw error;
  }
}

/**
 * Cập nhật phiếu chi
 * @param {number} id - ID của phiếu chi
 * @param {Object} data - Dữ liệu cập nhật
 */
export async function updatePhieuChi(id, data) {
  try {
    const response = await client.put(`/phieu-chi/${id}`, data);
    return response.data;
  } catch (error) {
    console.error("Error updating phieu chi:", error);
    throw error;
  }
}

/**
 * Xóa phiếu chi
 * @param {number} id - ID của phiếu chi
 */
export async function deletePhieuChi(id) {
  try {
    const response = await client.delete(`/phieu-chi/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error deleting phieu chi:", error);
    throw error;
  }
}

/**
 * Lấy lịch sử thanh toán của phiếu chi
 * @param {number} id - ID của phiếu chi
 */
export async function getPhieuChiPaymentHistory(id) {
  try {
    const response = await client.get(`/phieu-chi/${id}/lich-su-thanh-toan`);
    return response.data;
  } catch (error) {
    console.error("Error fetching payment history:", error);
    throw error;
  }
}
