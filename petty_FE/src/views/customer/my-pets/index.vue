<template>
  <div class="min-h-screen">
    <!-- ====================== DANH SÁCH THÚ CƯNG ====================== -->
    <div class="container mx-auto max-w-7xl">
      <!-- Header -->
      <div class="flex justify-between items-center mb-12">
        <div>
          <h1 class="text-xl font-bold">Thú cưng của tôi</h1>
          <p class="text-lg font-semibold text-gray-600">
            Quản lý thông tin và sức khỏe các bé
          </p>
        </div>
        <button
          @click="isAddPetOpen = true"
          class="flex items-center gap-2 bg-[#5a9690] text-white rounded-xl px-5 py-3.5 font-semibold text-lg hover:bg-[#4a807a] transition"
        >
          <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 16 16">
            <path d="M8 3v10M3 8h10" stroke-width="2" stroke-linecap="round" />
          </svg>
          Thêm thú cưng
        </button>
      </div>

      <!-- Pet Cards -->
      <div v-if="pets.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="pet in pets"
          :key="pet.id"
          class="bg-white border !border-black/15 rounded-2xl overflow-hidden flex flex-col hover:shadow-xl transition cursor-pointer"
          @click="openDetail(pet)"
        >
          <!-- Ảnh + Tag loại -->
          <div
            class="relative h-48 bg-cover bg-center flex justify-end items-start p-7 overflow-hidden"
          >
            <img
              :src="pet.imageCard"
              alt="pet image"
              class="absolute inset-0 w-full h-full object-cover"
              @error="handleImgError($event, pet)"
            />
            <span
              class="px-4 py-2 rounded-lg font-bold text-lg relative z-10"
              :class="pet.tagClass"
              >{{ pet.type }}</span
            >
          </div>

          <!-- Nội dung card -->
          <div class="p-8 flex-1 flex flex-col">
            <div class="flex justify-between mb-4">
              <div>
                <h3 class="text-lg font-bold">{{ pet.name }}</h3>
                <p class="text-gray-600 font-medium whitespace-nowrap">
                  {{ pet.breed }}
                </p>
              </div>
              <div class="space-y-1 text-sm">
                <div>
                  <span class="text-gray-500 font-medium">Tuổi: </span>
                  <span class="font-bold">{{ pet.age }}</span>
                </div>
                <div>
                  <span class="text-gray-500 font-medium">Cân nặng: </span>
                  <span class="font-bold">{{ pet.weight }}</span>
                </div>
              </div>
            </div>

            <!-- Reminder -->
            <div
              class="flex gap-3 px-6 py-4 rounded-xl border !border-black/15 mb-2"
              :class="pet.reminderBg"
            >
              <Calendar class="w-5 h-5" />
              <div>
                <p class="font-bold" :class="pet.reminderTitleClass">
                  {{ pet.reminderTitle }}
                </p>
                <p class="text-xs" :class="pet.reminderDetailClass">
                  {{ pet.reminderDetail }}
                </p>
              </div>
            </div>

            <!-- Buttons -->
            <div class="mt-auto space-y-3">
              <button
                @click.stop="openDeletePopup(pet)"
                class="w-full py-2 border !border-[#eb8e90] rounded-lg hover:bg-red-50 transition"
              >
                <svg
                  class="w-7 h-7 mx-auto"
                  fill="none"
                  stroke="#eb8e90"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6h14zM10 11v6M14 11v6"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="flex flex-col items-center justify-center py-16">
        <svg class="w-32 h-32 text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          <circle cx="12" cy="12" r="10" stroke-width="1.5" />
        </svg>
        <h3 class="text-2xl font-bold text-gray-700 mb-2">Chưa có thú cưng nào</h3>
        <p class="text-gray-500 mb-6">Hãy thêm thú cưng đầu tiên của bạn để bắt đầu quản lý sức khỏe cho bé</p>
        <button
          @click="isAddPetOpen = true"
          class="flex items-center gap-2 bg-[#5a9690] text-white rounded-xl px-6 py-3 font-semibold text-lg hover:bg-[#4a807a] transition"
        >
          <svg class="w-5 h-5" fill="none" stroke="white" viewBox="0 0 16 16">
            <path d="M8 3v10M3 8h10" stroke-width="2" stroke-linecap="round" />
          </svg>
          Thêm thú cưng đầu tiên
        </button>
      </div>
    </div>

    <!-- Modal Chi Tiết Thú Cưng -->
    <teleport to="body">
      <ChiTietThuCung
        v-if="selectedPet"
        :isOpen="showDetail"
        :pet="selectedPet"
        @close="showDetail = false"
        @updated="onPetUpdated"
      />
    </teleport>

    <!-- Modal Xóa Thú Cưng -->
    <teleport to="body">
      <XoaThuCung
        v-if="petToDelete"
        :isOpen="isDeletePetOpen"
        :petData="petToDelete"
        @close="isDeletePetOpen = false"
        @delete="handleDeletePet"
      />
    </teleport>

    <!-- Modal Thêm Thú Cưng -->
    <teleport to="body">
      <ThemThuCung
        :isOpen="isAddPetOpen"
        @close="isAddPetOpen = false"
        @submit="handleAddPet"
      />
    </teleport>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import ThemThuCung from "./add-pet/index.vue";
