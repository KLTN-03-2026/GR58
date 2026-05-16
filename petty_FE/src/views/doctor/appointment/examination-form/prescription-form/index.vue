<template>
  <div class="bg-white flex flex-col rounded-2xl shadow-2xl w-full max-w-5xl max-h-[88vh]">
    <!-- Header -->
    <div class="flex items-center justify-between px-7 py-5 border-b !border-gray-200">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
          <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
        </div>
        <div>
          <h2 class="font-bold text-lg text-gray-900">Kê đơn thuốc</h2>
          <p class="text-xs text-gray-400">Chọn thuốc bên trái, điều chỉnh liều dùng bên phải</p>
        </div>
      </div>
      <button
        @click="$emit('close')"
        class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    <!-- Two-panel body -->
    <div class="flex flex-1 overflow-hidden min-h-0">

      <!-- LEFT PANEL: Medication Catalog -->
      <div class="w-[260px] border-r !border-gray-200 flex flex-col bg-gray-50/50 shrink-0">
        <!-- Search -->
        <div class="p-4 pb-3">
          <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm thuốc..."
              class="w-full h-9 pl-9 pr-3 bg-white border !border-gray-200 rounded-lg text-sm text-gray-700 outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition-all"
            />
          </div>
        </div>

        <!-- Catalog list -->
        <div v-if="loadingCatalog" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-6 w-6 border-2 border-emerald-500 border-t-transparent"></div>
        </div>

        <div v-else class="flex-1 overflow-y-auto px-2 pb-3">
          <button
            v-for="item in filteredCatalog"
            :key="item.id"
            class="w-full px-3 py-2.5 mb-0.5 text-left rounded-lg hover:bg-emerald-50 active:bg-emerald-100 transition-colors group flex items-center gap-2"
            @click="addMedicine(item)"
          >
            <span class="w-5 h-5 rounded-full border-2 !border-emerald-300 flex items-center justify-center shrink-0 group-hover:bg-emerald-500 group-hover:border-emerald-500 transition-colors">
              <svg class="w-3 h-3 text-transparent group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
              </svg>
            </span>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-gray-800 truncate leading-tight">{{ item.ten_mat_hang }}</p>
              <p class="text-[11px] text-gray-400 leading-tight">{{ item.don_vi_tinh }}</p>
            </div>
          </button>

          <p v-if="filteredCatalog.length === 0 && !loadingCatalog" class="text-center text-xs text-gray-400 py-6">
            Không tìm thấy
          </p>
        </div>
      </div>

      <!-- RIGHT PANEL: Prescription Items -->
      <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Empty state -->
        <div v-if="medicines.length === 0" class="flex-1 flex items-center justify-center p-8">
          <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-50 flex items-center justify-center">
              <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
              </svg>
            </div>
            <p class="text-sm font-medium text-gray-500 mb-1">Chưa có thuốc trong đơn</p>
            <p class="text-xs text-gray-400">Click vào thuốc bên trái để thêm vào đơn</p>
          </div>
        </div>

        <!-- Prescription items list -->
        <div v-else class="flex-1 overflow-y-auto p-5 space-y-4">
          <div
            v-for="(med, idx) in medicines"
            :key="idx"
            class="bg-emerald-50/60 border !border-emerald-200/60 rounded-xl p-4 relative group"
          >
            <!-- Remove button -->
            <button
              @click="removeMedicine(idx)"
              class="absolute top-3 right-3 w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover:opacity-100"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>

            <!-- Medicine name -->
            <p class="font-bold text-sm text-gray-900 mb-3 pr-8">{{ idx + 1 }}. {{ med.ten }}</p>

            <!-- Số lượng + Đơn vị (compact row) -->
            <div class="flex items-center gap-4 mb-3">
              <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 whitespace-nowrap">SL:</label>
                <input
                  v-model.number="med.so_luong"
                  type="number"
                  min="1"
                  class="w-16 h-8 px-2 bg-white border !border-gray-200 rounded-md text-sm text-center text-gray-900 outline-none focus:ring-2 focus:ring-emerald-400 transition-all"
                />
              </div>
              <div class="flex items-center gap-2">
                <label class="text-xs font-medium text-gray-500 whitespace-nowrap">Đơn vị:</label>
                <input
                  v-model="med.don_vi"
                  type="text"
                  class="w-24 h-8 px-2 bg-white border !border-gray-200 rounded-md text-sm text-gray-900 outline-none focus:ring-2 focus:ring-emerald-400 transition-all"
                />
              </div>
            </div>

            <!-- Liều dùng (FULL WIDTH, prominent) -->
            <div class="mb-3">
              <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Liều dùng</label>
              <input
                v-model="med.lieu_dung"
                type="text"
                placeholder="VD: 1 viên × 2 lần/ngày, sau ăn 30 phút"
                class="w-full h-10 px-3 bg-white border !border-gray-200 rounded-lg text-sm text-gray-900 outline-none focus:ring-2 focus:ring-emerald-400 transition-all placeholder:text-gray-300"
              />
              <!-- Quick-fill buttons -->
              <div class="flex flex-wrap gap-1.5 mt-2">
                <button
                  v-for="preset in dosagePresets"
                  :key="preset"
                  @click="appendDosage(med, preset)"
                  class="px-2.5 py-1 bg-white border !border-emerald-200 rounded-md text-[11px] font-medium text-emerald-700 hover:bg-emerald-50 hover:border-emerald-300 transition-colors"
                >
                  {{ preset }}
                </button>
              </div>
            </div>

            <!-- Ghi chú (FULL WIDTH) -->
            <div>
              <label class="text-xs font-semibold text-gray-600 mb-1.5 block">Ghi chú</label>
              <input
                v-model="med.ghi_chu"
                type="text"
                placeholder="Lưu ý đặc biệt, tác dụng phụ, tương tác thuốc..."
                class="w-full h-10 px-3 bg-white border !border-gray-200 rounded-lg text-sm text-gray-900 outline-none focus:ring-2 focus:ring-emerald-400 transition-all placeholder:text-gray-300"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="px-7 py-4 border-t !border-gray-200 flex items-center justify-between bg-gray-50/50 rounded-b-2xl">
      <p v-if="medicines.length > 0" class="text-sm text-gray-500">
        <span class="font-semibold text-gray-700">{{ medicines.length }}</span> loại thuốc trong đơn
      </p>
      <span v-else></span>

      <div class="flex items-center gap-3">
        <button
          @click="$emit('close')"
          class="px-5 py-2.5 rounded-lg font-medium text-sm text-gray-700 bg-white border !border-gray-300 hover:bg-gray-50 transition-colors"
        >
          Hủy
        </button>
        <button
          @click="savePrescription"
          :disabled="medicines.length === 0"
          class="px-5 py-2.5 rounded-lg font-semibold text-sm text-white bg-[#00a63e] hover:bg-[#009235] transition-colors disabled:opacity-40 disabled:cursor-not-allowed flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          Lưu đơn thuốc
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/utils/api";

