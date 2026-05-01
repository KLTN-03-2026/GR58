<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    @click.self="close"
  >
    <div
      class="bg-white border !border-black/15 rounded-[10px] w-full max-w-[512px] max-h-[90vh] overflow-y-auto m-4"
    >
      <div class="flex flex-col gap-3 p-4">
        <!-- Header -->
        <div class="flex flex-col gap-1">
          <h2 class="text-lg font-bold text-black leading-tight">
            Thêm thú cưng mới
          </h2>
          <p class="text-sm font-medium text-gray-500 leading-tight">
            Nhập thông tin thú cưng của bạn để quản lý sức khỏe tốt hơn
          </p>
        </div>

        <!-- Form Content -->
        <div class="flex flex-col gap-3">
          <!-- Avatar Upload Section -->
          <div class="flex flex-col items-center gap-2">
            <div class="relative">
              <div
                class="w-20 h-20 rounded-full bg-teal-100 border-2 border-teal-300 flex items-center justify-center overflow-hidden"
              >
                <img
                  v-if="avatarPreview"
                  :src="avatarPreview"
                  alt="Pet avatar"
                  class="w-full h-full object-cover"
                />
                <CameraIcon v-else />
              </div>
              <button
                type="button"
                @click="triggerFileInput"
                class="absolute bottom-0 right-0 w-7 h-7 bg-[#5a9690] rounded-full flex items-center justify-center hover:bg-teal-800 transition"
              >
                <UploadIcon class="w-4 h-4 text-white" />
              </button>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFileChange"
              />
            </div>
            <p class="text-xs font-medium text-gray-500">
              Ảnh đại diện (không bắt buộc)
            </p>
          </div>

          <!-- Tên thú cưng -->
          <div class="flex flex-col gap-1.5">
            <label class="flex gap-1 items-center">
              <span class="text-sm font-semibold text-black">Tên thú cưng</span>
              <span class="text-sm font-semibold text-red-500">*</span>
            </label>
            <input
              v-model="formData.name"
              type="text"
              placeholder="Ví dụ: Miu, Bống..."
              class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-lg text-sm font-medium placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
            />
          </div>

          <!-- Loài -->
          <div class="flex flex-col gap-1.5">
            <label class="flex gap-1 items-center">
              <span class="text-sm font-semibold text-black">Loài</span>
              <span class="text-sm font-semibold text-red-500">*</span>
            </label>
            <div class="flex flex-row gap-4 items-center">
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="speciesType"
                  type="radio"
                  value="dog"
                  class="w-4 h-4 accent-teal-600"
                />
                <span class="text-sm font-semibold text-black">Chó</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="speciesType"
                  type="radio"
                  value="cat"
                  class="w-4 h-4 accent-teal-600"
                />
                <span class="text-sm font-semibold text-black">Mèo</span>
              </label>
              <label class="flex items-center gap-2 cursor-pointer">
                <input
                  v-model="speciesType"
                  type="radio"
                  value="other"
                  class="w-4 h-4 accent-teal-600"
                />
                <span class="text-sm font-semibold text-black">Khác</span>
              </label>
            </div>

            <!-- Dropdown cho loài khác -->
            <div v-if="speciesType === 'other'" class="relative mt-1">
              <select
                v-model="formData.species"
                class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-md text-sm text-gray-600 appearance-none focus:outline-none focus:ring-2 focus:ring-teal-500"
              >
                <option value="">Chọn loài</option>
                <option value="bird">Chim</option>
                <option value="parrot">Vẹt</option>
                <option value="hamster">Chuột Hamster</option>
                <option value="rabbit">Thỏ</option>
                <option value="squirrel">Sóc</option>
                <option value="other_species">Loài khác</option>
              </select>
              <ChevronDownIcon
                class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
              />
            </div>
          </div>

          <!-- 2-Column Grid for Details -->
          <div class="grid grid-cols-2 gap-x-4 gap-y-3">
            <!-- Giống -->
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-semibold text-black">Giống</label>
              <div class="relative">
                <select
                  v-model="formData.breed"
                  class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-md text-sm text-gray-600 appearance-none focus:outline-none focus:ring-2 focus:ring-teal-500"
                >
                  <option value="">Chọn giống</option>
                  <option value="golden-retriever">Golden Retriever</option>
                  <option value="husky">Husky</option>
                  <option value="poodle">Poodle</option>
                  <option value="persian">Persian</option>
                  <option value="siamese">Siamese</option>
                  <option value="scottish-fold">Scottish Fold</option>
                  <option value="other">Khác</option>
                </select>
                <ChevronDownIcon
                  class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                />
              </div>
            </div>

            <!-- Ngày sinh / Tuổi -->
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-semibold text-black"
                >Ngày sinh / Tuổi</label
              >
              <div class="relative">
                <input
                  v-model="formData.dateOfBirth"
                  type="date"
                  class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-md text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-teal-500"
                />
              </div>
            </div>

            <!-- Giới tính -->
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-semibold text-black">Giới tính</label>
              <div class="flex flex-row gap-4 h-9 items-center">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formData.gender"
                    type="radio"
                    value="male"
                    class="w-4 h-4 accent-teal-600"
                  />
                  <span class="text-sm font-semibold text-black">Đực</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                  <input
                    v-model="formData.gender"
                    type="radio"
                    value="female"
                    class="w-4 h-4 accent-teal-600"
                  />
                  <span class="text-sm font-semibold text-black">Cái</span>
                </label>
              </div>
            </div>

            <!-- Cân nặng -->
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-semibold text-black"
                >Cân nặng (kg)</label
              >
              <input
                v-model="formData.weight"
                type="number"
                step="0.1"
                placeholder="Ví dụ: 5.5"
                class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-lg text-sm font-medium placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-teal-500"
              />
            </div>
          </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex gap-6 justify-end pt-2">
          <button
            type="button"
            @click="close"
            class="h-9 px-4 py-2 bg-white border !border-black/15 rounded-lg text-sm font-semibold text-black hover:bg-gray-50 transition"
          >
            Hủy
          </button>
          <button
            type="button"
            @click="handleSubmit"
            class="h-9 px-4 py-2 bg-[#5a9690] rounded-lg text-sm font-semibold text-white flex items-center gap-2 hover:bg-teal-800 transition"
          >
            <span>Lưu thông tin</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from "vue";
