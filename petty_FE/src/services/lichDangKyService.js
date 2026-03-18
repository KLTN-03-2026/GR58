import api from "@/utils/api";

/**
 * ===========================================
 * LỊCH ĐĂNG KÝ CA LÀM VIỆC SERVICE
 * ===========================================
 * Quản lý việc nhân viên tự đăng ký ca làm việc
 * và admin duyệt/từ chối
 */

/**
 * Nhân viên tự đăng ký ca làm việc
 * Chỉ cần: ngay_gio và ghi_chu
 * @param {Object} data - { ngay_gio, ghi_chu }
 * @returns {Promise}
 */
export const dangKyNhanVien = async (data) => {
  try {
    const response = await api.post("/lich-dang-ky/dang-ky-nhan-vien", data);
    return response.data;
  } catch (error) {
    console.error("Error registering shift:", error);
    throw error;
  }
};

/**
 * Lấy danh sách tất cả lịch đăng ký
 * Nhân viên: chỉ xem của mình
 * Admin: xem tất cả hoặc filter theo nhan_vien_id
 * @param {Object} params - { trang_thai, ngay, tu_ngay, den_ngay, per_page, sort }
 * @returns {Promise}
 */
export const getDanhSachLichDangKy = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching registered shifts:", error);
    throw error;
  }
};

/**
 * Lấy lịch đăng ký của nhân viên đang đăng nhập
 * Admin có thể truyền nhan_vien_id để xem lịch của nhân viên cụ thể
 * @param {Object} params - { trang_thai, ngay, per_page }
 * @returns {Promise}
 */
export const getLichCuaToi = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky/lich-cua-toi", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching my shifts:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch chưa xác nhận
 * Hiển thị: tên nhân viên, thời gian, ghi chú, trạng thái
 * @param {Object} params - { ngay, tu_ngay, den_ngay, per_page }
 * @returns {Promise}
 */
export const getLichChuaXacNhan = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky/chua-xac-nhan", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching pending shifts:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch đã xác nhận (ca đã duyệt)
 * @param {Object} params - { nhan_vien_id, ngay, tu_ngay, den_ngay, per_page }
 * @returns {Promise}
 */
export const getLichDaXacNhan = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky/da-xac-nhan", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching confirmed shifts:", error);
    throw error;
  }
};

/**
 * Lấy ca đã xác nhận của nhân viên đang đăng nhập
 * Admin có thể truyền nhan_vien_id
 * @param {Object} params - { ngay, tu_ngay, den_ngay, per_page }
 * @returns {Promise}
 */
export const getCaDaXacNhanCuaToi = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky/ca-da-xac-nhan-cua-toi", {
      params,
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching my confirmed shifts:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch bị từ chối
 * @param {Object} params - { nhan_vien_id, ngay, tu_ngay, den_ngay, per_page }
 * @returns {Promise}
 */
export const getDanhSachTuChoi = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky/danh-sach-tu-choi", {
      params,
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching rejected shifts:", error);
    throw error;
  }
};

/**
 * Xác nhận lịch đăng ký (Admin only)
 * @param {number} id - ID của lịch đăng ký
 * @param {Object} data - { nhan_vien_id, admin_id, lich_lam_viec_id }
 * @returns {Promise}
 */
export const xacNhanLich = async (id, data = {}) => {
  try {
    const response = await api.patch(`/lich-dang-ky/${id}/xac-nhan`, data);
    return response.data;
  } catch (error) {
    console.error("Error confirming shift:", error);
    throw error;
  }
};

/**
 * Từ chối lịch đăng ký (Admin only)
 * @param {number} id - ID của lịch đăng ký
 * @param {Object} data - { admin_id, ghi_chu }
 * @returns {Promise}
 */
export const tuChoiLich = async (id, data = {}) => {
  try {
    const response = await api.patch(`/lich-dang-ky/${id}/tu-choi`, data);
    return response.data;
  } catch (error) {
    console.error("Error rejecting shift:", error);
    throw error;
  }
};

/**
 * Đổi trạng thái lịch đăng ký (Staff only)
 * @param {number} id - ID của lịch đăng ký
 * @param {Object} data - { trang_thai, admin_id, ghi_chu }
 * @returns {Promise}
 */
export const doiTrangThai = async (id, data) => {
  try {
    const response = await api.patch(
      `/lich-dang-ky/${id}/doi-trang-thai`,
      data
    );
    return response.data;
  } catch (error) {
    console.error("Error changing shift status:", error);
    throw error;
  }
};

/**
 * Lấy chi tiết một lịch đăng ký
 * @param {number} id - ID của lịch đăng ký
 * @returns {Promise}
 */
export const getChiTietLich = async (id) => {
  try {
    const response = await api.get(`/lich-dang-ky/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching shift detail:", error);
    throw error;
  }
};

/**
 * Cập nhật lịch đăng ký
 * @param {number} id - ID của lịch đăng ký
 * @param {Object} data - Dữ liệu cần cập nhật
 * @returns {Promise}
 */
export const capNhatLich = async (id, data) => {
  try {
    const response = await api.put(`/lich-dang-ky/${id}`, data);
    return response.data;
  } catch (error) {
    console.error("Error updating shift:", error);
    throw error;
  }
};

/**
 * Xóa lịch đăng ký
 * @param {number} id - ID của lịch đăng ký
 * @returns {Promise}
 */
export const xoaLich = async (id) => {
  try {
    const response = await api.delete(`/lich-dang-ky/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error deleting shift:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch đăng ký nhóm theo nhân viên (Admin only)
 * @param {Object} params - { trang_thai, ngay, tu_ngay, den_ngay, trang_thai_nhan_vien, sort }
 * @returns {Promise}
 */
export const getDanhSachTheoNhanVien = async (params = {}) => {
  try {
    const response = await api.get("/lich-dang-ky/danh-sach-theo-nhan-vien", {
      params,
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching shifts by employee:", error);
    throw error;
  }
};

/**
 * Lấy các trạng thái có thể có
 * @returns {Promise}
 */
export const getTrangThai = async () => {
  try {
    const response = await api.get("/lich-dang-ky/trang-thai");
    return response.data;
  } catch (error) {
    console.error("Error fetching statuses:", error);
    throw error;
  }
};

export default {
  dangKyNhanVien,
  getDanhSachLichDangKy,
  getLichCuaToi,
  getLichChuaXacNhan,
  getLichDaXacNhan,
  getCaDaXacNhanCuaToi,
  getDanhSachTuChoi,
  xacNhanLich,
  tuChoiLich,
  doiTrangThai,
  getChiTietLich,
  capNhatLich,
  xoaLich,
  getDanhSachTheoNhanVien,
  getTrangThai,
};
