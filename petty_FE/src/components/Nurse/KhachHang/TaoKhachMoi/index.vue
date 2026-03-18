<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-6"
    @click.self="closeModal"
  >
    <div
      class="bg-white rounded-[14px] shadow-lg w-full max-w-[500px] max-h-[90vh] flex flex-col overflow-hidden"
    >
      <!-- Modal Header -->
      <div class="px-6 pt-6 pb-4 border-b !border-gray-300">
        <h2 class="text-lg font-semibold text-black">
          Thêm Hồ Sơ Khách Hàng Mới
        </h2>
        <p class="text-sm text-gray-600 mt-1">
          Tạo hồ sơ mới cho khách hàng và thú cưng
        </p>
      </div>

      <!-- Modal Content (Scrollable) -->
      <div class="flex-1 overflow-y-auto px-6 py-6">
        <div class="flex flex-col gap-6">
          <!-- THÔNG TIN CHỦ NUÔI (Gray Section) -->
          <div class="bg-gray-50 border-2 !border-gray-200 rounded-[14px] p-4">
            <!-- Section Title -->
            <div class="flex items-center gap-2 mb-4">
              <!-- <img :src="iconUser" alt="User" class="w-5 h-5" /> -->
              <h3 class="text-base font-bold text-black">THÔNG TIN CHỦ NUÔI</h3>
            </div>

            <!-- Phone Number -->
            <div class="flex flex-col mb-4">
              <label class="text-sm font-medium text-black mb-2">
                Số điện thoại
                <span class="text-red-600 ml-1">*</span>
              </label>
              <input
                v-model="formData.phone"
                type="tel"
                placeholder="0912345678"
                class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <!-- Full Name -->
            <div class="flex flex-col">
              <label class="text-sm font-medium text-black mb-2">
                Họ và tên
                <span class="text-red-600 ml-1">*</span>
              </label>
              <input
                v-model="formData.fullName"
                type="text"
                placeholder="Trần Thị B"
                class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
              <p class="text-xs text-gray-600 mt-1">
                Tự động viết hoa chữ cái đầu
              </p>
            </div>
          </div>

          <!-- THÔNG TIN THÚ CƯNG (Blue Section) -->
          <div class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-4">
            <!-- Section Title -->
            <div class="flex items-center gap-2 mb-4">
              <!-- <img :src="iconPet" alt="Pet" class="w-5 h-5" /> -->
              <h3 class="text-base font-bold text-black">THÔNG TIN THÚ CƯNG</h3>
            </div>

            <!-- Pet Name -->
            <div class="flex flex-col mb-4">
              <label class="text-sm font-medium text-black mb-2">
                Tên thú cưng
                <span class="text-red-600 ml-1">*</span>
              </label>
              <input
                v-model="formData.petName"
                type="text"
                placeholder="VD: Milo, Mimi..."
                class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <!-- Species (Radio Buttons) -->
            <div class="flex flex-col gap-2 mb-4">
              <label class="text-sm font-medium text-black">
                Loài vật
                <span class="text-red-600 ml-1">*</span>
              </label>
              <div class="flex items-center gap-4">
                <label
                  v-for="species in speciesOptions"
                  :key="species.value"
                  class="flex items-center gap-2 cursor-pointer"
                >
                  <div class="relative">
                    <input
                      v-model="formData.species"
                      type="radio"
                      :value="species.value"
                      class="appearance-none w-4 h-4 border border-gray-300 rounded-full shadow-sm checked:bg-white checked:border-[#009689] cursor-pointer"
                    />
                    <div
                      v-if="formData.species === species.value"
                      class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-2 h-2 pointer-events-none"
                    >
                      <!-- <img :src="iconCheck" alt="Check" class="w-full h-full" /> -->
                      <div
                        class="w-full h-full bg-[#009689] rounded-full"
                      ></div>
                    </div>
                  </div>
                  <span class="text-sm font-medium text-black">
                    {{ species.label }}
                  </span>
                </label>
              </div>
            </div>

            <!-- Breed -->
            <div class="flex flex-col mb-4">
              <label class="text-sm font-medium text-black mb-2">
                Giống (Breed)
                <span class="text-red-600 ml-1">*</span>
              </label>
              <div class="relative">
                <select
                  v-model="formData.breed"
                  class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 w-full text-sm text-gray-900 appearance-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                >
                  <option value="" disabled>Chọn giống...</option>
                  <option
                    v-for="breed in availableBreeds"
                    :key="breed"
                    :value="breed"
                  >
                    {{ breed }}
                  </option>
                </select>
                <!-- <img
                  :src="iconChevron"
                  alt="Chevron"
                  class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none"
                /> -->
              </div>
              <div class="flex items-center gap-1 mt-1">
                <!-- <img :src="iconInfo" alt="Info" class="w-3 h-3" /> -->
                <p class="text-xs text-gray-600">
                  Danh sách giống thay đổi theo loài
                </p>
              </div>
            </div>

            <!-- Birth Date -->
            <div class="flex flex-col mb-4">
              <label class="text-sm font-medium text-black mb-2">
                Ngày sinh
                <span class="text-red-600 ml-1">*</span>
              </label>
              <input
                v-model="formData.birthDate"
                type="date"
                class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
              />
            </div>

            <!-- Notes -->
            <div class="flex flex-col mb-4">
              <label class="text-sm font-medium text-black mb-2">
                Ghi chú
              </label>
              <textarea
                v-model="formData.notes"
                rows="3"
                placeholder="VD: Hay sủa, hiền lành, dễ thương..."
                class="bg-white border !border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
              ></textarea>
            </div>

            <!-- Add Pet Button -->
            <button
              class="bg-[#00a63e] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#008833] transition-colors"
              @click="addPet"
            >
              <!-- <img :src="iconPlus" alt="Plus" class="w-4 h-4" /> -->
              <span class="text-sm font-medium text-white">
                Thêm Thú Cưng
              </span>
            </button>
          </div>
        </div>
      </div>

      <!-- Modal Footer -->
      <div
        class="px-6 py-4 border-t !border-gray-300 flex items-center justify-end gap-3"
      >
        <button
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2.5 h-10 hover:bg-gray-50 transition-colors"
          @click="closeModal"
        >
          <span class="text-sm font-medium text-black"> Hủy </span>
        </button>
        <button
          class="bg-[#9810fa] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#7a0cc9] transition-colors"
          @click="saveProfile"
        >
          <!-- <img :src="iconSave" alt="Save" class="w-4 h-4" /> -->
          <span class="text-sm font-medium text-white"> Lưu hồ sơ </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

