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
              <label
                v-for="option in speciesTypeOptions"
                :key="option.value"
                class="flex items-center gap-2 cursor-pointer"
              >
                <input
                  v-model="speciesType"
                  type="radio"
                  :value="option.value"
                  class="w-4 h-4 accent-teal-600"
                />
                <span class="text-sm font-semibold text-black">
                  {{ option.label }}
                </span>
              </label>
            </div>

            <!-- Dropdown cho loài khác -->
            <div v-if="speciesType === 'other'" class="relative mt-1">
              <select
                v-model="otherSpeciesKey"
                class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-md text-sm text-gray-600 appearance-none focus:outline-none focus:ring-2 focus:ring-teal-500"
              >
                <option value="">Chọn loài</option>
                <option
                  v-for="option in otherSpeciesOptions"
                  :key="option.value"
                  :value="option.value"
                >
                  {{ option.label }}
                </option>
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
                  :disabled="!selectedSpeciesKey"
                  class="w-full h-9 px-3 py-1 bg-gray-50 border !border-black/15 rounded-md text-sm text-gray-600 appearance-none focus:outline-none focus:ring-2 focus:ring-teal-500 disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed"
                >
                  <option value="">
                    {{ selectedSpeciesKey ? "Chọn giống" : "Chọn loài trước" }}
                  </option>
                  <option
                    v-for="breed in availableBreeds"
                    :key="breed"
                    :value="breed"
                  >
                    {{ breed }}
                  </option>
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
                  :max="todayIso"
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
                min="0.01"
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
import { computed, reactive, ref, watch } from "vue";
import CameraIcon from "@/assets/svg/camera.svg";
import UploadIcon from "@/assets/svg/upload.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";

const speciesTypeOptions = [
  { value: "dog", label: "Chó" },
  { value: "cat", label: "Mèo" },
  { value: "other", label: "Khác" },
];

const otherSpeciesOptions = [
  { value: "bird", label: "Chim" },
  { value: "parrot", label: "Vẹt" },
  { value: "hamster", label: "Chuột Hamster" },
  { value: "rabbit", label: "Thỏ" },
  { value: "squirrel", label: "Sóc" },
  { value: "guinea_pig", label: "Chuột lang" },
  { value: "turtle", label: "Rùa" },
  { value: "hedgehog", label: "Nhím kiểng" },
  { value: "ferret", label: "Chồn sương" },
  { value: "other_species", label: "Loài khác" },
];

const speciesLabelByKey = {
  dog: "Chó",
  cat: "Mèo",
  bird: "Chim",
  parrot: "Vẹt",
  hamster: "Chuột Hamster",
  rabbit: "Thỏ",
  squirrel: "Sóc",
  guinea_pig: "Chuột lang",
  turtle: "Rùa",
  hedgehog: "Nhím kiểng",
  ferret: "Chồn sương",
  other_species: "Loài khác",
};

const breedsBySpecies = {
  dog: [
    "Poodle",
    "Golden Retriever",
    "Husky",
    "Corgi",
    "Chihuahua",
    "Pug",
    "Shiba Inu",
    "Pomeranian",
    "Bulldog",
    "Becgie Đức",
    "Alaska",
    "Labrador Retriever",
    "Phốc Sóc",
    "Chó ta",
    "Giống khác",
  ],
  cat: [
    "Mèo Anh lông ngắn",
    "Mèo Ba Tư",
    "Mèo Xiêm",
    "Mèo Bengal",
    "Mèo Maine Coon",
    "Mèo Scottish Fold",
    "Mèo Ragdoll",
    "Mèo Sphynx",
    "Mèo Munchkin",
    "Mèo ta",
    "Giống khác",
  ],
  bird: [
    "Chào mào",
    "Sơn ca",
    "Yến phụng",
    "Hoàng yến",
    "Bồ câu",
    "Chim cảnh khác",
  ],
  parrot: [
    "Vẹt yến phụng",
    "Vẹt cockatiel",
    "Vẹt lovebird",
    "Vẹt macaw",
    "Vẹt xám châu Phi",
    "Vẹt khác",
  ],
  hamster: [
    "Hamster Bear",
    "Hamster Robo",
    "Hamster Campbell",
    "Hamster Winter White",
    "Hamster Syrian",
    "Hamster khác",
  ],
  rabbit: [
    "Thỏ Holland Lop",
    "Thỏ Mini Rex",
    "Thỏ Lionhead",
    "Thỏ New Zealand",
    "Thỏ Angora",
    "Thỏ ta",
    "Giống khác",
  ],
  squirrel: [
    "Sóc bay",
    "Sóc đất",
    "Sóc đỏ",
    "Sóc cảnh khác",
  ],
  guinea_pig: [
    "Guinea Pig lông ngắn",
    "Guinea Pig Abyssinian",
    "Guinea Pig Peru",
    "Guinea Pig Teddy",
    "Guinea Pig khác",
  ],
  turtle: [
    "Rùa tai đỏ",
    "Rùa sao",
    "Rùa hộp",
    "Rùa núi vàng",
    "Rùa cảnh khác",
  ],
  hedgehog: [
    "Nhím kiểng châu Phi",
    "Nhím kiểng albino",
    "Nhím kiểng khác",
  ],
  ferret: [
    "Ferret sable",
    "Ferret albino",
    "Ferret silver",
    "Ferret khác",
  ],
  other_species: ["Giống khác"],
};

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
const otherSpeciesKey = ref("");

const formData = reactive({
  name: "",
  species: "",
  breed: "",
  dateOfBirth: "",
  gender: "",
  weight: "",
  avatar: null,
});

const selectedSpeciesKey = computed(() => {
  if (speciesType.value === "dog" || speciesType.value === "cat") {
    return speciesType.value;
  }

  if (speciesType.value === "other") {
    return otherSpeciesKey.value;
  }

  return "";
});

const availableBreeds = computed(() => {
  return breedsBySpecies[selectedSpeciesKey.value] || [];
});

const todayIso = new Date().toISOString().split("T")[0];

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
  if (formData.dateOfBirth && formData.dateOfBirth > todayIso) {
    alert("Ngày sinh không được lớn hơn ngày hiện tại");
    return;
  }
  if (formData.weight && parseFloat(formData.weight) <= 0) {
    alert("Cân nặng phải lớn hơn 0");
    return;
  }
  emit("submit", { ...formData });
};

watch(() => props.isOpen, (val) => {
  if (!val) resetForm();
});

watch(speciesType, (newVal) => {
  formData.breed = "";

  if (newVal === "dog" || newVal === "cat") {
    otherSpeciesKey.value = "";
    formData.species = speciesLabelByKey[newVal];
    return;
  }

  if (newVal === "other") {
    formData.species = "";
    return;
  }

  formData.species = "";
});

watch(otherSpeciesKey, (newVal) => {
  formData.breed = "";
  formData.species = speciesLabelByKey[newVal] || "";
});

const resetForm = () => {
  speciesType.value = "";
  otherSpeciesKey.value = "";
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
