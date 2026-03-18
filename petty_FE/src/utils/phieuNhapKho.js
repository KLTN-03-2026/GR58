import api from "./api";

/**
 * Lấy danh sách phiếu nhập kho
 */
export const getPhieuNhapKhos = async () => {
  try {
    const response = await api.get("/phieu-nhap-kho");
    return response.data;
  } catch (error) {
    console.error("Error fetching phieu nhap kho:", error);
    throw error;
  }
};

/**
 * Lấy thông tin chi tiết phiếu nhập kho
 */
export const getPhieuNhapKho = async (id) => {
  try {
    const response = await api.get(`/phieu-nhap-kho/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error fetching phieu nhap kho detail:", error);
    throw error;
  }
};

/**
 * Alias cho getPhieuNhapKho - lấy chi tiết phiếu nhập kho
 */
export const getChiTietPhieuNhapKho = getPhieuNhapKho;

/**
 * Tạo phiếu nhập kho mới
 */
export const createPhieuNhapKho = async (data) => {
  try {
    const response = await api.post("/phieu-nhap-kho", data);
    return response.data;
  } catch (error) {
    console.error("Error creating phieu nhap kho:", error);
    throw error;
  }
};

/**
 * Xóa phiếu nhập kho
 */
export const deletePhieuNhapKho = async (id) => {
  try {
    const response = await api.delete(`/phieu-nhap-kho/${id}`);
    return response.data;
  } catch (error) {
    console.error("Error deleting phieu nhap kho:", error);
    throw error;
  }
};

/**
 * Xuất PDF phiếu nhập kho
 * @param {number} id - ID của phiếu nhập kho
 * @returns {Promise<Blob>} File PDF
 */
export const exportPhieuNhapKhoPdf = async (id) => {
  try {
    const response = await api.get(`/phieu-nhap-kho/${id}/pdf`, {
      responseType: "blob", // Quan trọng: Để nhận file blob
    });
    return response.data;
  } catch (error) {
    console.error("Error exporting phieu nhap kho PDF:", error);
    throw error;
  }
};
