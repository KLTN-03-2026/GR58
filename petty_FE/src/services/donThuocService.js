import api from "@/utils/api";

/**
 * Lấy đơn thuốc của phiếu khám
 * @param {number} phieuKhamId - ID phiếu khám
 * @returns {Promise} Response data
 */
export const getByPhieuKham = async (phieuKhamId) => {
  try {
    const response = await api.get(`/phieu-kham/${phieuKhamId}/don-thuoc`);
    return response.data;
  } catch (error) {
    console.error("Error fetching don thuoc:", error);
    throw error;
  }
};

/**
 * Lưu đơn thuốc (replace toàn bộ)
 * @param {number} phieuKhamId - ID phiếu khám
 * @param {Array} items - Danh sách thuốc
 * @returns {Promise} Response data
 */
export const save = async (phieuKhamId, items) => {
  try {
    const response = await api.post(`/phieu-kham/${phieuKhamId}/don-thuoc`, {
      items,
    });
    return response.data;
  } catch (error) {
    console.error("Error saving don thuoc:", error);
    throw error;
  }
};

/**
 * Xóa một dòng thuốc khỏi đơn thuốc
 * @param {number} phieuKhamId - ID phiếu khám
 * @param {number} chiTietId - ID chi tiết phiếu khám
 * @returns {Promise} Response data
 */
export const deleteItem = async (phieuKhamId, chiTietId) => {
  try {
    const response = await api.delete(
      `/phieu-kham/${phieuKhamId}/don-thuoc/${chiTietId}`
    );
    return response.data;
  } catch (error) {
    console.error("Error deleting don thuoc item:", error);
    throw error;
  }
};
