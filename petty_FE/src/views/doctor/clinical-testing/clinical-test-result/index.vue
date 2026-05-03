<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-6"
  >
    <!-- Upload Mode Modal -->
    <div
      v-if="mode === 'upload'"
      class="bg-white rounded-[14px] shadow-lg p-6 w-[880px] max-h-[90vh] flex flex-col"
    >
      <!-- Header -->
      <div class="flex flex-col gap-1 mb-6 pb-4 border-b !border-gray-300">
        <div class="flex items-center gap-2">
          <!-- <img :src="icons.perform" alt="" class="w-5 h-5" /> -->
          <h2 class="text-xl font-semibold text-black">
            Thực hiện & Trả kết quả
          </h2>
        </div>
        <p class="text-sm text-gray-600">Hồ sơ: {{ recordId }}</p>
      </div>

      <!-- Upload Section -->
      <div class="flex-1 overflow-y-auto mb-6">
        <div class="flex gap-6">
          <!-- Left Sidebar -->
          <div class="w-[250px] flex-shrink-0 flex flex-col gap-4">
            <!-- Patient Info Card -->
            <div
              class="bg-blue-50 border-2 !border-[#bedbff] rounded-lg p-4 flex flex-col gap-3 items-center"
            >
              <div class="border-b border-[#8ec5ff] pb-2 w-full">
                <p
                  class="text-sm text-[#1c398e] leading-5 tracking-[-0.1504px]"
                >
                  THÔNG TIN BỆNH NHÂN
                </p>
              </div>

              <!-- Pet Image -->
              <div
                class="w-24 h-24 rounded-full border-4 border-white shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)] overflow-hidden p-1"
              >
                <img
                  :src="patientInfo.image"
                  alt=""
                  class="w-full h-full object-cover rounded-full"
                />
              </div>

              <!-- Pet Details -->
              <div class="flex flex-col gap-1 w-full">
                <p
                  class="text-base font-bold text-[#101828] text-center tracking-[-0.3125px]"
                >
                  {{ patientInfo.name }}
                </p>
                <p class="text-xs text-[#4a5565] text-center leading-4">
                  {{ patientInfo.species }}
                </p>
                <p class="text-xs text-[#4a5565] text-center leading-4">
                  {{ patientInfo.age }} • {{ patientInfo.weight }}
                </p>
              </div>

              <!-- Owner Info -->
              <div
                class="border-t border-[#8ec5ff] pt-3 w-full flex flex-col gap-2"
              >
                <p class="text-xs font-bold text-[#1c398e] leading-4">
                  CHỦ NUÔI:
                </p>
                <p
                  class="text-sm text-[#101828] leading-5 tracking-[-0.1504px]"
                >
                  {{ patientInfo.ownerName }}
                </p>
                <p
                  class="text-sm text-[#101828] leading-5 tracking-[-0.1504px]"
                >
                  {{ patientInfo.ownerPhone }}
                </p>
              </div>
            </div>

            <!-- Order Info Card -->
            <div
              class="bg-purple-50 border-2 !border-[#e9d4ff] rounded-lg p-4 flex flex-col gap-2"
            >
              <div class="border-b border-[#dab2ff] pb-2">
                <p
                  class="text-sm text-[#59168b] leading-5 tracking-[-0.1504px]"
                >
                  Y LỆNH (ORDER)
                </p>
              </div>

              <div class="flex flex-col gap-2">
                <div class="flex gap-1">
                  <span
                    class="text-base font-bold text-[#101828] tracking-[-0.3125px]"
                    >Dịch vụ:</span
                  >
                  <span class="text-base text-[#101828] tracking-[-0.3125px]">{{
                    orderInfo.service
                  }}</span>
                </div>

                <div class="flex flex-col gap-2">
                  <span
                    class="text-base font-bold text-[#364153] tracking-[-0.3125px]"
                    >Lưu ý:</span
                  >
                  <p class="text-base text-[#364153] tracking-[-0.3125px]">
                    {{ orderInfo.note }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Content -->
          <div class="flex-1 flex flex-col gap-6">
            <!-- Upload Area -->
            <div class="flex flex-col gap-2">
              <div class="flex items-start gap-1">
                <label
                  class="text-sm font-medium text-[#101828] leading-[14px] tracking-[-0.1504px]"
                >
                  Ảnh/File đính kèm
                </label>
                <span
                  class="text-sm font-medium text-[#fb2c36] leading-[14px] tracking-[-0.1504px]"
                  >*</span
                >
              </div>

              <!-- Drop Zone -->
              <div
                class="relative bg-gray-50 border-2 !border-gray-300 border-dashed rounded-lg h-[232px] flex flex-col items-center justify-center cursor-pointer hover:bg-gray-100 transition-colors"
                :class="{ 'border-blue-400 bg-blue-50': isDragging }"
                @click="triggerFileInput"
                @dragover.prevent="isDragging = true"
                @dragleave.prevent="isDragging = false"
                @drop.prevent="handleDrop"
              >
                <!-- <img :src="icons.upload" alt="" class="w-12 h-12 mb-4" /> -->
                <p class="text-base text-gray-700 text-center mb-1">
                  Kéo thả ảnh vào đây hoặc click để chọn
                </p>
                <p
                  class="text-sm text-[#6a7282] text-center leading-5 tracking-[-0.1504px] mb-3"
                >
                  Hỗ trợ: JPG, PNG, PDF (Tối đa 10MB)
                </p>
                <button
                  class="bg-white border !border-gray-300 rounded-lg px-3 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                  type="button"
                  @click.stop="triggerFileInput"
                >
                  <!-- <img :src="icons.uploadBtn" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm font-medium text-gray-900">
                    Chọn file
                  </span>
                </button>
                <input
                  ref="fileInput"
                  type="file"
                  class="hidden"
                  multiple
                  accept="image/jpeg,image/png,application/pdf"
                  @change="handleFileSelect"
                />
              </div>

              <!-- Preview Images -->
              <div
                v-if="uploadedFiles.length > 0"
                class="grid grid-cols-3 gap-4 mt-4"
              >
                <div
                  v-for="(file, index) in uploadedFiles"
                  :key="index"
                  class="relative border !border-gray-300 rounded-lg aspect-square overflow-hidden group"
                >
                  <img
                    v-if="file.preview"
                    :src="file.preview"
                    :alt="file.name"
                    class="w-full h-full object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-full bg-gray-100 flex items-center justify-center"
                  >
                    <div class="w-16 h-16 rounded-full bg-gray-200"></div>
                  </div>
                  <button
                    class="absolute top-2 right-2 w-6 h-6 bg-[#fb2c36] rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                    @click="removeFile(index)"
                  >
                    <svg
                      class="w-4 h-4"
                      viewBox="0 0 16 16"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M12 4L4 12M4 4L12 12"
                        stroke="white"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Conclusion Textarea -->
            <div class="flex flex-col gap-2">
              <label
                class="text-sm font-medium text-[#101828] leading-[14px] tracking-[-0.1504px]"
              >
                Kết luận
              </label>
              <textarea
                v-model="conclusion"
                class="bg-gray-50 border !border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 h-16 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Nhập kết luận chẩn đoán hình ảnh hoặc xét nghiệm..."
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex items-center justify-end gap-3 pt-4 border-t !border-gray-300"
      >
        <button
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-10 text-sm font-medium text-gray-900 hover:bg-gray-50 transition-colors"
          @click="$emit('close')"
        >
          Hủy
        </button>
        <button
          class="bg-[#155dfc] rounded-lg px-4 py-2 h-10 flex items-center gap-2 text-sm font-medium text-white hover:bg-[#1350e0] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :disabled="uploadedFiles.length === 0"
          @click="handleSave"
        >
          <span>Lưu & Hoàn tất</span>
        </button>
      </div>
    </div>

    <!-- View Mode Modal -->
    <div
      v-else
      class="bg-white rounded-[14px] shadow-lg p-6 w-[810px] max-h-[90vh] flex flex-col"
    >
      <!-- Header -->
      <div class="flex flex-col gap-1 mb-6 pb-4 border-b !border-gray-300">
        <div class="flex items-center gap-2">
          <!-- <img :src="icons.view" alt="" class="w-5 h-5" /> -->
          <h2 class="text-xl font-semibold text-black">Xem kết quả</h2>
        </div>
        <p class="text-sm text-gray-600">Hồ sơ: {{ recordId }}</p>
      </div>

      <!-- View Section -->
      <div class="flex-1 overflow-y-auto mb-6">
        <div class="flex gap-6">
          <!-- Left Sidebar -->
          <div class="w-[250px] flex-shrink-0 flex flex-col gap-4">
            <!-- Patient Info Card -->
            <div
              class="bg-blue-50 border-2 !border-[#bedbff] rounded-lg p-4 flex flex-col gap-3 items-center"
            >
              <div class="border-b border-[#8ec5ff] pb-2 w-full">
                <p
                  class="text-sm text-[#1c398e] leading-5 tracking-[-0.1504px]"
                >
                  THÔNG TIN BỆNH NHÂN
                </p>
              </div>

              <!-- Pet Image -->
              <div
                class="w-24 h-24 rounded-full border-4 border-white shadow-[0px_4px_6px_-1px_rgba(0,0,0,0.1),0px_2px_4px_-2px_rgba(0,0,0,0.1)] overflow-hidden p-1"
              >
                <img
                  :src="resultData.petImage"
                  alt=""
                  class="w-full h-full object-cover rounded-full"
                />
              </div>

              <!-- Pet Details -->
              <div class="flex flex-col gap-1 w-full">
                <p
                  class="text-base font-bold text-[#101828] text-center tracking-[-0.3125px]"
                >
                  {{ resultData.petName }}
                </p>
                <p class="text-xs text-[#4a5565] text-center leading-4">
                  {{ resultData.petSpecies }}
                </p>
                <p class="text-xs text-[#4a5565] text-center leading-4">
                  {{ resultData.petAge }} • {{ resultData.petWeight }}
                </p>
              </div>

              <!-- Owner Info -->
              <div
                class="border-t border-[#8ec5ff] pt-3 w-full flex flex-col gap-2"
              >
                <p class="text-xs font-bold text-[#1c398e] leading-4">
                  CHỦ NUÔI:
                </p>
                <p
                  class="text-sm text-[#101828] leading-5 tracking-[-0.1504px]"
                >
                  {{ resultData.ownerName }}
                </p>
                <p
                  class="text-sm text-[#101828] leading-5 tracking-[-0.1504px]"
                >
                  {{ resultData.ownerPhone }}
                </p>
              </div>
            </div>

            <!-- Order Info Card -->
            <div
              class="bg-purple-50 border-2 !border-[#e9d4ff] rounded-lg p-4 flex flex-col gap-2"
            >
              <div class="border-b border-[#dab2ff] pb-2">
                <p class="text-sm text-[#59168b] font-medium">Y LỆNH (ORDER)</p>
              </div>

              <div class="flex flex-col gap-2">
                <div class="flex flex-col gap-1">
                  <span class="text-sm font-semibold text-black">Dịch vụ:</span>
                  <span class="text-sm text-gray-900">{{
                    orderInfo.service
                  }}</span>
                </div>

                <div class="flex flex-col gap-1">
                  <span class="text-sm font-semibold text-gray-700"
                    >Lưu ý:</span
                  >
                  <p class="text-sm text-gray-700">
                    {{ orderInfo.note }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Content -->
          <div class="flex-1 flex flex-col gap-4">
            <!-- Images Section -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-black">
                Ảnh/File đính kèm
              </label>

              <!-- Image Grid -->
              <div class="grid grid-cols-2 gap-5 h-[263px]">
                <div
                  v-for="(image, index) in resultData.images"
                  :key="index"
                  class="relative rounded-lg overflow-hidden group cursor-pointer"
                  @click="previewImage(index)"
                >
                  <img
                    v-if="image.url"
                    :src="image.url"
                    :alt="`Result ${index + 1}`"
                    class="w-full h-full object-cover rounded-lg"
                  />
                  <div
                    v-else
                    class="w-full h-full bg-gray-100 flex items-center justify-center rounded-lg"
                  >
                    <div class="w-16 h-16 rounded-full bg-gray-200"></div>
                  </div>
                  <div
                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-opacity flex items-center justify-center opacity-0 group-hover:opacity-100"
                  >
                    <svg
                      class="w-8 h-8"
                      viewBox="0 0 32 32"
                      fill="none"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M1.33334 16C1.33334 16 6.66667 5.33333 16 5.33333C25.3333 5.33333 30.6667 16 30.6667 16C30.6667 16 25.3333 26.6667 16 26.6667C6.66667 26.6667 1.33334 16 1.33334 16Z"
                        stroke="white"
                        stroke-width="2.66667"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                      <path
                        d="M16 20C18.2091 20 20 18.2091 20 16C20 13.7909 18.2091 12 16 12C13.7909 12 12 13.7909 12 16C12 18.2091 13.7909 20 16 20Z"
                        stroke="white"
                        stroke-width="2.66667"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Conclusion -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-black"> Kết luận </label>
              <div
                class="bg-gray-100 border !border-gray-300 rounded-lg px-3 py-2 min-h-[64px]"
              >
                <p class="text-sm text-gray-900">
                  {{ resultData.conclusion }}
                </p>
              </div>
            </div>

            <!-- Info Card -->
            <div class="bg-blue-50 border !border-blue-100 rounded-[10px] p-3">
              <div class="flex gap-3">
                <div class="flex-1 flex flex-col items-center">
                  <p
                    class="text-xs text-[#4a5565] leading-4 tracking-[-0.1504px] mb-1"
                  >
                    Số ảnh
                  </p>
                  <p
                    class="text-base text-[#101828] leading-6 tracking-[-0.3125px] font-semibold"
                  >
                    {{ resultData.imageCount }}
                  </p>
                </div>
                <div class="flex-1 flex flex-col items-center">
                  <p
                    class="text-xs text-[#4a5565] leading-4 tracking-[-0.1504px] mb-1"
                  >
                    Thực hiện bởi
                  </p>
                  <p
                    class="text-sm text-[#101828] leading-5 tracking-[-0.3125px] font-medium text-center"
                  >
                    {{ resultData.performedBy }}
                  </p>
                </div>
                <div class="flex-1 flex flex-col items-center">
                  <p
                    class="text-xs text-[#4a5565] leading-4 tracking-[-0.1504px] mb-1"
                  >
                    Thời gian
                  </p>
                  <p
                    class="text-sm text-[#101828] leading-5 tracking-[-0.3125px] font-medium text-center"
                  >
                    {{ resultData.time }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <div class="flex justify-end pt-4 border-t !border-gray-300">
        <button
          class="bg-white border !border-gray-300 rounded-lg px-4 py-2 h-10 text-sm font-medium text-gray-900 hover:bg-gray-50 transition-colors"
          @click="$emit('close')"
        >
          Đóng
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "KetQuaCanLamSang",
  props: {
    mode: {
      type: String,
      default: "upload", // 'upload' or 'view'
      validator: (value) => ["upload", "view"].includes(value),
    },
    recordId: {
      type: String,
      default: "HSBA-001",
    },
    recordData: {
      type: Object,
      default: null,
    },
    // For view mode
    resultData: {
      type: Object,
      default: () => ({
        petImage:
          "https://www.figma.com/api/mcp/asset/e67eace5-4e3d-4730-901c-05b98d42a814",
        petName: "Milo",
        petSpecies: "Chó Golden Retriever",
        petAge: "2 tuổi",
        petWeight: "28kg",
        ownerName: "Nguyễn Văn A",
        ownerPhone: "0901.234.567",
        images: [
          {
            url: "https://www.figma.com/api/mcp/asset/f01361b0-2d8c-43d7-bb5a-199a6e6fb476",
            name: "Ultrasound 1",
          },
          {
            url: "https://www.figma.com/api/mcp/asset/262a170f-695f-41f4-9fbd-ce1f709b4d6d",
            name: "Ultrasound 2",
          },
        ],
        imageCount: 2,
        performedBy: "BS. Nguyễn B",
        time: "10:30 hôm nay",
        conclusion: "Thai kỳ ổn định, 2 bào thai",
      }),
    },
  },
  data() {
    return {
      icons: {
        perform:
          "https://www.figma.com/api/mcp/asset/80145c6c-5446-4e07-813d-98d709e96a33",
        view: "https://www.figma.com/api/mcp/asset/51592814-cb7a-4234-afe0-24fed7640f93",
        upload:
          "https://www.figma.com/api/mcp/asset/7096fcdc-ce75-4c01-b56f-2fe39c53d1a0",
        uploadBtn:
          "https://www.figma.com/api/mcp/asset/573b343d-a6fa-4f24-bb9c-b2bc40a709a6",
      },
      patientInfo: this.recordData?.patientInfo || {
        image:
          "https://www.figma.com/api/mcp/asset/e67eace5-4e3d-4730-901c-05b98d42a814",
        name: "Milo",
        species: "Chó Golden Retriever",
        age: "2 tuổi",
        weight: "28kg",
        ownerName: "Nguyễn Văn A",
        ownerPhone: "0901.234.567",
      },
      orderInfo: this.recordData?.orderInfo || {
        service: "Siêu âm ổ bụng",
        note: "Kiểm tra dị vật dạ dày. Thú cưng dữ, cần rọ mõm.",
      },
      uploadedFiles: [],
      isDragging: false,
      conclusion: "",
    };
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput.click();
    },
    handleFileSelect(event) {
      const files = Array.from(event.target.files);
      this.processFiles(files);
    },
    handleDrop(event) {
      this.isDragging = false;
      const files = Array.from(event.dataTransfer.files);
      this.processFiles(files);
    },
    processFiles(files) {
      const validFiles = files.filter((file) => {
        const isValidType = [
          "image/jpeg",
          "image/png",
          "application/pdf",
        ].includes(file.type);
        const isValidSize = file.size <= 10 * 1024 * 1024; // 10MB

        if (!isValidType) {
          console.warn(`File ${file.name} không đúng định dạng`);
          return false;
        }
        if (!isValidSize) {
          console.warn(`File ${file.name} vượt quá 10MB`);
          return false;
        }
        return true;
      });

      validFiles.forEach((file) => {
        const fileData = {
          file: file,
          name: file.name,
          preview: null,
        };

        // Create preview for images only
        if (file.type.startsWith("image/")) {
          const reader = new FileReader();
          reader.onload = (e) => {
            fileData.preview = e.target.result;
          };
          reader.readAsDataURL(file);
        }

        this.uploadedFiles.push(fileData);
      });

      // Reset input
      if (this.$refs.fileInput) {
        this.$refs.fileInput.value = "";
      }
    },
    removeFile(index) {
      this.uploadedFiles.splice(index, 1);
    },
    handleSave() {
      if (this.uploadedFiles.length === 0) {
        return;
      }

      // TODO: Upload files to server
      console.log("Uploading files:", this.uploadedFiles);
      console.log("Conclusion:", this.conclusion);

      // Emit save event with file data
      this.$emit("save", {
        files: this.uploadedFiles,
        conclusion: this.conclusion,
        recordId: this.recordId,
      });

      // Close modal
      this.$emit("close");
    },
    previewImage(index) {
      // TODO: Open image in fullscreen preview
      console.log("Preview image:", index, this.resultData.images[index]);

      // Emit preview event
      this.$emit("preview", {
        index: index,
        image: this.resultData.images[index],
      });
    },
  },
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>
