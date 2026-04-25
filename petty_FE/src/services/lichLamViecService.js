import api from "@/utils/api";

/**
 * Lấy lịch làm việc của bác sĩ đang đăng nhập
 * @param {string} startDate - Ngày bắt đầu (YYYY-MM-DD)
 * @param {string} endDate - Ngày kết thúc (YYYY-MM-DD)
 * @returns {Promise} Response data
 */
export const getMySchedule = async (startDate, endDate) => {
  try {
    // ✅ FIX: Sử dụng endpoint /lich-lam-viec/cua-toi (getMySchedule từ backend)
    const response = await api.get("/lich-lam-viec/cua-toi", {
      params: {
        start_date: startDate,
        end_date: endDate,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching my schedule:", error);
    throw error;
  }
};

/**
 * Lấy lịch hôm nay của bác sĩ đang đăng nhập
 * @returns {Promise} Response data
 */
export const getMyTodaySchedule = async () => {
  try {
    // ✅ FIX: Sử dụng endpoint /lich-lam-viec/cua-toi/hom-nay (getMyTodaySchedule từ backend)
    const response = await api.get("/lich-lam-viec/cua-toi/hom-nay");
    return response.data;
  } catch (error) {
    console.error("Error fetching my today schedule:", error);
    throw error;
  }
};

/**
 * Lấy lịch làm việc của một bác sĩ/nhân viên cụ thể (cho admin)
 * @param {number} nhanVienId - ID của nhân viên
 * @param {string} startDate - Ngày bắt đầu (YYYY-MM-DD)
 * @param {string} endDate - Ngày kết thúc (YYYY-MM-DD)
 * @returns {Promise} Response data
 */
export const getScheduleByDoctor = async (nhanVienId, startDate, endDate) => {
  try {
    // ✅ FIX: Sử dụng endpoint getScheduleByDoctor từ backend
    const response = await api.get(`/lich-lam-viec/bac-si/${nhanVienId}`, {
      params: {
        start_date: startDate,
        end_date: endDate,
      },
    });
    return response.data;
  } catch (error) {
    console.error("Error fetching doctor schedule:", error);
    throw error;
  }
};

/**
 * Lấy danh sách tất cả bác sĩ/nhân viên với lịch làm việc trong ngày hôm nay (Admin only)
 * @param {string} date - Ngày cần xem (YYYY-MM-DD), mặc định là hôm nay
 * @returns {Promise} Response data
 */
export const getTodaySchedule = async (date = null) => {
  try {
    const params = {};
    if (date) {
      params.date = date;
    }

    // ✅ FIX: Sử dụng endpoint /lich-lam-viec/hom-nay (getTodaySchedule từ backend)
    const response = await api.get("/lich-lam-viec/hom-nay", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching today schedule:", error);
    throw error;
  }
};

/**
 * Lấy danh sách lịch làm việc với bộ lọc
 * @param {Object} filters - Bộ lọc {ngay_lam, nhan_vien_id, thoi_gian_truc}
 * @param {number} perPage - Số bản ghi mỗi trang
 * @returns {Promise} Response data
 */
export const getScheduleList = async (filters = {}, perPage = 20) => {
  try {
    const params = {
      per_page: perPage,
      ...filters,
    };

    const response = await api.get("/lich-lam-viec", { params });
    return response.data;
  } catch (error) {
    console.error("Error fetching schedule list:", error);
    throw error;
  }
};

/**
 * Lấy chi tiết một lịch làm việc
 * @param {number} id - ID của lịch làm việc
 * @returns {Promise} Response data
 */
export const getScheduleDetail = async (id) => {
  try {
    const response = await api.get(`/lich-lam-viec/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching schedule detail:", error);
    throw error;
  }
};

/**
 * Tạo mới lịch làm việc
 * @param {Object} data - Dữ liệu lịch làm việc
 * @returns {Promise} Response data
 */
export const createSchedule = async (data) => {
  try {
    const response = await api.post("/lich-lam-viec", data);
    return response.data;
  } catch (error) {
    console.error("Error creating schedule:", error);
    throw error;
  }
};

export const generateSchedule = async (year, month, force = false) => {
  const res = await api.post("/lich-lam-viec/generate", { year, month, force });
  return res.data;
};

export const getAvailableSlots = async (date) => {
  const res = await api.get("/lich-hen/available-slots", { params: { date } });
  return res.data;
};

export default {
  getMySchedule,
  getMyTodaySchedule,
  getScheduleByDoctor,
  getTodaySchedule,
  getScheduleList,
  getScheduleDetail,
  createSchedule,
  generateSchedule,
  getAvailableSlots,
};