import ChiTietThuCung from "./pet-detail/index.vue";
import XoaThuCung from "./delete-pet/index.vue";
import { showSuccessToast } from "@/utils/toast";
import Calendar from "@/assets/svg/calendar.svg";

const API_BASE = "http://localhost:8000/api";
const BASE_HOST = API_BASE.replace("/api", "");
const PLACEHOLDER_IMAGE =
  "https://www.figma.com/api/mcp/asset/7dc3f4c9-30fd-4f46-b415-7a1aab552e01";

const isAddPetOpen = ref(false);
const isDeletePetOpen = ref(false);
const petToDelete = ref(null);
const showDetail = ref(false);
const selectedPet = ref(null);

const route = useRoute();

const pets = ref([]);

// small helpers (moved out to avoid recreating per-item)
const formatDMY = (d) => {
  const dd = String(d.getDate()).padStart(2, "0");
  const mm = String(d.getMonth() + 1).padStart(2, "0");
  const yyyy = d.getFullYear();
  return `${dd}/${mm}/${yyyy}`;
};

const computeAgeDisplay = (v) => {
  if (v === null || v === undefined || v === "") return "-";
  const s = String(v).trim();
  if (/^\d+$/.test(s)) {
    const n = parseInt(s, 10);
    return n <= 0 ? "Dưới 1 tuổi" : `${n} tuổi`;
  }
  const parsed = Date.parse(s);
  if (!isNaN(parsed)) {
    const dob = new Date(parsed);
    const now = new Date();
    let years = now.getFullYear() - dob.getFullYear();
    const monthDiff = now.getMonth() - dob.getMonth();
    if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < dob.getDate()))
      years -= 1;
    if (years > 0) return `${years} tuổi`;
    let months =
      (now.getFullYear() - dob.getFullYear()) * 12 +
      (now.getMonth() - dob.getMonth());
    if (now.getDate() < dob.getDate()) months -= 1;
    if (months < 0) months = 0;
    return `${months} tháng`;
  }
  return s;
};

// Map backend gender values to Vietnamese display values
const mapGender = (raw) => {
  if (raw === null || raw === undefined) return "-";
  const s = String(raw).trim().toLowerCase();
  if (!s) return "-";
  // common english values
  if (s === "male" || s === "m" || s === "đực" || s === "duc") return "Đực";
  if (s === "female" || s === "f" || s === "cái" || s === "cai") return "Cái";
  // vietnamese words perhaps already
  if (s === "đực" || s === "duc") return "Đực";
  if (s === "cái" || s === "cai") return "Cái";
  // fallback: capitalize first letter
  return raw.charAt(0).toUpperCase() + raw.slice(1);
};

// Resolve a single path into a canonical URL (used as first candidate)
const resolveImageUrl = (imagePath) => {
  if (!imagePath) return PLACEHOLDER_IMAGE;
  const p = String(imagePath).trim();
  if (p.startsWith("http://") || p.startsWith("https://")) return p;
  // If begins with /storage or storage/, use BASE_HOST + /storage/...
  if (p.startsWith("/storage/") || p.startsWith("storage/"))
    return `${BASE_HOST}/storage/${p
      .replace(/^\/+/, "")
      .replace(/^storage\//, "")}`;
  // If relative path like thu_cungs/..., assume stored in storage
  return `${BASE_HOST}/storage/${encodeURI(p)}`;
};

