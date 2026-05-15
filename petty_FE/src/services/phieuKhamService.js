import api from "@/utils/api";

export const getAll = async (params = {}) => {
  const response = await api.get("/phieu-kham", { params });
  return response.data;
};

export const getById = async (id) => {
  const response = await api.get(`/phieu-kham/${id}`);
  return response.data;
};

export const create = async (data) => {
  const response = await api.post("/phieu-kham", data);
  return response.data;
};

export const update = async (id, data) => {
  const response = await api.put(`/phieu-kham/${id}`, data);
  return response.data;
};

export const hoanTat = async (id) => {
  const response = await api.put(`/phieu-kham/${id}`, { trang_thai: "hoan_tat" });
  return response.data;
};

export const getByPet = async (thuCungId) => {
  const response = await api.get(`/phieu-kham/thu-cung/${thuCungId}`);
  return response.data;
};

export const getByDoctor = async (nhanVienId) => {
  const response = await api.get(`/phieu-kham/bac-si/${nhanVienId}`);
  return response.data;
};

export default {
  getAll,
  getById,
  create,
  update,
  hoanTat,
  getByPet,
  getByDoctor,
};
