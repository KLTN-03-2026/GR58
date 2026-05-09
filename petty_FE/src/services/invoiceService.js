import api from "@/utils/api";

/**
 * ========================================
 * INVOICE SERVICE - HÓA ĐƠN
 * ========================================
 */

/**
 * Lấy danh sách hóa đơn
 * @param {Object} params - { search, period, trang_thai, hinh_thuc, page, per_page }
 */
export const getInvoices = async (params = {}) => {
  try {
    const response = await api.get("/invoices", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching invoices:", error);
    throw error;
  }
};

/**
 * Lấy chi tiết 1 hóa đơn
 * @param {number} id
 */
export const getInvoiceDetail = async (id) => {
  try {
    const response = await api.get(`/invoices/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching invoice detail:", error);
    throw error;
  }
};

export default {
  getInvoices,
  getInvoiceDetail,
};