<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[1000] pt-24"
    @click.self="$emit('close')"
  >
    <div
      class="w-[512px] max-h-[85vh] bg-white rounded-[10px] overflow-hidden flex flex-col shadow-xl"
    >
      <div class="flex flex-col w-full h-full overflow-hidden p-6 gap-4">
        <!-- Header -->
        <div class="flex flex-col gap-2 shrink-0">
          <div class="flex items-center gap-2">
            <h2 class="font-semibold text-lg text-gray-900">
              Quản lý Danh Mục Dịch vụ
            </h2>
          </div>
          <p class="text-sm text-gray-600">
            Tạo và quản lý các danh mục dịch vụ để phân loại dịch vụ
          </p>
        </div>

        <!-- Categories List -->
        <div
          class="flex-1 bg-gray-50 border !border-gray-200 rounded-lg overflow-hidden flex flex-col min-h-0 p-4"
        >
          <div class="flex-1 overflow-y-auto space-y-2 custom-scrollbar">
            <div
              v-for="category in categories"
              :key="category.id"
              class="bg-white border border-gray-200 rounded-lg p-3 flex items-center justify-between hover:border-teal-600/30 hover:bg-teal-50/30 transition-all"
            >
              <div class="flex flex-col flex-1">
                <span class="font-normal text-base text-gray-900 leading-6">{{
                  category.name
                }}</span>
                <span class="text-xs text-gray-600"
                  >{{ category.description }} • {{ category.serviceCount }} dịch
                  vụ</span
                >
              </div>
              <button
                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                @click="handleDeleteCategory(category)"
                :disabled="deletingId === category.id"
                title="Xóa nhóm"
              >
                <TrashIcon v-if="deletingId !== category.id" class="w-4 h-4" />
                <span v-else class="text-xs text-gray-500">Đang xóa...</span>
              </button>
            </div>

            <!-- Empty State -->
            <div
              v-if="categories.length === 0"
              class="flex flex-col items-center justify-center h-full text-gray-400 py-8 gap-2"
            >
              <div class="w-12 h-12 flex items-center justify-center">
                <FolderIcon class="text-gray-400" />
              </div>
              <p class="text-sm">Chưa có danh mục nào</p>
            </div>
          </div>
        </div>

        <!-- Footer - Add New Category -->
        <div class="border-t border-gray-200 pt-4 shrink-0 space-y-2">
          <label class="font-nunito font-medium text-sm text-gray-900">
            Thêm nhóm mới
          </label>
          <div class="space-y-3">
            <input
              v-model="newCategoryName"
              type="text"
              placeholder="Tên nhóm..."
              class="w-full bg-gray-100 border-0 text-gray-700 text-sm rounded-lg focus:ring-teal-600 focus:border-teal-600 block p-2.5 font-nunito outline-none transition-all"
              @keyup.enter="handleAddCategory"
            />
            <input
              v-model="newCategoryDescription"
              type="text"
              placeholder="Mô tả (tùy chọn)..."
              class="w-full bg-gray-100 border-0 text-gray-700 text-sm rounded-lg focus:ring-teal-600 focus:border-teal-600 block p-2.5 font-nunito outline-none transition-all"
              @keyup.enter="handleAddCategory"
            />
            <button
              class="w-full py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg font-medium text-sm transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed font-nunito"
              :disabled="!newCategoryName.trim() || adding"
              @click="handleAddCategory"
            >
              <span v-if="!adding">Thêm nhóm</span>
              <span v-else>Đang tạo...</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api, { attachToken } from "@/utils/api";
import { showSuccessToast, showErrorToast, showInfoToast } from "@/utils/toast";
import { clearAuth } from "@/utils/auth";

// Icon SVG
import FolderIcon from "@/assets/svg/folder.svg";
import TrashIcon from "@/assets/svg/trash.svg";

const emit = defineEmits(["close", "addCategory", "deleteCategory"]);