const emit = defineEmits(["close", "save"]);

const props = defineProps({
  initialData: { type: Array, default: () => [] }
});

const searchQuery = ref("");
const catalog = ref([]);
const loadingCatalog = ref(false);
const medicines = ref(props.initialData.length > 0 ? [...props.initialData] : []);

const dosagePresets = [
  "Ngày 2 lần",
  "Ngày 3 lần",
  "Sáng-Chiều",
  "Sau ăn",
  "Trước ăn",
  "Trước ngủ",
];

const filteredCatalog = computed(() => {
  if (!searchQuery.value) return catalog.value;
  const q = searchQuery.value.toLowerCase();
  return catalog.value.filter(item =>
    item.ten_mat_hang.toLowerCase().includes(q)
  );
});

onMounted(async () => {
  loadingCatalog.value = true;
  try {
    const res = await api.get('/hang-hoa');
    const data = res.data?.data;
    catalog.value = (Array.isArray(data) ? data : [])
      .filter(item => item.tinh_trang === 'hoat_dong');
  } catch (e) {
    console.error('Lỗi load danh sách thuốc:', e);
  } finally {
    loadingCatalog.value = false;
  }
});

const addMedicine = (item) => {
  const exists = medicines.value.find(m => m.hang_hoa_id === item.id);
  if (exists) {
    exists.so_luong++;
  } else {
    medicines.value.push({
      hang_hoa_id: item.id,
      ten: item.ten_mat_hang,
      so_luong: 1,
      don_vi: item.don_vi_tinh || '',
      don_gia: parseFloat(item.gia_ban) || 0,
      lieu_dung: '',
      ghi_chu: '',
    });
  }
};

const removeMedicine = (idx) => {
  medicines.value.splice(idx, 1);
};

const appendDosage = (med, preset) => {
  if (med.lieu_dung) {
    med.lieu_dung += ', ' + preset;
  } else {
    med.lieu_dung = preset;
  }
};

const savePrescription = () => {
  emit('save', medicines.value);
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&display=swap");
* { font-family: "Nunito Sans", sans-serif; }
</style>
