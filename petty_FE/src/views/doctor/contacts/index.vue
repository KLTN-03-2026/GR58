<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-2xl font-semibold text-black">Quản lý Liên hệ</h1>
      <p class="text-base font-medium text-gray-500">
        Xem và phản hồi yêu cầu hỗ trợ từ khách hàng
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 mb-6">
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm cursor-pointer transition-all hover:shadow-md"
        :class="{ 'border-[#155dfc] border-2': activeFilter === 'all' }"
        @click="activeFilter = 'all'"
      >
        <p class="text-sm text-gray-600">Tất cả</p>
        <p class="text-2xl font-bold text-[#155dfc]">{{ stats.total }}</p>
      </div>
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm cursor-pointer transition-all hover:shadow-md"
        :class="{ 'border-[#f54900] border-2': activeFilter === 'chua_xu_ly' }"
        @click="activeFilter = 'chua_xu_ly'"
      >
        <p class="text-sm text-gray-600">Chưa xử lý</p>
        <p class="text-2xl font-bold text-[#f54900]">{{ stats.chua_xu_ly }}</p>
      </div>
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm cursor-pointer transition-all hover:shadow-md"
        :class="{ 'border-[#00a63e] border-2': activeFilter === 'dang_xu_ly' }"
        @click="activeFilter = 'dang_xu_ly'"
      >
        <p class="text-sm text-gray-600">Đang xử lý</p>
        <p class="text-2xl font-bold text-[#00a63e]">{{ stats.dang_xu_ly }}</p>
      </div>
      <div
        class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm cursor-pointer transition-all hover:shadow-md"
        :class="{ 'border-[#9810fa] border-2': activeFilter === 'da_xu_ly' }"
        @click="activeFilter = 'da_xu_ly'"
      >
        <p class="text-sm text-gray-600">Đã xử lý</p>
        <p class="text-2xl font-bold text-[#9810fa]">{{ stats.da_xu_ly }}</p>
      </div>
    </div>

    <!-- Search -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm mb-6"
    >
      <div class="relative">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Tìm theo tên, email, chủ đề..."
          class="w-full h-11 bg-gray-50 border !border-gray-300 rounded-lg px-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
        />
      </div>
    </div>

    <!-- Contact List -->
    <div class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm">
      <h2 class="text-base font-semibold text-black mb-6">
        Danh sách yêu cầu - {{ filteredContacts.length }} yêu cầu
      </h2>

      <!-- Loading -->
      <div v-if="loading" class="flex justify-center items-center py-12">
        <span class="text-gray-500">Đang tải dữ liệu...</span>
      </div>

      <!-- Empty State -->
      <div v-else-if="filteredContacts.length === 0" class="flex flex-col items-center justify-center py-12">
        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
        </svg>
        <p class="text-gray-500">Không có yêu cầu nào</p>
      </div>

      <!-- Contact Cards -->
      <div v-else class="space-y-4">
        <div
          v-for="contact in paginatedContacts"
          :key="contact.id"
          class="border !border-gray-200 rounded-lg p-4 hover:shadow-md transition-all cursor-pointer"
          :class="{
            'border-l-4 border-l-[#f54900]': contact.trang_thai === 'chua_xu_ly',
            'border-l-4 border-l-[#00a63e]': contact.trang_thai === 'dang_xu_ly',
            'border-l-4 border-l-[#9810fa]': contact.trang_thai === 'da_xu_ly',
          }"
          @click="openDetail(contact)"
        >
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <h3 class="font-semibold text-black">{{ contact.ho_ten }}</h3>
                <span
                  class="px-2 py-0.5 rounded-full text-xs font-medium"
                  :class="{
                    'bg-orange-100 text-orange-700': contact.trang_thai === 'chua_xu_ly',
                    'bg-green-100 text-green-700': contact.trang_thai === 'dang_xu_ly',
                    'bg-purple-100 text-purple-700': contact.trang_thai === 'da_xu_ly',
                  }"
                >
                  {{ getStatusLabel(contact.trang_thai) }}
                </span>
              </div>
              <p class="text-sm text-gray-600 mb-1">
                <span class="font-medium">Chủ đề:</span> {{ contact.chu_de }}
              </p>
              <p class="text-sm text-gray-500 line-clamp-2">{{ contact.noi_dung }}</p>
              <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                <span>{{ contact.email }}</span>
                <span v-if="contact.so_dien_thoai">{{ contact.so_dien_thoai }}</span>
                <span>{{ formatDate(contact.created_at) }}</span>
              </div>
            </div>
            <button
              class="ml-4 p-2 text-gray-400 hover:text-[#009689] transition-colors"
              @click.stop="openDetail(contact)"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex justify-center items-center gap-2 mt-6">
        <button
          class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          <
        </button>
        <span class="text-sm text-gray-600">
          Trang {{ currentPage }} / {{ totalPages }}
        </span>
        <button
          class="px-3 py-1 rounded border hover:bg-gray-100 disabled:opacity-50"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          >
        </button>
      </div>
    </div>

    <!-- Detail Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
      @click.self="showModal = false"
    >
      <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-hidden shadow-xl">
        <!-- Modal Header -->
        <div class="bg-[#009689] text-white px-6 py-4 flex items-center justify-between">
          <h3 class="text-lg font-semibold">Chi tiết yêu cầu</h3>
          <button @click="showModal = false" class="hover:opacity-80">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="text-sm font-medium text-gray-500">Họ tên</label>
                <p class="font-medium">{{ selectedContact?.ho_ten }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Email</label>
                <p class="font-medium">{{ selectedContact?.email }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Số điện thoại</label>
                <p class="font-medium">{{ selectedContact?.so_dien_thoai || 'Không có' }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-500">Ngày gửi</label>
                <p class="font-medium">{{ formatDate(selectedContact?.created_at) }}</p>
              </div>
            </div>

            <div>
              <label class="text-sm font-medium text-gray-500">Chủ đề</label>
              <p class="font-medium">{{ selectedContact?.chu_de }}</p>
            </div>

            <div>
              <label class="text-sm font-medium text-gray-500">Nội dung</label>
              <p class="bg-gray-50 rounded-lg p-3">{{ selectedContact?.noi_dung }}</p>
            </div>

            <div>
              <label class="text-sm font-medium text-gray-500">Trạng thái</label>
              <select
                v-model="updateForm.trang_thai"
                class="w-full h-10 border !border-gray-300 rounded-lg px-3 mt-1"
              >
                <option value="chua_xu_ly">Chưa xử lý</option>
                <option value="dang_xu_ly">Đang xử lý</option>
                <option value="da_xu_ly">Đã xử lý</option>
              </select>
            </div>

            <div>
              <label class="text-sm font-medium text-gray-500">Phản hồi</label>
              <textarea
                v-model="updateForm.phan_hoi"
                rows="4"
                class="w-full border !border-gray-300 rounded-lg px-3 py-2 mt-1"
                placeholder="Nhập phản hồi..."
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="px-6 py-4 border-t flex justify-end gap-3">
          <button
            class="px-4 py-2 rounded-lg border hover:bg-gray-100 transition-colors"
            @click="showModal = false"
          >
            Đóng
          </button>
          <button
            class="px-4 py-2 rounded-lg bg-[#009689] text-white hover:bg-[#007d72] transition-colors"
            :disabled="saving"
            @click="saveResponse"
          >
            {{ saving ? 'Đang lưu...' : 'Lưu phản hồi' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import hoTroService from "@/services/hoTroService";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

const loading = ref(false);
const saving = ref(false);
const contacts = ref([]);
const searchQuery = ref("");
const activeFilter = ref("all");
const showModal = ref(false);
const selectedContact = ref(null);

const updateForm = ref({
  trang_thai: "",
  phan_hoi: "",
});

const currentPage = ref(1);
const perPage = 10;

const stats = computed(() => {
  return {
    total: contacts.value.length,
    chua_xu_ly: contacts.value.filter(c => c.trang_thai === "chua_xu_ly").length,
    dang_xu_ly: contacts.value.filter(c => c.trang_thai === "dang_xu_ly").length,
    da_xu_ly: contacts.value.filter(c => c.trang_thai === "da_xu_ly").length,
  };
});

const filteredContacts = computed(() => {
  let result = contacts.value;

  if (activeFilter.value !== "all") {
    result = result.filter(c => c.trang_thai === activeFilter.value);
  }

  if (searchQuery.value) {
    const q = searchQuery.value.toLowerCase();
    result = result.filter(c =>
      c.ho_ten?.toLowerCase().includes(q) ||
      c.email?.toLowerCase().includes(q) ||
      c.chu_de?.toLowerCase().includes(q) ||
      c.noi_dung?.toLowerCase().includes(q)
    );
  }

  return result;
});

const totalPages = computed(() => Math.ceil(filteredContacts.value.length / perPage));

const paginatedContacts = computed(() => {
  const start = (currentPage.value - 1) * perPage;
  return filteredContacts.value.slice(start, start + perPage);
});

async function fetchContacts() {
  loading.value = true;
  try {
    const res = await hoTroService.getAll();
    console.log("API response:", res);
    contacts.value = res.data || [];
    console.log("Contacts loaded:", contacts.value.length);
  } catch (error) {
    console.error("Lỗi khi tải danh sách:", error);
    console.error("Error response:", error.response?.data);
    showErrorToast("Lỗi", "Không thể tải danh sách liên hệ");
  } finally {
    loading.value = false;
  }
}

function openDetail(contact) {
  selectedContact.value = contact;
  updateForm.value = {
    trang_thai: contact.trang_thai || "chua_xu_ly",
    phan_hoi: contact.phan_hoi || "",
  };
  showModal.value = true;
}

async function saveResponse() {
  saving.value = true;
  try {
    await hoTroService.update(selectedContact.value.id, updateForm.value);
    showSuccessToast("Thành công", "Đã lưu phản hồi");
    showModal.value = false;
    fetchContacts();
  } catch (error) {
    console.error("Lỗi khi lưu:", error);
    showErrorToast("Lỗi", "Không thể lưu phản hồi");
  } finally {
    saving.value = false;
  }
}

function getStatusLabel(status) {
  const labels = {
    chua_xu_ly: "Chưa xử lý",
    dang_xu_ly: "Đang xử lý",
    da_xu_ly: "Đã xử lý",
  };
  return labels[status] || status;
}

function formatDate(date) {
  if (!date) return "";
  return new Date(date).toLocaleDateString("vi-VN", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}

onMounted(() => {
  fetchContacts();
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>