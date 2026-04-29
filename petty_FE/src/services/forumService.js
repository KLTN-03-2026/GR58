import api from "@/utils/api";

export const forumService = {
  async getPosts(params) {
    const res = await api.get("/bai-viet", { params });
    return res.data;
  },

  async getPostById(id) {
    const res = await api.get(`/bai-viet/${id}`);
    return res.data;
  },

  async getCategories() {
    const res = await api.get("/phan-loai-bai-viet");
    return res.data;
  },

  async getComments(postId, params) {
    const res = await api.get(`/bai-viet/${postId}/binh-luan`, { params });
    return res.data;
  },

  async createComment(postId, data) {
    const res = await api.post(`/bai-viet/${postId}/binh-luan`, data);
    return res.data;
  },

  async deleteComment(id) {
    const res = await api.delete(`/binh-luan/${id}`);
    return res.data;
  },

  async toggleReaction(data) {
    // data should contain reactionable_id, reactionable_type ('bai_viet' or 'binh_luan'), and type ('like' or 'dislike')
    const res = await api.post("/reactions/toggle", data);
    return res.data;
  },
};

export default forumService;