const mapBackendPetToCard = (item) => {
  const imagePath = item.anh_dai_dien || "";
  const candidates = getImageCandidates(imagePath);
  const imageUrl = candidates.length ? candidates[0] : PLACEHOLDER_IMAGE;

  const type = item.loai_thu_cung || "";
  const tagClass =
    type.toLowerCase().includes("cho") || type === "dog"
      ? "bg-amber-50 text-amber-700"
      : type.toLowerCase().includes("meo") || type === "cat"
      ? "bg-blue-50 text-sky-500"
      : "bg-zinc-100 text-zinc-500";

  const age = computeAgeDisplay(item.tuoi_thu_cung);

  return {
    id: item.id,
    name: item.ten_thu_cung || "Không rõ",
    breed: item.giong_thu_cung || "-",
    type: type || "-",
    age: age,
    // keep human-friendly weight for display, but also expose numeric value for edit
    weight: item.can_nang ? `${item.can_nang} kg` : "-",
    weightValue: item.can_nang || "",
    gender: mapGender(item.gioi_tinh),
    imageCard: imageUrl || PLACEHOLDER_IMAGE,
    // expose raw/backend fields so detail popup can pre-fill editable inputs
    loai_thu_cung: item.loai_thu_cung || "",
    giong_thu_cung: item.giong_thu_cung || "",
    birthDate: item.tuoi_thu_cung || "",
    gioi_tinh_raw: item.gioi_tinh || "",
    anh_dai_dien_url: item.anh_dai_dien_url || imageUrl || "",
    // keep original DB path and precomputed candidates for retries
    rawPath: item.anh_dai_dien || "",
    __candidates: candidates,
    tagClass,
    reminderBg: "bg-zinc-50 border-zinc-400",
    reminderTitle: item.upcomingVaccination || "Chưa có lịch hẹn",
    reminderTitleClass: "text-zinc-700",
    reminderDetail: item.reminderDetail || "",
    reminderDetailClass: "text-gray-500",
    vaccinations: item.vaccinations || [],
    medicalRecords: item.medical_records || [],
    // raw backend data intentionally not exposed in UI
  };
};

const handleImgError = (ev, pet) => {
  try {
    const failed = ev.target.src;
    pet.__triedImgs = pet.__triedImgs || [];
    pet.__triedImgs.push(failed);
    const candidates =
      pet.__candidates || getImageCandidates(pet.rawPath || "");
    const next = candidates.find((c) => !pet.__triedImgs.includes(c));
    if (next) {
      ev.target.src = next;
      pet.imageCard = next;
      return;
    }
    ev.target.src = PLACEHOLDER_IMAGE;
    pet.imageCard = PLACEHOLDER_IMAGE;
  } catch (e) {
    console.error("handleImgError", e);
    try {
      ev.target.src = PLACEHOLDER_IMAGE;
    } catch (e) {}
  }
};

const getImageCandidates = (rawPath) => {
  const p = String(rawPath || "").trim();
  if (!p) return [PLACEHOLDER_IMAGE];
  const primary = resolveImageUrl(p);
  const host = BASE_HOST;
  const list = [primary];
  if (!p.startsWith("/")) list.push(`${host}/${encodeURI(p)}`);
  list.push(`${host}/storage/${encodeURI(p.replace(/^\/+/, ""))}`);
  if (p.startsWith("/")) list.push(`${host}${p}`);
  list.push(p);
  return [...new Set(list)].filter(Boolean);
};

const fetchPets = async () => {
  try {
    // ensure Authorization header is present when token stored
    try {
      const t = (await import("@/utils/auth")).getToken();
      if (t) axios.defaults.headers.common["Authorization"] = `Bearer ${t}`;
    } catch (e) {}
    // Request all records from the backend so frontend can display full data
    const res = await axios.get(`${API_BASE}/thu-cung?all=1`);
    // handle both paginated and non-paginated responses
    let list = [];
    if (res.data && res.data.data) {
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
    }
    pets.value = list.map(mapBackendPetToCard);
  } catch (err) {
    console.warn("Không thể lấy danh sách thú cưng từ API:", err);
    // keep empty list
  }
};

