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
              Quản lý Danh Mục Hàng Hoá
            </h2>
          </div>
          <p class="text-sm text-gray-600">
            Tạo và quản lý các danh mục để phân loại hàng hóa trong kho
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
                <span class="text-xs text-gray-600">{{
                  category.description
                }}</span>
              </div>
              <button
                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                @click="handleDeleteCategory(category.id)"
                :disabled="deleting.has(category.id)"
                title="Xóa nhóm"
              >
                <TrashIcon v-if="!deleting.has(category.id)" class="w-4 h-4" />
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
            Thêm danh mục mới
          </label>
          <div class="space-y-3">
            <input
              v-model="newCategoryName"
              type="text"
              placeholder="Tên danh mục..."
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
              :disabled="!newCategoryName.trim() || saving"
              @click="handleAddCategory"
            >
              <span v-if="!saving">Thêm danh mục</span>
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
import {
  listDanhMucHangHoa,
  createDanhMucHangHoa,
  deleteDanhMucHangHoa,
} from "@/utils/danhMucHangHoa";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

// Icon SVG
import FolderIcon from "@/assets/svg/folder.svg";
import TrashIcon from "@/assets/svg/trash.svg";

// Emits
const emit = defineEmits(["close", "add", "delete"]);

// State
const newCategoryName = ref("");
const newCategoryDescription = ref("");
const categories = ref([]);
const loading = ref(false);
const saving = ref(false);
const deleting = ref(new Set());

async function loadCategories() {
  loading.value = true;
  try {
    const data = await listDanhMucHangHoa();
    // backend returns array of objects; adapt if shape differs
    // normalize backend keys to frontend-friendly keys
    const map = (b) => ({
      id: b.id ?? b._id ?? null,
      name: b.ten_danh_muc_hang_hoa || b.name || b.ten || "",
      description: b.mo_ta || b.description || b.mota || "Chưa có mô tả",
      count: b.count ?? b.so_luong ?? 0,
      // keep raw payload in case other fields needed
      raw: b,
    });
    categories.value = Array.isArray(data) ? data.map(map) : [];
  } catch (err) {
    console.error("Failed to load categories", err);
    showErrorToast("Lỗi", "Không thể tải danh mục hàng hoá");
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  loadCategories();
});

const handleAddCategory = async () => {
  if (!newCategoryName.value.trim()) return;
  saving.value = true;
  try {
    const payload = {
      name: newCategoryName.value.trim(),
      description: newCategoryDescription.value.trim() || null,
    };
    const created = await createDanhMucHangHoa(payload);
    if (created) {
      // normalize created item
      const mapped = {
        id: created.id ?? created._id ?? null,
        name:
          created.ten_danh_muc_hang_hoa || created.name || created.ten || "",
        description:
          created.mo_ta ||
          created.description ||
          created.mota ||
          "Chưa có mô tả",
        count: created.count ?? created.so_luong ?? 0,
        raw: created,
      };
      categories.value.unshift(mapped);
      emit("add", created);
      showSuccessToast("Thành công", "Tạo danh mục hàng hóa thành công");
      // reset
      newCategoryName.value = "";
      newCategoryDescription.value = "";
    } else {
      showErrorToast("Lỗi", "Không thể tạo danh mục");
    }
  } catch (err) {
    console.error("Create error", err);
    // Prefer validation error messages from backend if present
    const resp = err?.response?.data;
    let msg = resp?.message || "Không thể tạo danh mục";
    if (resp && resp.errors) {
      // resp.errors is an object with arrays of messages, pick the first
      const firstKey = Object.keys(resp.errors)[0];
      if (firstKey && resp.errors[firstKey] && resp.errors[firstKey].length)
        msg = resp.errors[firstKey][0];
    }
    showErrorToast("Lỗi", msg);
  } finally {
    saving.value = false;
  }
};

const handleDeleteCategory = async (categoryId) => {
  if (!confirm("Bạn có chắc muốn xóa danh mục này?")) return;
  deleting.value.add(categoryId);
  try {
    await deleteDanhMucHangHoa(categoryId);
    const idx = categories.value.findIndex((c) => c.id === categoryId);
    if (idx !== -1) categories.value.splice(idx, 1);
    emit("delete", categoryId);
    showSuccessToast("Đã xóa", "Xóa danh mục thành công");
  } catch (err) {
    console.error("Delete error", err);
    const msg = err?.response?.data?.message || "Không thể xóa danh mục";
    showErrorToast("Lỗi", msg);
  } finally {
    deleting.value.delete(categoryId);
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
