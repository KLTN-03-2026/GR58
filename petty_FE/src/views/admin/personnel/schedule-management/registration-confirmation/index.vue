<template>
  <!-- Modal Overlay -->
  <div class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4">
    <!-- Modal Content -->
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl overflow-hidden animate-slide-up max-h-[90vh] flex flex-col">
      <!-- Header -->
      <div class="bg-gradient-to-r from-teal-500 to-teal-600 px-8 py-6 flex items-center justify-between shrink-0">
        <div>
          <h2 class="text-2xl font-bold text-white">Chi tiết đơn xin nghỉ</h2>
          <p class="text-teal-100 text-sm mt-1">Xem và duyệt đơn xin nghỉ phép của nhân viên</p>
        </div>
        <button @click="$emit('close')" class="text-teal-100 hover:text-white transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="p-8 overflow-y-auto flex-1">
        <!-- Staff Info -->
        <div class="flex items-center gap-6 mb-8 pb-8 border-b border-gray-200">
          <img :src="confirmation.avatar" :alt="confirmation.staffName" class="w-20 h-20 rounded-full object-cover border-4 border-teal-100" />
          <div class="flex-1">
            <h3 class="text-2xl font-bold text-gray-900">{{ confirmation.staffName }}</h3>
            <p class="text-gray-600 mt-1">{{ confirmation.role }}</p>
            <p class="text-sm text-gray-500 mt-3">ID: {{ confirmation.id }}</p>
          </div>
          <div :class="[
            'px-6 py-3 rounded-lg font-bold text-white',
            confirmation.status === 'cho_duyet' ? 'bg-amber-500' : confirmation.status === 'da_duyet' ? 'bg-emerald-500' : 'bg-red-500',
          ]">
            {{ confirmation.status === 'cho_duyet' ? 'Chờ duyệt' : confirmation.status === 'da_duyet' ? 'Đã duyệt' : 'Từ chối' }}
          </div>
        </div>

        <!-- Shift Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="space-y-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Thông tin nghỉ</h4>
            <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-4 border border-blue-200">
              <p class="text-sm text-blue-600 font-bold mb-1">Ngày xin nghỉ</p>
              <p class="text-2xl font-bold text-blue-900">{{ confirmation.date }}</p>
            </div>
          </div>
          <div class="space-y-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">Chi tiết</h4>
            <div :class="[
              'rounded-lg p-4 border',
              confirmation.loaiNghi === 'co_luong' ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'
            ]">
              <p :class="['text-sm font-bold mb-1', confirmation.loaiNghi === 'co_luong' ? 'text-emerald-600' : 'text-red-600']">Loại nghỉ</p>
              <p :class="['text-xl font-bold', confirmation.loaiNghi === 'co_luong' ? 'text-emerald-900' : 'text-red-900']">
                {{ confirmation.loaiNghi === 'co_luong' ? 'Nghỉ có lương (Trong quota)' : 'Nghỉ không lương (Vượt quota)' }}
              </p>
            </div>
          </div>
        </div>

        <!-- Note Section -->
        <div class="mb-8">
          <h4 class="text-lg font-bold text-gray-900 mb-3">Lý do xin nghỉ</h4>
          <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <p class="text-gray-700">{{ confirmation.note || 'Không có lý do' }}</p>
          </div>
        </div>

        <!-- Suggestions Section -->
        <div v-if="confirmation.status === 'cho_duyet'" class="mb-8">
          <div class="flex items-center justify-between mb-3">
            <h4 class="text-lg font-bold text-gray-900">Gợi ý nhân viên thay thế ca</h4>
            <span v-if="loadingSuggestions" class="text-sm text-teal-600 animate-pulse">Đang tìm kiếm...</span>
          </div>
          
          <div v-if="loadingSuggestions" class="bg-gray-50 rounded-lg p-8 border border-gray-200 flex justify-center">
            <div class="w-8 h-8 border-4 border-teal-500 border-t-transparent rounded-full animate-spin"></div>
          </div>
          <div v-else-if="suggestions.length === 0" class="bg-amber-50 rounded-lg p-4 border border-amber-200 text-amber-800 text-center">
            Không tìm thấy nhân viên nào phù hợp để thay thế.
          </div>
          <div v-else class="space-y-3">
            <div v-for="nv in suggestions" :key="nv.id" class="bg-white border border-gray-200 rounded-lg p-4 flex items-center justify-between hover:border-teal-300 transition-colors">
              <div class="flex items-center gap-3">
                <img :src="nv.avatar" class="w-10 h-10 rounded-full bg-gray-100" />
                <div>
                  <p class="font-bold text-gray-900">{{ nv.ho_ten }}</p>
                  <p class="text-xs text-gray-500">{{ nv.chuc_vu }}</p>
                </div>
              </div>
              <div class="text-right">
                <p class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full border border-emerald-100 inline-block">
                  ✓ Phù hợp
                </p>
                <p class="text-[11px] text-gray-500 mt-1 max-w-[200px] truncate" :title="nv.ly_do">{{ nv.ly_do }}</p>
              </div>
            </div>
          </div>
        </div>

        <div v-if="rejecting" class="mb-8 bg-red-50 p-4 rounded-lg border border-red-200">
          <label class="block text-sm font-bold text-red-800 mb-2">Lý do từ chối (Bắt buộc)</label>
          <textarea v-model="rejectReason" class="w-full border border-red-300 rounded p-2 focus:ring-red-500 focus:border-red-500" rows="3" placeholder="Nhập lý do từ chối đơn này..."></textarea>
        </div>

        <!-- Dates -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
          <div class="flex items-center justify-between text-sm text-gray-600">
            <span>Ngày gửi yêu cầu:</span>
            <span class="font-bold text-gray-900">{{ confirmation.submittedDate }}</span>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div v-if="confirmation.status === 'cho_duyet'" class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex gap-3 justify-end shrink-0">
        <button @click="$emit('close')" class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition">
          Hủy
        </button>
        <button v-if="!rejecting" @click="rejecting = true" class="px-6 py-3 bg-red-100 hover:bg-red-200 text-red-700 font-bold rounded-lg transition">
          Từ chối
        </button>
        <button v-if="rejecting" @click="submitReject" :disabled="!rejectReason.trim() || isProcessing" class="px-6 py-3 bg-red-500 hover:bg-red-600 disabled:bg-red-400 text-white font-bold rounded-lg transition">
          {{ isProcessing ? "Đang xử lý..." : "Xác nhận từ chối" }}
        </button>
        <button v-if="!rejecting" @click="$emit('approve')" :disabled="isProcessing" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 disabled:from-emerald-400 disabled:to-teal-400 text-white font-bold rounded-lg transition flex items-center gap-2">
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          {{ isProcessing ? "Đang xử lý..." : "Duyệt đơn" }}
        </button>
      </div>
      <div v-else class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex justify-end shrink-0">
        <button @click="$emit('close')" class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition">
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/utils/api";

const props = defineProps({
  confirmation: {
    type: Object,
    required: true,
  },
  isProcessing: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "approve", "reject"]);

const rejecting = ref(false);
const rejectReason = ref("");

const loadingSuggestions = ref(false);
const suggestions = ref([]);

const fetchSuggestions = async () => {
  if (props.confirmation.status !== 'cho_duyet') return;
  
  loadingSuggestions.value = true;
  try {
    const res = await api.get(`/admin/lich-nghi/${props.confirmation.id}/suggest`);
    if (res.data?.status) {
      suggestions.value = res.data.data || [];
    }
  } catch (error) {
    console.error("Lỗi lấy gợi ý:", error);
  } finally {
    loadingSuggestions.value = false;
  }
};

const submitReject = () => {
  if (!rejectReason.value.trim()) return;
  emit('reject', rejectReason.value);
};

onMounted(() => {
  fetchSuggestions();
});
</script>

<style scoped>
@keyframes slide-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-slide-up { animation: slide-up 0.3s ease-out; }
</style>