// Emits
const emit = defineEmits(["close", "submit"]);

// Icons
const iconUser =
  "https://www.figma.com/api/mcp/asset/b9bbdb94-a032-470d-8a79-5c02b70d39df";
const iconPet =
  "https://www.figma.com/api/mcp/asset/55e321fc-1581-494e-a165-15c8317238bb";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/f2d3e2d3-0809-4d13-9240-bd7a127750f2";
const iconChevron =
  "https://www.figma.com/api/mcp/asset/2bb7bd10-d9fa-46f4-8c76-259dd73e0fd2";
const iconInfo =
  "https://www.figma.com/api/mcp/asset/8e903663-fc0a-4416-a8a8-e99edb44529d";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/8244750d-92b0-4418-84a4-bbb408b84406";
const iconSave =
  "https://www.figma.com/api/mcp/asset/e39ca45c-c65b-464c-aee8-12fd565db54f";

// Form data
const formData = ref({
  phone: "",
  fullName: "",
  petName: "",
  species: "dog",
  breed: "",
  birthDate: "",
  notes: "",
});

// Species options
const speciesOptions = [
  { value: "dog", label: "Chó" },
  { value: "cat", label: "Mèo" },
  { value: "other", label: "Khác" },
];

// Breed options based on species
const breedsBySpecies = {
  dog: [
    "Poodle",
    "Golden Retriever",
    "Husky",
    "Chihuahua",
    "Corgi",
    "Bulldog",
    "Chó ta",
  ],
  cat: ["Mèo Anh lông ngắn", "Mèo Ba Tư", "Mèo Xiêm", "Mèo Bengal", "Mèo ta"],
  other: ["Hamster", "Thỏ", "Chim", "Rùa", "Khác"],
};

// Computed breeds based on selected species
const availableBreeds = computed(() => {
  return breedsBySpecies[formData.value.species] || [];
});

// Watch species change to reset breed
watch(
  () => formData.value.species,
  () => {
    formData.value.breed = "";
  }
);

// Auto-capitalize full name
watch(
  () => formData.value.fullName,
  (newValue) => {
    if (newValue) {
      formData.value.fullName = newValue
        .split(" ")
        .map(
          (word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        )
        .join(" ");
    }
  }
);

// Methods
const closeModal = () => {
  emit("close");
  resetForm();
};

const resetForm = () => {
  formData.value = {
    phone: "",
    fullName: "",
    petName: "",
    species: "dog",
    breed: "",
    birthDate: "",
    notes: "",
  };
};

const addPet = () => {
  console.log("Add another pet");
  // TODO: Implement add multiple pets functionality
};

const saveProfile = () => {
  // Validate required fields
  if (
    !formData.value.phone ||
    !formData.value.fullName ||
    !formData.value.petName ||
    !formData.value.breed ||
    !formData.value.birthDate
  ) {
    alert("Vui lòng điền đầy đủ thông tin bắt buộc (*)");
    return;
  }

  emit("submit", { ...formData.value });
  closeModal();
};

// Watch modal open to reset form
watch(
  () => props.isOpen,
  (newValue) => {
    if (!newValue) {
      resetForm();
    }
  }
);
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}

/* Custom radio button styling */
input[type="radio"]:checked {
  position: relative;
}
</style>
