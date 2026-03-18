<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-black">Quản lý Bài viết</h1>
        <p class="text-gray-500 font-medium text-base">
          Tạo và quản lý nội dung cho website
        </p>
      </div>
      <button
        @click="router.push('/admin/bai-viet/them-moi')"
        class="bg-[#5a9690] text-white rounded-lg px-4 py-2 h-9 flex items-center gap-2 text-sm font-medium hover:bg-[#5a9690]/80 transition-colors"
      >
        <AddIcon class="w-4 h-4" />
        Viết bài mới
      </button>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div class="flex flex-col gap-4">
        <!-- Search Bar -->
        <div class="relative">
          <SearchIcon class="absolute left-3 top-2 w-5 h-5" />
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Tìm theo tiêu đề bài viết, tên tác giả..."
            class="w-full h-9 pl-10 pr-3 py-1 bg-[#f3f3f5] border-0 rounded-lg text-sm text-[#717182] focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
        </div>

        <!-- Filters -->
        <div class="grid grid-cols-2 gap-4">
          <!-- Category Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tất cả danh mục</span>
            <ChevronDownIcon />
          </button>

          <!-- Status Filter -->
          <button
            class="bg-[#f3f3f5] border-0 rounded-lg px-3 py-1 flex items-center justify-between h-9 hover:bg-gray-200 transition-colors"
          >
            <span class="text-sm text-neutral-950">Tất cả</span>
            <ChevronDownIcon />
          </button>
        </div>
      </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6">
      <div v-if="isLoading" class="text-center py-8">
        <p class="text-gray-500">Đang tải dữ liệu...</p>
      </div>
      <div v-else-if="filteredPosts.length === 0" class="text-center py-8">
        <p class="text-gray-500">Không có bài viết nào</p>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Tiêu đề & Tóm tắt
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Danh mục
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Ngày đăng
              </th>
              <th
                class="text-left text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Trạng thái
              </th>
              <th
                class="text-right text-sm font-medium text-neutral-950 py-2.5 px-2"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="post in filteredPosts"
              :key="post.id"
              class="border-b border-black/10 hover:bg-gray-50 transition-colors"
            >
              <!-- Title & Summary -->
              <td class="py-4 px-2">
                <div class="flex flex-col gap-1">
                  <p class="text-sm text-[#101828] leading-5">
                    {{ post.title }}
                  </p>
                  <p
                    class="text-sm text-[#6a7282] leading-5 truncate max-w-[500px]"
                  >
                    {{ post.summary }}
                  </p>
                </div>
              </td>

              <!-- Category -->
              <td class="py-4 px-2">
                <span
                  class="inline-block px-3 py-1 rounded-lg border text-xs font-medium"
                  :class="getCategoryClass(post.category)"
                >
                  {{ post.categoryLabel }}
                </span>
              </td>

              <!-- Date -->
              <td class="py-4 px-2">
                <div v-if="post.publishedAt" class="flex flex-col gap-1">
                  <p class="text-sm text-[#101828] leading-5">
                    {{ post.publishedDate }}
                  </p>
                  <p class="text-xs text-[#6a7282] leading-4">
                    {{ post.publishedTime }}
                  </p>
                </div>
                <p v-else class="text-sm text-[#99a1af] leading-5">
                  Chưa xuất bản
                </p>
              </td>

              <!-- Status -->
              <td class="py-4 px-2">
                <button
                  class="inline-block px-3 py-1 rounded-lg border-0 text-xs font-medium transition-opacity hover:opacity-80"
                  :class="getStatusClass(post.status)"
                  @click="toggleStatus(post)"
                >
                  {{ post.statusLabel }}
                </button>
              </td>

              <!-- Actions -->
              <td class="py-4 px-2">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="router.push(`/admin/bai-viet/chinh-sua/${post.id}`)"
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Chỉnh sửa"
                  >
                    <UpdateIcon class="w-4 h-4 text-[#009689]" />
                  </button>
                  <button
                    @click="handleDeletePost(post)"
                    class="w-9 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
                    title="Xóa"
                  >
                    <TrashIcon class="w-4 h-4 text-[#e7000b]" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modals -->
    <XoaBaiViet
      v-if="isDeletePostModalOpen"
      :post-title="selectedPostForDelete?.title"
      @close="isDeletePostModalOpen = false"
      @confirm="handleDeleteConfirm"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import XoaBaiViet from "./XoaBaiViet/index.vue";
import client from "../../../../utils/api.js";
// Icon SVG
import AddIcon from "@/assets/svg/add.svg";
import SearchIcon from "@/assets/svg/search.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import UpdateIcon from "@/assets/svg/update.svg";
import TrashIcon from "@/assets/svg/trash.svg";

// Router
const router = useRouter();

// Search query
const searchQuery = ref("");
const isDeletePostModalOpen = ref(false);
const selectedPostForDelete = ref(null);

// State
const posts = ref([]);
const categories = ref([]);
const isLoading = ref(false);
const selectedCategoryFilter = ref(null);
const selectedStatusFilter = ref(null);