import CameraIcon from "@/assets/svg/camera.svg";
import UploadIcon from "@/assets/svg/upload.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["close", "submit", "reset"]);

const fileInput = ref(null);
const avatarPreview = ref(null);
const speciesType = ref("");

const formData = reactive({
  name: "",
  species: "",
  breed: "",
  dateOfBirth: "",
  gender: "",
  weight: "",
  avatar: null,
});

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    formData.avatar = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const close = () => {
  emit("close");
  setTimeout(resetForm, 300);
};

const handleSubmit = () => {
  if (!formData.name || !formData.species) {
    alert("Vui lòng điền đầy đủ thông tin bắt buộc");
    return;
  }
  emit("submit", { ...formData });
};

watch(() => props.isOpen, (val) => {
  if (!val) resetForm();
});

watch(speciesType, (newVal) => {
  if (newVal === "dog") formData.species = "dog";
  else if (newVal === "cat") formData.species = "cat";
  else if (newVal === "other") formData.species = "";
});

const resetForm = () => {
  speciesType.value = "";
  formData.name = "";
  formData.species = "";
  formData.breed = "";
  formData.dateOfBirth = "";
  formData.gender = "";
  formData.weight = "";
  formData.avatar = null;
  avatarPreview.value = null;
};

// Expose resetForm to parent
defineExpose({
  resetForm,
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Nunito+Sans:ital,wght@0,400;0,500;0,600;0,700;1,700&family=Nunito:wght@400&display=swap");

input[type="date"]::-webkit-calendar-picker-indicator {
  cursor: pointer;
}
</style>
