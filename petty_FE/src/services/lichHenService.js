import api from "@/utils/api";

/**
 * ========================================
 * LỊCH HẸN SERVICE
 * ========================================
 * Quản lý các API liên quan đến lịch hẹn
 */

/**
 * Lấy danh sách tất cả lịch hẹn (cho staff/admin)
 * @param {Object} params - Filter parameters
 * @param {string} params.khach_hang_id - Filter by customer ID
 * @param {string} params.pet_name - Filter by pet name
 * @param {string} params.dich_vu_id - Filter by service ID
 * @param {string} params.dich_vu_name - Filter by service name
 * @param {string} params.trang_thai - Filter by status
 * @param {string} params.nhan_vien_id - Filter by staff ID
 * @param {string} params.from_date - Start date filter
 * @param {string} params.to_date - End date filter
 * @param {number} params.per_page - Items per page
 * @returns {Promise}
 */
export const getAllAppointments = async (params = {}) => {
  try {
    const response = await api.get("/lich-hen-all", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching all appointments:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch hẹn của khách hàng đang đăng nhập
 * @param {Object} params - Filter parameters
 * @returns {Promise}
 */
export const getMyAppointments = async (params = {}) => {
  try {
    const response = await api.get("/lich-hen", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching my appointments:", error);
    throw error;
  }
};

/**
 * Lấy chi tiết một lịch hẹn
 * @param {number} id - Appointment ID
 * @returns {Promise}
 */
export const getAppointmentById = async (id) => {
  try {
    const response = await api.get(`/lich-hen/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching appointment:", error);
    throw error;
  }
};

/**
 * Tạo lịch hẹn mới
 * @param {Object} data - Appointment data
 * @param {number} data.thu_cung_id - Pet ID (required)
 * @param {number} data.dich_vu_id - Service ID (required)
 * @param {string} data.ngay_gio - Appointment datetime (required)
 * @param {string} data.ghi_chu - Notes (optional)
 * @param {number} data.khach_hang_id - Customer ID (required for staff creating appointment)
 * @returns {Promise}
 */
export const createAppointment = async (data) => {
  try {
    const response = await api.post("/lich-hen", data);
    return response.data;
  } catch (error) {
    console.error("Error creating appointment:", error);
    throw error;
  }
};

/**
 * Cập nhật lịch hẹn (gán bác sĩ, cập nhật trạng thái, ghi chú)
 * @param {number} id - Appointment ID
 * @param {Object} data - Update data
 * @param {number} data.nhan_vien_id - Staff ID (optional)
 * @param {string} data.trang_thai - Status: pending|confirmed|in-progress|completed|cancelled (optional)
 * @param {string} data.ghi_chu - Notes (optional)
 * @returns {Promise}
 */
export const updateAppointment = async (id, data) => {
  try {
    const response = await api.put(`/lich-hen/${id}`, data);
    return response.data;
  } catch (error) {
    console.error("Error updating appointment:", error);
    throw error;
  }
};

/**
 * Xác nhận lịch hẹn (chuyển từ pending sang confirmed)
 * @param {number} id - Appointment ID
 * @returns {Promise}
 */
export const confirmAppointment = async (id) => {
  try {
    const response = await api.post(`/lich-hen/${id}/confirm`);
    return response.data;
  } catch (error) {
    console.error("Error confirming appointment:", error);
    throw error;
  }
};

/**
 * Cập nhật ngày giờ lịch hẹn
 * @param {number} id - Appointment ID
 * @param {Object} data - Update data
 * @param {string} data.ngay_gio - New datetime
 * @returns {Promise}
 */
export const updateAppointmentDateTime = async (id, data) => {
  try {
    const response = await api.put(`/lich-hen/${id}/ngay-gio`, data);
    return response.data;
  } catch (error) {
    console.error("Error updating appointment datetime:", error);
    throw error;
  }
};

/**
 * Xóa lịch hẹn
 * @param {number} id - Appointment ID
 * @returns {Promise}
 */
export const deleteAppointment = async (id) => {
  try {
    const response = await api.delete(`/lich-hen/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error deleting appointment:", error);
    throw error;
  }
};

/**
 * ========================================
 * CHECK-IN FUNCTIONS (Dành cho Y tá)
 * ========================================
 */

/**
 * Check-in lịch hẹn
 * Chỉ Y tá mới có thể thực hiện
 * @param {number} id - Appointment ID
 * @returns {Promise}
 */
export const checkInAppointment = async (id) => {
  try {
    const response = await api.post(`/lich-hen/${id}/check-in`);
    return response.data;
  } catch (error) {
    console.error("Error checking in appointment:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch hẹn chờ check-in
 * Lịch hẹn có trạng thái pending/confirmed và chưa check-in
 * @param {Object} params - Filter parameters
 * @param {string} params.ngay - Filter by date (format: YYYY-MM-DD), default: today
 * @param {number} params.per_page - Items per page
 * @returns {Promise}
 */
export const getAppointmentsWaitingCheckIn = async (params = {}) => {
  try {
    const response = await api.get("/lich-hen-cho-check-in", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching appointments waiting check-in:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch hẹn đã check-in
 * @param {Object} params - Filter parameters
 * @param {string} params.ngay - Filter by date (format: YYYY-MM-DD), default: today
 * @param {string} params.trang_thai - Filter by status
 * @param {number} params.per_page - Items per page
 * @returns {Promise}
 */
export const getCheckedInAppointments = async (params = {}) => {
  try {
    const response = await api.get("/lich-hen-da-check-in", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching checked-in appointments:", error);
    throw error;
  }
};

/**
 * ========================================
 * KHÁM BỆNH FUNCTIONS (Dành cho Bác sĩ)
 * ========================================
 */

/**
 * Lấy danh sách bệnh nhân chờ khám
 * Bệnh nhân đã check-in nhưng chưa bắt đầu khám
 * @param {Object} params - Filter parameters
 * @param {string} params.ngay - Filter by date (format: YYYY-MM-DD), default: today
 * @param {number} params.per_page - Items per page
 * @returns {Promise}
 */
export const getPatientsWaitingExam = async (params = {}) => {
  try {
    const response = await api.get("/benh-nhan-cho-kham", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching patients waiting exam:", error);
    throw error;
  }
};

/**
 * Bác sĩ bắt đầu khám bệnh
 * @param {number} id - Appointment ID
 * @returns {Promise}
 */
export const startExamination = async (id) => {
  try {
    const response = await api.post(`/lich-hen/${id}/bat-dau-kham`);
    return response.data;
  } catch (error) {
    console.error("Error starting examination:", error);
    throw error;
  }
};

/**
 * Lấy danh sách bệnh nhân đang khám
 * @param {Object} params - Filter parameters
 * @param {string} params.ngay - Filter by date (format: YYYY-MM-DD), default: today
 * @param {number} params.per_page - Items per page
 * @returns {Promise}
 */
export const getExaminingPatients = async (params = {}) => {
  try {
    const response = await api.get("/benh-nhan-dang-kham", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching examining patients:", error);
    throw error;
  }
};

/**
 * Hoàn thành khám bệnh
 * @param {number} id - Appointment ID
 * @param {Object} data - Completion data
 * @param {string} data.ghi_chu - Medical notes (optional)
 * @param {string} data.huong_dan - Treatment instructions (optional)
 * @returns {Promise}
 */
export const completeExamination = async (id, data = {}) => {
  try {
    const response = await api.post(`/lich-hen/${id}/hoan-thanh-kham`, data);
    return response.data;
  } catch (error) {
    console.error("Error completing examination:", error);
    throw error;
  }
};

export default {
  getAllAppointments,
  getMyAppointments,
  getAppointmentById,
  createAppointment,
  updateAppointment,
  confirmAppointment,
  updateAppointmentDateTime,
  deleteAppointment,
  checkInAppointment,
  getAppointmentsWaitingCheckIn,
  getCheckedInAppointments,
  getPatientsWaitingExam,
  startExamination,
  getExaminingPatients,
  completeExamination,
};
