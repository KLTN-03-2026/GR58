import api from "@/utils/api";

export const getMyLeaves = async () => {
  const res = await api.get("/lich-nghi");
  return res.data;
};

export const requestLeave = async (data) => {
  const res = await api.post("/lich-nghi", data);
  return res.data;
};

export const cancelLeave = async (id) => {
  const res = await api.delete(`/lich-nghi/${id}`);
  return res.data;
};

export const getAdminLeaves = async (params = {}) => {
  const res = await api.get("/admin/lich-nghi", { params });
  return res.data;
};

export const approveLeave = async (id) => {
  const res = await api.patch(`/admin/lich-nghi/${id}/duyet`);
  return res.data;
};

export const rejectLeave = async (id, ly_do) => {
  const res = await api.patch(`/admin/lich-nghi/${id}/tu-choi`, { ly_do });
  return res.data;
};

export const createAdminLeave = async (data) => {
  const res = await api.post("/admin/lich-nghi", data);
  return res.data;
};

export default {
  getMyLeaves,
  requestLeave,
  cancelLeave,
  getAdminLeaves,
  approveLeave,
  rejectLeave,
  createAdminLeave,
};