// Fetch articles from API
const fetchPosts = async () => {
  isLoading.value = true;
  try {
    const response = await client.get("/bai-viet");
    console.log("🔍 API Response:", response.data);

    if (response.data.status) {
      // Transform API data to match UI format
      posts.value = (response.data.data || []).map((post) => {
        console.log("📝 Processing post:", {
          id: post.id,
          title: post.ten_bai_viet,
          phan_loai: post.phan_loai,
          phan_loai_bai_viet_id: post.phan_loai_bai_viet_id,
        });

        const transformedPost = {
          id: post.id,
          title: post.ten_bai_viet,
          summary: post.mo_ta || "Không có mô tả",
          category:
            post.phan_loai_bai_viet_id || post.phan_loai?.id || "unknown",
          categoryLabel: post.phan_loai?.ten_phan_loai || "Không xác định",
          publishedAt: post.created_at,
          publishedDate: formatDate(post.created_at),
          publishedTime: formatTime(post.created_at),
          status: post.trang_thai || "published",
          statusLabel: getStatusLabel(post.trang_thai),
          views: 0,
          author: post.nhan_vien?.ho_va_ten || "Không xác định",
        };

        console.log(`✅ Post ${post.id} transformed:`, transformedPost);
        return transformedPost;
      });
      console.log("✅ Total posts loaded:", posts.value.length);
      console.log("📊 All posts:", posts.value);
    } else {
      console.error("❌ Failed to fetch posts:", response.data.message);
    }
  } catch (error) {
    console.error("❌ Error fetching posts:", error);
    console.error("❌ Error details:", error.response?.data);
  } finally {
    isLoading.value = false;
  }
};

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await client.get("/phan-loai-bai-viet");
    if (response.data.status) {
      categories.value = response.data.data || [];
      console.log("✅ Categories loaded:", categories.value);
    } else {
      console.error("❌ Failed to fetch categories:", response.data.message);
    }
  } catch (error) {
    console.error("❌ Error fetching categories:", error);
  }
};

// Format date
const formatDate = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleDateString("vi-VN");
};

// Format time
const formatTime = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return date.toLocaleTimeString("vi-VN", {
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Get status label
const getStatusLabel = (status) => {
  const labels = {
    published: "Đã xuất bản",
    draft: "Bản nháp",
    hidden: "Đã ẩn",
  };
  return labels[status] || "Không xác định";
};

// Filtered posts (search + filter)
const filteredPosts = computed(() => {
  let result = posts.value;

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(
      (post) =>
        post.title.toLowerCase().includes(query) ||
        post.summary.toLowerCase().includes(query) ||
        post.author.toLowerCase().includes(query)
    );
  }

  // Filter by category
  if (selectedCategoryFilter.value) {
    result = result.filter(
      (post) => post.category === selectedCategoryFilter.value
    );
  }

  // Filter by status
  if (selectedStatusFilter.value) {
    result = result.filter(
      (post) => post.status === selectedStatusFilter.value
    );
  }

  return result;
});

// Get category badge class
const getCategoryClass = (categoryId) => {
  const colors = {
    1: "bg-blue-100 border-black/10 text-[#1447e6]",
    2: "bg-[#ffe2e2] border-black/10 text-[#c10007]",
    3: "bg-gray-100 border-black/10 text-[#364153]",
  };
  // Default color if categoryId not found
  return colors[categoryId] || "bg-gray-100 border-black/10 text-gray-700";
};

// Get status badge class
const getStatusClass = (status) => {
  const classes = {
    published: "bg-green-100 text-[#008236]",
    draft: "bg-gray-100 text-[#364153]",
    hidden: "bg-[#ffe2e2] text-[#c10007]",
  };
  return classes[status] || "bg-gray-100 text-gray-700";
};

const handleDeletePost = (post) => {
  selectedPostForDelete.value = post;
  isDeletePostModalOpen.value = true;
};

const handleDeleteConfirm = async () => {
  try {
    const response = await client.delete(
      `/bai-viet/${selectedPostForDelete.value?.id}`
    );
    if (response.data.status) {
      console.log("✅ Post deleted");
      await fetchPosts();
    } else {
      console.error("❌ Failed to delete post");
    }
  } catch (error) {
    console.error("❌ Error deleting post:", error);
  } finally {
    isDeletePostModalOpen.value = false;
    selectedPostForDelete.value = null;
  }
};

const toggleStatus = async (post) => {
  const newStatus = post.status === "published" ? "hidden" : "published";
  try {
    const response = await client.patch(`/bai-viet/${post.id}`, {
      trang_thai: newStatus,
    });
    if (response.data.status) {
      post.status = newStatus;
      post.statusLabel = getStatusLabel(newStatus);
      console.log("✅ Status updated");
    }
  } catch (error) {
    console.error("❌ Error updating status:", error);
  }
};

// Lifecycle
onMounted(() => {
  fetchPosts();
  fetchCategories();
});
</script>

<style scoped>
/* Ensure Nunito Sans font is applied */
.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
