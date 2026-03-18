<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[100] p-6"
      @click.self="closeModal"
    >
      <div
        class="bg-white rounded-[14px] shadow-lg w-full max-w-[600px] max-h-[90vh] flex flex-col overflow-hidden"
      >
        <!-- Header -->
        <div class="px-6 pt-6 pb-4 border-b !border-gray-300">
          <h2 class="text-lg font-semibold text-black">Thêm thú cưng mới</h2>
          <p class="text-sm text-gray-600 mt-1">
            Thêm thú cưng cho khách hàng {{ customerName || "..." }}
          </p>
        </div>

        <!-- Content (Scrollable) -->
        <div class="flex-1 overflow-y-auto px-6 py-6">
          <div class="flex flex-col gap-6">
            <div
              class="bg-blue-50 border-2 !border-[#bedbff] rounded-[14px] p-6"
            >
              <!-- Section Header -->
              <div class="flex items-center gap-2 mb-6">
                <h3 class="text-base font-bold text-black">
                  THÔNG TIN THÚ CƯNG
                </h3>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pet Name -->
                <div class="col-span-2">
                  <label class="text-sm font-medium text-black mb-2 block">
                    Tên thú cưng
                    <span class="text-red-600 ml-1">*</span>
                  </label>
                  <input
                    v-model="formData.name"
                    type="text"
                    placeholder="VD: Milo, Mimi..."
                    class="w-full bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  />
                </div>

                <!-- Species -->
                <div class="col-span-2">
                  <label class="text-sm font-medium text-black mb-2 block">
                    Loài vật
                    <span class="text-red-600 ml-1">*</span>
                  </label>
                  <div class="flex items-center gap-6">
                    <label
                      v-for="species in speciesOptions"
                      :key="species.value"
                      class="flex items-center gap-2 cursor-pointer"
                    >
                      <div class="relative flex items-center justify-center">
                        <input
                          v-model="formData.species"
                          type="radio"
                          :value="species.value"
                          class="appearance-none w-5 h-5 border border-gray-300 rounded-full shadow-sm checked:border-[#009689] cursor-pointer"
                        />
                        <div
                          v-if="formData.species === species.value"
                          class="absolute w-3 h-3 bg-[#009689] rounded-full pointer-events-none"
                        ></div>
                      </div>
                      <span class="text-sm font-medium text-black">
                        {{ species.label }}
                      </span>
                    </label>
                  </div>
                </div>

                <!-- Breed -->
                <div class="col-span-1">
                  <label class="text-sm font-medium text-black mb-2 block">
                    Giống (Breed)
                    <span class="text-red-600 ml-1">*</span>
                  </label>
                  <div class="relative">
                    <select
                      v-model="formData.breed"
                      class="w-full bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 appearance-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
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
                    <!-- Custom arrow could go here but skipping for simplicity as per refactor style -->
                  </div>
                </div>

                <!-- Birth Date -->
                <div class="col-span-1">
                  <label class="text-sm font-medium text-black mb-2 block">
                    Ngày sinh/Tuổi
                  </label>
                  <input
                    v-model="formData.birthDate"
                    type="date"
                    class="w-full bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  />
                </div>

                <!-- Gender -->
                <div class="col-span-1">
                  <label class="text-sm font-medium text-black mb-2 block">
                    Giới tính
                  </label>
                  <div class="relative">
                    <select
                      v-model="formData.sex"
                      class="w-full bg-white border !border-gray-300 rounded-lg px-3 py-2.5 h-11 text-sm text-gray-900 appearance-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    >
                      <option value="male">Đực</option>
                      <option value="female">Cái</option>
                      <option value="unknown">Không rõ</option>
                    </select>
                  </div>
                </div>

                <!-- Notes -->
                <div class="col-span-2">
                  <label class="text-sm font-medium text-black mb-2 block">
                    Ghi chú
                  </label>
                  <textarea
                    v-model="formData.notes"
                    rows="3"
                    placeholder="Đặc điểm nhận dạng, tính cách..."
                    class="w-full bg-white border !border-gray-300 rounded-lg px-3 py-2.5 text-sm text-gray-900 resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  ></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
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
            class="bg-[#155dfc] rounded-lg px-6 py-2.5 h-10 flex items-center gap-2 hover:bg-[#0d4ed4] transition-colors"
            @click="handleSubmit"
          >
            <span class="text-sm font-medium text-white"> Thêm thú cưng </span>
          </button>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  customerName: {
    type: String,
    default: "",
  },
});

const emit = defineEmits(["close", "submit"]);

const formData = ref({
  name: "",
  species: "dog",
  breed: "",
  birthDate: "",
  sex: "male",
  notes: "",
});

const speciesOptions = [
  { value: "dog", label: "Chó" },
  { value: "cat", label: "Mèo" },
  { value: "other", label: "Khác" },
];

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

const availableBreeds = computed(() => {
  return breedsBySpecies[formData.value.species] || [];
});

watch(
  () => formData.value.species,
  () => {
    formData.value.breed = "";
  }
);

watch(
  () => props.isOpen,
  (newVal) => {
    if (newVal) {
      // Reset form
      formData.value = {
        name: "",
        species: "dog",
        breed: "",
        birthDate: "",
        sex: "male",
        notes: "",
      };
    }
  }
);

const closeModal = () => {
  emit("close");
};

const handleSubmit = () => {
  if (!formData.value.name || !formData.value.species) {
    alert("Vui lòng điền tên và loài thú cưng");
    return;
  }
  emit("submit", { ...formData.value });
  closeModal();
};
</script>

<style scoped>
/* No specific styles needed as we use Tailwind */
</style>
