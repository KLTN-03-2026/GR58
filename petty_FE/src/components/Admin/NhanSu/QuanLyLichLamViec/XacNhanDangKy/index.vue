<template>
  <!-- Modal Overlay -->
  <div
    class="fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center p-4"
  >
    <!-- Modal Content -->
    <div
      class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl overflow-hidden animate-slide-up"
    >
      <!-- Header -->
      <div
        class="bg-gradient-to-r from-teal-500 to-teal-600 px-8 py-6 flex items-center justify-between"
      >
        <div>
          <h2 class="text-2xl font-bold text-white">Chi tiết yêu cầu</h2>
          <p class="text-teal-100 text-sm mt-1">
            Xem và xác nhận yêu cầu đăng ký ca
          </p>
        </div>
        <button
          @click="$emit('close')"
          class="text-teal-100 hover:text-white transition"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </button>
      </div>

      <!-- Body -->
      <div class="p-8">
        <!-- Staff Info -->
        <div class="flex items-center gap-6 mb-8 pb-8 border-b border-gray-200">
          <img
            :src="confirmation.avatar"
            :alt="confirmation.staffName"
            class="w-20 h-20 rounded-full object-cover border-4 border-teal-100"
          />
          <div class="flex-1">
            <h3 class="text-2xl font-bold text-gray-900">
              {{ confirmation.staffName }}
            </h3>
            <p class="text-gray-600 mt-1">{{ confirmation.role }}</p>
            <p class="text-sm text-gray-500 mt-3">
              📋 ID: {{ confirmation.id }}
            </p>
          </div>
          <div
            :class="[
              'px-6 py-3 rounded-lg font-bold text-white',
              confirmation.status === 'pending'
                ? 'bg-amber-500'
                : confirmation.status === 'confirmed'
                ? 'bg-emerald-500'
                : 'bg-red-500',
            ]"
          >
            {{
              confirmation.status === "pending"
                ? "⏳ Chờ xác nhận"
                : confirmation.status === "confirmed"
                ? "✓ Đã xác nhận"
                : "✕ Từ chối"
            }}
          </div>
        </div>

        <!-- Shift Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
          <div class="space-y-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">
              📅 Thông tin ca làm việc
            </h4>

            <!-- Date -->
            <div
              class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg p-4 border border-blue-200"
            >
              <p class="text-sm text-blue-600 font-bold mb-1">Ngày</p>
              <p class="text-2xl font-bold text-blue-900">
                {{ confirmation.date }}
              </p>
            </div>

            <!-- Shift Time -->
            <div
              :class="[
                'rounded-lg p-4 border text-white font-bold',
                confirmation.shift === 'morning'
                  ? 'bg-gradient-to-br from-emerald-400 to-emerald-500 border-emerald-600'
                  : confirmation.shift === 'afternoon'
                  ? 'bg-gradient-to-br from-amber-400 to-amber-500 border-amber-600'
                  : 'bg-gradient-to-br from-slate-600 to-slate-700 border-slate-800',
              ]"
            >
              <p class="text-sm opacity-90 mb-1">Ca làm việc</p>
              <p class="text-2xl">
                {{
                  confirmation.shift === "morning"
                    ? "☀️ Sáng (06:00 - 12:00)"
                    : confirmation.shift === "afternoon"
                    ? "🌤️ Chiều (13:00 - 18:00)"
                    : "🌙 Tối (19:00 - 07:00)"
                }}
              </p>
            </div>
          </div>

          <div class="space-y-4">
            <h4 class="text-lg font-bold text-gray-900 mb-4">
              🏥 Thông tin chi tiết
            </h4>

            <!-- Room -->
            <div class="bg-purple-50 rounded-lg p-4 border border-purple-200">
              <p class="text-sm text-purple-600 font-bold mb-1">Phòng</p>
              <p class="text-xl font-bold text-purple-900">
                {{ confirmation.room }}
              </p>
            </div>

            <!-- Hours -->
            <div class="bg-teal-50 rounded-lg p-4 border border-teal-200">
              <p class="text-sm text-teal-600 font-bold mb-1">
                Giờ làm dự kiến
              </p>
              <p class="text-2xl font-bold text-teal-900">
                {{ confirmation.hours }}h
              </p>
            </div>
          </div>
        </div>

        <!-- Note Section -->
        <div v-if="confirmation.note" class="mb-8">
          <h4 class="text-lg font-bold text-gray-900 mb-3">
            Ghi chú từ nhân viên
          </h4>
          <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <p class="text-gray-700">{{ confirmation.note }}</p>
          </div>
        </div>

        <!-- Dates -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-8">
          <div class="flex items-center justify-between text-sm text-gray-600">
            <span>📤 Ngày gửi yêu cầu:</span>
            <span class="font-bold text-gray-900">{{
              confirmation.submittedDate
            }}</span>
          </div>
        </div>

        <!-- Response History (if available) -->
        <div
          v-if="confirmation.status !== 'pending'"
          class="bg-yellow-50 rounded-lg p-4 border border-yellow-200"
        >
          <p class="text-sm text-yellow-700">
            <span class="font-bold">Thời gian xác nhận:</span>
            {{ confirmation.confirmedDate || "Không có thông tin" }}
          </p>
        </div>
      </div>

      <!-- Footer Actions -->
      <div
        v-if="confirmation.status === 'pending'"
        class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex gap-3 justify-end"
      >
        <button
          @click="$emit('close')"
          class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition"
        >
          Hủy
        </button>
        <button
          @click="$emit('reject')"
          :disabled="isProcessing"
          class="px-6 py-3 bg-red-500 hover:bg-red-600 disabled:bg-red-400 text-white font-bold rounded-lg transition flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
              clip-rule="evenodd"
            />
          </svg>
          {{ isProcessing ? "Đang xử lý..." : "Từ chối" }}
        </button>
        <button
          @click="$emit('approve')"
          :disabled="isProcessing"
          class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 disabled:from-emerald-400 disabled:to-teal-400 text-white font-bold rounded-lg transition flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
            <path
              fill-rule="evenodd"
              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
              clip-rule="evenodd"
            />
          </svg>
          {{ isProcessing ? "Đang xử lý..." : "Xác nhận" }}
        </button>
      </div>
      <div
        v-else
        class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex justify-end"
      >
        <button
          @click="$emit('close')"
          class="px-6 py-3 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition"
        >
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

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

defineEmits(["close", "approve", "reject"]);
</script>

<style scoped>
@keyframes slide-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slide-up 0.3s ease-out;
}
</style>