// State
const newCategoryName = ref("");
const newCategoryDescription = ref("");
const categories = ref([]);
const loading = ref(false);
const adding = ref(false);
const deletingId = ref(null);

// Icon URLs from Figma
const iconFolder =
  "https://www.figma.com/api/mcp/asset/4f9adbdd-1893-41d2-8672-ca8be0ced444";
const iconDelete =
  "https://www.figma.com/api/mcp/asset/ee18260a-445b-40bb-a50c-dbaeb3bdbfc7";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/23574880-4357-4ba1-ab64-697061f1221b";

// Helpers
const normalize = (i) => ({
  id: i.id,
  name: i.ten_nhom ?? i.name ?? "",
  description: i.mo_ta ?? i.description ?? "Chưa có mô tả",
  serviceCount: i.service_count ?? 0,
});

const handleApiError = (e, defaultMsg = "Có lỗi xảy ra. Vui lòng thử lại.") => {
  console.error("API error", e);
  if (e && e.response) {
    if (e.response.status === 401) {
      showErrorToast(
        "Chưa xác thực",
        "Phiên đăng nhập đã hết hạn hoặc bạn chưa đăng nhập. Vui lòng đăng nhập lại."
      );
      try {
        clearAuth();
      } catch (_) {}
      window.location.href = "/admin/dang-nhap";
      return null;
    }

    const msg =
      e.response.data && e.response.data.message
        ? e.response.data.message
        : defaultMsg;
    showErrorToast("Lỗi", msg);
    return null;
  }

  showErrorToast("Lỗi", defaultMsg);
  return null;
};

// Fetch categories from backend
const fetchCategories = async () => {
  loading.value = true;
  try {
    const res = await api.get("/danh-muc-dich-vu");
    const items = (res && res.data && res.data.data) || [];
    categories.value = items.map(normalize);
  } catch (e) {
    handleApiError(e, "Không tải được danh mục dịch vụ. Vui lòng thử lại.");
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchCategories();
});

// Methods
const handleAddCategory = async () => {
  if (!newCategoryName.value.trim()) return;
  adding.value = true;
  try {
    const payload = {
      ten_nhom: newCategoryName.value.trim(),
      mo_ta: newCategoryDescription.value.trim() || null,
    };

    // ensure token (api client will attach token if present)
    attachToken();
    const res = await api.post("/danh-muc-dich-vu", payload);

    if (res && res.data && res.data.status) {
      const created = res.data.data;
      categories.value.unshift(normalize(created));
      showSuccessToast("Thành công", "Tạo danh mục dịch vụ mới thành công.");
      emit("addCategory", created);
      newCategoryName.value = "";
      newCategoryDescription.value = "";
    } else {
      const message =
        (res && res.data && res.data.message) || "Lỗi khi tạo danh mục.";
      showErrorToast("Lỗi", message);
    }
  } catch (e) {
    console.error("create category error", e);
    handleApiError(
      e,
      "Không thể tạo danh mục. Vui lòng kiểm tra quyền hoặc thử lại."
    );
  } finally {
    adding.value = false;
  }
};

const handleDeleteCategory = async (category) => {
  if (!confirm(`Bạn có chắc muốn xóa nhóm "${category.name}"?`)) return;

  deletingId.value = category.id;
  try {
    attachToken();
    const res = await api.delete(`/danh-muc-dich-vu/${category.id}`);
    if (res && res.data && res.data.status) {
      categories.value = categories.value.filter((c) => c.id !== category.id);
      showSuccessToast(
        "Thành công",
        res.data.message || "Xóa danh mục thành công."
      );
      emit("deleteCategory", category);
    } else {
      const message =
        (res && res.data && res.data.message) || "Lỗi khi xóa danh mục.";
      showErrorToast("Lỗi", message);
    }
  } catch (e) {
    handleApiError(e, "Có lỗi xảy ra khi xóa danh mục. Vui lòng thử lại.");
  } finally {
    deletingId.value = null;
  }
};
</script>

<style scoped>
/* Custom scrollbar for categories list */
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #d1d5db;
}
</style>
