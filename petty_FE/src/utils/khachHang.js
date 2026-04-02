import client from "./api";

const BASE = "/khach-hang";

export async function getKhachHangList(params = {}) {
  const res = await client.get(BASE, { params });
  return res.data;
}

export default {
  getKhachHangList,
};