onMounted(() => {
  fetchPets();
  if (route.query.action === 'add-pet') {
    isAddPetOpen.value = true;
  }
});

const openDeletePopup = (pet) => {
  petToDelete.value = pet;
  isDeletePetOpen.value = true;
};

const handleDeletePet = async (pet) => {
  // pet may be passed by the child or use the selected petToDelete
  const target = pet || petToDelete.value;
  if (!target) return;

  try {
    // call backend delete endpoint
    // ensure auth header
    try {
      const t = (await import("@/utils/auth")).getToken();
      if (t) axios.defaults.headers.common["Authorization"] = `Bearer ${t}`;
    } catch (e) {}
    
    const response = await axios.delete(`${API_BASE}/thu-cung/${target.id}`);

    // remove from local list
    pets.value = pets.value.filter((p) => p.id !== target.id);
    isDeletePetOpen.value = false;
    petToDelete.value = null;

    showSuccessToast(
      "Xóa thành công",
      `Đã xóa thú cưng ${target.ten_thu_cung || target.name} khỏi danh sách`
    );
  } catch (err) {
    console.error("Lỗi khi xóa thú cưng:", err);
    
    // Hiển thị thông báo lỗi chi tiết từ server
    let errorMessage = "Không thể xóa thú cưng. Vui lòng thử lại.";
    
    if (err.response?.data?.message) {
      errorMessage = err.response.data.message;
    } else if (err.response?.status === 403) {
      errorMessage = "Bạn không có quyền xóa thú cưng này.";
    } else if (err.response?.status === 400) {
      errorMessage = err.response.data.message || "Không thể xóa thú cưng vì còn lịch hẹn đang hoạt động.";
    }
    
    alert(errorMessage);
    // keep popup open so user can retry or cancel
  }
};

const openDetail = (pet) => {
  selectedPet.value = pet;
  showDetail.value = true;
};

const buildFormDataForCreate = (data) => {
  const fd = new FormData();
  fd.append("ten_thu_cung", data.name || "");
  fd.append("loai_thu_cung", data.species || "");
  fd.append("giong_thu_cung", data.breed || "");
  // backend expects a date for 'tuoi_thu_cung'
  if (data.dateOfBirth) fd.append("tuoi_thu_cung", data.dateOfBirth);
  if (data.gender) fd.append("gioi_tinh", data.gender);
  if (data.weight) fd.append("can_nang", data.weight);
  if (data.avatar instanceof File) fd.append("anh_dai_dien", data.avatar);
  return fd;
};

const handleAddPet = async (data) => {
  try {
    const fd = buildFormDataForCreate(data);
    // ensure auth header
    try {
      const t = (await import("@/utils/auth")).getToken();
      if (t) axios.defaults.headers.common["Authorization"] = `Bearer ${t}`;
    } catch (e) {}
    const res = await axios.post(`${API_BASE}/thu-cung`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    if (res.data && res.data.data) {
      const created = mapBackendPetToCard(res.data.data);
      // add to the top
      pets.value = [created, ...pets.value];
      isAddPetOpen.value = false;
      showSuccessToast("Thêm thú cưng", "Thêm thú cưng thành công");
    } else {
      console.warn("API trả về dữ liệu không hợp lệ khi tạo thú cưng", res);
    }
  } catch (err) {
    console.error("Lỗi khi thêm thú cưng:", err);
    alert("Không thể thêm thú cưng. Vui lòng thử lại.");
  }
};

const onPetUpdated = (backendItem) => {
  // backendItem is already formatted by the API (formatThuCung)
  const mapped = mapBackendPetToCard(backendItem);
  // use loose equality when comparing IDs to avoid string/number mismatches
  pets.value = pets.value.map((p) => (p.id == mapped.id ? mapped : p));
  // update selected pet shown in modal
  selectedPet.value = mapped;
  showSuccessToast("Cập nhật", "Thông tin thú cưng đã được cập nhật");
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600;700&display=swap");

.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
