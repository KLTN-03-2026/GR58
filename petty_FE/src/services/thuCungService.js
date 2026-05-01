import api from "@/utils/api";

/**
 * Lấy lịch sử khám bệnh của thú cưng (dành cho khách hàng đã đăng nhập).
 * Chỉ trả về dữ liệu nếu pet thuộc sở hữu của customer.
 * @param {number} petId
 * @returns {Promise}
 */
export const getLichSuKham = async (petId) => {
  const response = await api.get(`/thu-cung/${petId}/lich-su-kham`);
  return response.data;
};

export default { getLichSuKham };
