<template>
  <!-- Main Detail Popup -->
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    @click="close"
  >
    <div
      class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <div class="p-6">
        <!-- Header Modal -->
        <div class="relative mb-4">
          <h2 class="text-lg font-bold">Hồ sơ chi tiết</h2>
          <button
            @click="close"
            class="absolute right-0 top-1/2 -translate-y-1/2 p-1 hover:bg-gray-100 rounded-full transition"
          >
            <svg
              class="w-7 h-7"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 28 28"
            >
              <path
                d="M21 7L7 21M7 7l14 14"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </button>
          <p class="text-gray-600 mt-1">
            Thông tin sức khỏe và lịch sử khám bệnh
          </p>
        </div>

        <!-- Pet Info -->
        <div class="flex gap-4 mb-6">
          <img
            :src="
              pet.anh_dai_dien_url ||
              pet.image ||
              pet.imageCard ||
              imgPetPlaceholder
            "
            alt="Pet"
            class="w-24 h-24 rounded-xl object-cover"
            @error="handleImgError"
          />
          <div>
            <h3 class="text-amber-600 font-semibold text-lg">{{ pet.name }}</h3>
            <div class="grid grid-cols-2 gap-y-2 gap-x-20 mt-2 text-sm">
              <div class="whitespace-nowrap">
                <span class="text-gray-500 font-medium">Giống: </span>
                <span class="font-semibold text-slate-900">{{
                  pet.breed
                }}</span>
              </div>
              <div class="whitespace-nowrap">
                <span class="text-gray-500 font-medium">Tuổi: </span>
                <span class="font-semibold text-slate-900">{{ pet.age }}</span>
              </div>
              <div class="whitespace-nowrap">
                <span class="text-gray-500 font-medium">Cân nặng: </span>
                <span class="font-semibold text-slate-900">{{
                  pet.weight
                }}</span>
              </div>
              <div class="whitespace-nowrap">
                <span class="text-gray-500 font-medium">Giới tính: </span>
                <span class="font-semibold text-slate-900">{{
                  pet.gender
                }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="bg-gray-200 rounded-2xl p-1 mb-6 flex">
          <button
            @click="tab = 'vaccination'"
            :class="{ 'bg-white shadow-sm': tab === 'vaccination' }"
            class="flex-1 py-2 rounded-2xl font-semibold text-sm transition"
          >
            Lịch tiêm phòng
          </button>
          <button
            @click="tab = 'medical'"
            :class="{ 'bg-white shadow-sm': tab === 'medical' }"
            class="flex-1 py-2 rounded-2xl font-semibold text-sm transition"
          >
            Bệnh án
          </button>
        </div>

        <!-- Tab Vaccination -->
        <div v-show="tab === 'vaccination'" class="space-y-4">
          <div class="flex flex-col items-center justify-center py-8 text-center text-gray-400">
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-medium text-sm">Chưa có lịch tiêm phòng</p>
          </div>
        </div>

        <!-- Tab Medical -->
        <div v-show="tab === 'medical'" class="space-y-4">
          <!-- Loading -->
          <div v-if="loadingHistory" class="flex justify-center py-8">
            <svg class="animate-spin w-8 h-8 text-[#5a9690]" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
            </svg>
          </div>

          <!-- Empty state -->
          <div
            v-else-if="medicalRecords.length === 0"
            class="flex flex-col items-center justify-center py-8 text-center text-gray-400"
          >
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="font-medium text-sm">Chưa có lịch sử khám bệnh</p>
          </div>

          <!-- Records list -->
          <div
            v-else
            v-for="m in medicalRecords"
            :key="m.id"
            class="border border-gray-300 rounded-xl p-4"
          >
            <div class="flex justify-between items-center mb-1">
              <span class="font-semibold text-slate-900">{{ m.title }}</span>
              <span class="text-sm font-medium">{{ m.date }}</span>
            </div>
            <p class="text-gray-600 text-sm">Bác sĩ: {{ m.doctor }}</p>
            <p v-if="m.note" class="text-slate-700 font-medium text-sm">
              Ghi chú: {{ m.note }}
            </p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 mt-8">
          <button
            @click="handleBookAppointment"
            class="flex-1 bg-[#5a9690] text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:opacity-90 transition"
          >
            Đặt lịch khám lại
          </button>
          <button
            @click="openUpdatePopup"
            class="flex-1 border !border-gray-400 py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-gray-50 transition"
          >
            Cập nhật thông tin
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Pet Info Popup -->
  <div
    v-if="showUpdatePopup"
    class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 p-4"
    @click="closeUpdatePopup"
  >
    <div
      class="bg-white border border-black/15 rounded-[10px] shadow-2xl w-full max-w-[512px] max-h-[90vh] overflow-y-auto"
      @click.stop
    >
      <div class="p-6 flex flex-col gap-6">
        <!-- Header -->
        <div class="flex flex-col gap-2">
          <div class="h-7 relative">
            <div class="flex items-center justify-between">
              <h2 class="font-bold text-lg leading-7 text-black">
                Cập nhật hồ sơ '{{ petData.name }}'
              </h2>
              <button @click="closeUpdatePopup" class="w-7 h-7">
                <CloseIcon />
              </button>
            </div>
          </div>
          <p class="font-normal text-sm leading-6 text-gray-500">
            Chỉnh sửa thông tin định danh thú cưng
          </p>
        </div>

        <!-- Form Container -->
        <div class="flex flex-col">
          <!-- Form Fields with Pet Image -->
          <div
            class="bg-amber-50 border-2 !border-amber-300 rounded-[10px] p-[18px] flex flex-col gap-4"
          >
            <!-- Pet Image and Name/Breed -->
            <div class="flex gap-4">
              <!-- Pet Image -->
              <div class="relative w-24 h-24">
                <img
                  :src="petData.image || imgPetPlaceholder"
                  alt="Pet"
                  class="w-24 h-24 rounded-[10px] object-cover"
                />
                <div
                  class="absolute inset-0 bg-black/50 rounded-[10px] flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity cursor-pointer"
                  @click="handleImageUpload"
                >
                  <div class="w-8 h-8">
                    <img :src="iconCamera" alt="Upload" class="w-full h-full" />
                  </div>
                </div>
              </div>

              <!-- Name, Loại and Breed Fields -->
              <div class="flex-1 flex flex-col gap-3">
                <!-- Name Field -->
                <div class="flex flex-col gap-1">
                  <label class="font-semibold text-sm leading-5 text-black">
                    Tên thú cưng
                    <span class="text-red-600">*</span>
                  </label>
                  <input
                    v-model="petData.name"
                    type="text"
                    placeholder="Milo"
                    tabindex="0"
                    :readonly="false"
                    class="bg-gray-50 border !border-black/15 rounded-lg h-9 px-3 py-1 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <!-- Loại Field -->
                <div class="flex flex-col gap-1">
                  <label class="font-semibold text-sm leading-5 text-black">
                    Loài
                  </label>
                  <input
                    v-model="petData.loai_thu_cung"
                    type="text"
                    placeholder="Chó / Mèo"
                    tabindex="0"
                    :readonly="false"
                    class="bg-gray-50 border !border-black/15 rounded-lg h-9 px-3 py-1 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
                  />
                </div>

                <!-- Breed Field -->
                <div class="flex flex-col gap-1">
                  <label class="font-semibold text-sm leading-5 text-black">
                    Giống
                  </label>
                  <input
                    v-model="petData.breed"
                    type="text"
                    placeholder="Golden Retriever"
                    tabindex="0"
                    :readonly="false"
                    class="bg-gray-50 border !border-black/15 rounded-lg h-9 px-3 py-1 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
                  />
                </div>
              </div>
            </div>

            <!-- Birth Date Field (editable)-->
            <div class="flex flex-col gap-1">
              <label class="font-semibold text-sm leading-5 text-black">
                Ngày sinh
              </label>
              <input
                v-model="petData.birthDate"
                type="date"
                tabindex="0"
                :readonly="false"
                class="bg-white border !border-black/15 rounded-lg h-9 px-3 py-2 text-sm text-gray-700"
              />
            </div>

            <!-- Weight and Gender Grid -->
            <div class="grid grid-cols-2 gap-3">
              <!-- Weight Field -->
              <div class="flex flex-col gap-1">
                <label class="font-semibold text-sm leading-5 text-black">
                  Cân nặng (kg)
                  <span class="text-red-600">*</span>
                </label>
                <input
                  v-model.number="petData.weight"
                  type="number"
                  placeholder="28"
                  tabindex="0"
                  :readonly="false"
                  class="bg-gray-50 border !border-black/15 rounded-lg h-9 px-3 py-1 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
                />
              </div>

              <!-- Gender Field -->
              <div class="flex flex-col gap-3">
                <label class="font-semibold text-sm leading-5 text-black">
                  Giới tính
                </label>
                <div class="flex items-center gap-4">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      v-model="petData.gender"
                      type="radio"
                      value="male"
                      class="w-4 h-4 accent-teal-600"
                    />
                    <span class="font-semibold text-sm leading-5 text-black"
                      >Đực</span
                    >
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      v-model="petData.gender"
                      type="radio"
                      value="female"
                      class="w-4 h-4 accent-teal-600"
                    />
                    <span class="font-semibold text-sm leading-5 text-black"
                      >Cái</span
                    >
                  </label>
                </div>
              </div>
            </div>

            <!-- Warning Note -->
            <div
              class="bg-amber-50 border !border-amber-500 rounded px-6 py-3 flex items-center gap-5"
            >
              <p
                class="font-bold text-xs leading-4 text-amber-800 whitespace-nowrap"
              >
                Lưu ý:
              </p>
              <p class="font-normal text-xs leading-4 text-amber-800">
                Cân nặng này là thông tin tham khảo. Cân nặng chính xác sẽ được
                đo bởi Bác sĩ trong mỗi lần khám.
              </p>
            </div>
          </div>

          <!-- Action Buttons: Save / Hủy for the update popup -->
          <div>
            <div class="flex gap-3 mt-4">
              <button
                type="button"
                @click.prevent="handleSave"
                :disabled="saving"
                aria-label="Lưu thay đổi"
                class="flex-1 bg-[#5a9690] text-white py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:opacity-90 transition disabled:opacity-60 disabled:cursor-not-allowed"
              >
                <template v-if="saving">
                  <svg class="animate-spin w-5 h-5" viewBox="0 0 24 24">
                    <circle
                      class="opacity-25"
                      cx="12"
                      cy="12"
                      r="10"
                      stroke="white"
                      stroke-width="4"
                      fill="none"
                    ></circle>
                    <path
                      class="opacity-75"
                      fill="white"
                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                    ></path>
                  </svg>
                  Đang lưu...
                </template>
                <template v-else> Lưu </template>
              </button>
              <button
                type="button"
                @click="closeUpdatePopup"
                class="flex-1 border !border-gray-400 py-3 rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-gray-50 transition"
              >
                Hủy
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import { getToken } from "@/utils/auth";
import { getLichSuKham } from "@/services/thuCungService";
import CloseIcon from "@/assets/svg/close.svg";

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const props = defineProps({
  isOpen: Boolean,
  pet: Object,
});

const emit = defineEmits(["close", "updated"]);

const router = useRouter();

// Tabs & popup
const tab = ref("vaccination");
const showUpdatePopup = ref(false);

// Lịch sử khám bệnh
const loadingHistory = ref(false);
const medicalRecords = ref([]);

// Dữ liệu chỉnh sửa
const petData = ref({
  name: "",
  loai_thu_cung: "",
  breed: "",
  birthDate: "",
  weight: null,
  gender: "male",
  image: "",
  _file: null,
});

const saving = ref(false);

// Hàm chuyển mọi định dạng ngày về YYYY-MM-DD
const toISODateInput = (val) => {
  if (!val) return "";
  const s = String(val).trim();
  if (!s) return "";

  // 2024-05-15 → giữ nguyên
  if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;

  // 15/05/2024 hoặc 15-05-2024
  const dmy = s.match(/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/);
  if (dmy) {
    return `${dmy[3]}-${String(dmy[2]).padStart(2, "0")}-${String(
      dmy[1]
    ).padStart(2, "0")}`;
  }

  // ISO string từ Laravel
  const date = new Date(s);
  if (!isNaN(date.getTime())) {
    return date.toISOString().split("T")[0];
  }

  return "";
};

// Icons
const iconClose =
  "https://www.figma.com/api/mcp/asset/4d87fa33-ac2d-4cba-8927-955ccda358c0";
const iconCamera =
  "https://www.figma.com/api/mcp/asset/57dea092-4899-4f1c-8556-da10fb08401a";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/4c59f3ee-3346-4481-972a-039e25c57ba4";
const iconCancel =
  "https://www.figma.com/api/mcp/asset/fba839da-4b3e-4f65-af14-ae508ea8adae";
const iconSave =
  "https://www.figma.com/api/mcp/asset/a70f24d7-c94e-4805-a57e-19a2a7f84371";
const imgPetPlaceholder =
  "https://www.figma.com/api/mcp/asset/f456f1c3-8143-4157-a509-7e9742162fdd";

// Watch props.pet → copy dữ liệu (giữ reactivity)
watch(
  () => props.pet,
  (newPet) => {
    if (!newPet) return;

    petData.value.name = newPet.ten_thu_cung || newPet.name || "";
    petData.value.loai_thu_cung = newPet.loai_thu_cung || "";
    petData.value.breed = newPet.giong_thu_cung || newPet.breed || "";
    petData.value.birthDate = toISODateInput(
      newPet.tuoi_thu_cung || newPet.birthDate || ""
    );

    // Cân nặng
    const w = newPet.can_nang || newPet.weight || "";
    const num = parseFloat(
      String(w)
        .replace(/[^0-9.,]/g, "")
        .replace(",", ".")
    );
    petData.value.weight = isNaN(num) ? null : num;

    // Giới tính
    const g = (newPet.gioi_tinh || newPet.gender || "")
      .toString()
      .toLowerCase();
    petData.value.gender = /cái|female|f/.test(g) ? "female" : "male";

    // Ảnh
    petData.value.image =
      newPet.anh_dai_dien_url ||
      newPet.image ||
      newPet.imageCard ||
      imgPetPlaceholder;
    petData.value._file = null;
  },
  { immediate: true }
);

// Lấy ID chính xác (rất quan trọng!)
const getPetId = () => {
  return props.pet?.id || props.pet?.thu_cung_id || props.pet?.pet_id || null;
};

// Fetch lịch sử khám khi popup mở
const fetchHistory = async (petId) => {
  if (!petId) return;
  loadingHistory.value = true;
  medicalRecords.value = [];
  try {
    const res = await getLichSuKham(petId);
    const list = res?.data?.phieu_khams ?? [];
    medicalRecords.value = list.map((pk) => ({
      id: pk.id,
      title: pk.chan_doan || pk.ly_do_den_kham || "Khám tổng quát",
      date: pk.date,
      doctor: pk.bac_si,
      note: pk.ghi_chu || "",
    }));
  } catch {
    medicalRecords.value = [];
  } finally {
    loadingHistory.value = false;
  }
};

watch(
  () => props.isOpen,
  (val) => {
    if (val && props.pet) {
      fetchHistory(props.pet.id || props.pet.thu_cung_id);
    }
  }
);

// Đặt lịch khám lại
const handleBookAppointment = () => {
  const petId = getPetId();
  emit("close");
  router.push({ path: "/customer/appointments/book", query: { pet_id: petId } });
};

// Ảnh fallback
const handleImgError = (event) => {
  event.target.src = imgPetPlaceholder;
};

// Các hàm
const close = () => emit("close");
const openUpdatePopup = () => (showUpdatePopup.value = true);
const closeUpdatePopup = () => (showUpdatePopup.value = false);

const handleImageUpload = () => {
  const input = document.createElement("input");
  input.type = "file";
  input.accept = "image/*";
  input.onchange = (e) => {
    const file = e.target.files?.[0];
    if (file) {
      petData.value._file = file;
      petData.value.image = URL.createObjectURL(file);
    }
  };
  input.click();
};

// CHỈNH SỬA DUY NHẤT LÀM LƯU HOẠT ĐỘNG: DÙNG getPetId()
const handleSave = async () => {
  const id = Number(getPetId());
  if (!id) {
    showErrorToast("Lỗi", "Không tìm thấy ID thú cưng");
    return;
  }

  if (!petData.value.name?.trim()) {
    showErrorToast("Lỗi", "Vui lòng nhập tên thú cưng");
    return;
  }
  if (!petData.value.weight || petData.value.weight <= 0) {
    showErrorToast("Lỗi", "Cân nặng phải lớn hơn 0");
    return;
  }

  try {
    saving.value = true;
    const fd = new FormData();
    fd.append("ten_thu_cung", petData.value.name.trim());
    if (petData.value.loai_thu_cung)
      fd.append("loai_thu_cung", petData.value.loai_thu_cung);
    if (petData.value.breed) fd.append("giong_thu_cung", petData.value.breed);
    if (petData.value.birthDate)
      fd.append("tuoi_thu_cung", petData.value.birthDate);
    fd.append("gioi_tinh", petData.value.gender === "male" ? "male" : "female");
    fd.append("can_nang", String(petData.value.weight));

    if (petData.value._file) {
      fd.append("anh_dai_dien", petData.value._file);
    }

    // Debug: log FormData entries (useful for diagnosing missing fields)
    try {
      for (const pair of fd.entries()) {
        // eslint-disable-next-line no-console
        console.log("FormData:", pair[0], pair[1]);
      }
    } catch (e) {}

    // attach Authorization header if token present (we avoid withCredentials to reduce CORS issues)
    const token = getToken();
    const headers = { "Content-Type": "multipart/form-data" };
    if (token) headers["Authorization"] = `Bearer ${token}`;

    const url = `${API_BASE}/thu-cung/${id}`;
    // eslint-disable-next-line no-console
    console.log("Saving to:", url, "token present:", !!getToken());
    let res;
    try {
      // If uploading a file, use multipart POST + _method=PUT so Laravel/PHP parses $_FILES
      const hasFile = !!petData.value._file;

      if (hasFile) {
        // reuse fd created above
        fd.append("_method", "PUT");
        res = await axios.post(url, fd, { headers });
      } else {
        // For non-file updates, send JSON via PUT (Laravel parses JSON body reliably)
        const jsonPayload = {};
        if (petData.value.name)
          jsonPayload.ten_thu_cung = petData.value.name.trim();
        if (petData.value.loai_thu_cung)
          jsonPayload.loai_thu_cung = petData.value.loai_thu_cung;
        if (petData.value.breed)
          jsonPayload.giong_thu_cung = petData.value.breed;
        if (petData.value.birthDate)
          jsonPayload.tuoi_thu_cung = petData.value.birthDate;
        jsonPayload.gioi_tinh =
          petData.value.gender === "male" ? "male" : "female";
        if (petData.value.weight !== null && petData.value.weight !== undefined)
          jsonPayload.can_nang = String(petData.value.weight);

        // prefer JSON header for PUT
        const jsonHeaders = { "Content-Type": "application/json" };
        if (token) jsonHeaders["Authorization"] = `Bearer ${token}`;

        res = await axios.put(url, jsonPayload, { headers: jsonHeaders });
      }
    } catch (err) {
      // If network error and we had a token, try cookie-based fallback (withCredentials)
      const isNetwork = err?.code === "ERR_NETWORK" || !err?.response;
      // eslint-disable-next-line no-console
      console.warn("Save error, network?", isNetwork, err?.message || err);
      if (isNetwork && getToken()) {
        // Retry using withCredentials (useful if backend expects cookie-based auth like Sanctum)
        try {
          // eslint-disable-next-line no-console
          console.log("Retrying with withCredentials=true (cookie-based)");
          if (petData.value._file) {
            // retry POST with withCredentials
            res = await axios.post(url, fd, {
              headers: { "Content-Type": "multipart/form-data" },
              withCredentials: true,
            });
          } else {
            res = await axios.put(url, fd, {
              headers: { "Content-Type": "multipart/form-data" },
              withCredentials: true,
            });
          }
        } catch (err2) {
          // return original error
          throw err2;
        }
      } else {
        throw err;
      }
    }

    if (res.data && res.data.status) {
      showSuccessToast("Thành công", "Cập nhật thông tin thú cưng thành công");
      // Fetch fresh formatted item from API (ensure parent gets canonical data)
      try {
        const token2 = getToken();
        const headers2 = {};
        if (token2) headers2["Authorization"] = `Bearer ${token2}`;
        const getRes = await axios.get(`${API_BASE}/thu-cung/${id}`, {
          headers: headers2,
        });
        if (getRes.data && getRes.data.status) {
          emit("updated", getRes.data.data);
        } else {
          // fallback to using patch response data
          emit("updated", res.data.data);
        }
      } catch (e) {
        // if get fails, still emit patch response
        emit("updated", res.data.data);
      }
      closeUpdatePopup();
    } else {
      // backend returned status:false
      // eslint-disable-next-line no-console
      console.warn("Update response not OK:", res.data);
      showErrorToast("Lỗi", res.data.message || "Cập nhật thất bại");
    }
  } catch (err) {
    // better error reporting for validation errors
    // eslint-disable-next-line no-console
    console.error("Save error:", err);
    const resp = err.response?.data;
    if (resp && resp.errors) {
      // show first validation error
      const firstField = Object.keys(resp.errors)[0];
      const firstMsg = resp.errors[firstField][0];
      showErrorToast("Lỗi xác thực", firstMsg);
    } else {
      const msg = resp?.message || err.message || "Lỗi mạng";
      showErrorToast("Lỗi", msg);
    }
  } finally {
    saving.value = false;
  }
};
</script>

<style scoped>
/* Custom styles for number input */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}
</style>
