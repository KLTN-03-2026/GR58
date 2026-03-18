<template>
  <div class="w-full h-full flex flex-col">
    <!-- Dialog Content -->
    <div
      class="w-full h-full bg-white rounded-[14px] shadow-lg flex flex-col max-h-[90vh]"
    >
      <!-- Header - Fixed -->
      <div
        class="w-full flex items-center justify-between px-6 py-4 border-b !border-gray-300"
      >
        <h2 class="text-xl font-semibold text-black">
          Phiếu Khám Lâm Sàng - {{ examinationData.date }}
        </h2>
        <button
          class="bg-white border !border-gray-300 rounded-lg h-9 px-4 flex items-center gap-2 hover:bg-gray-50"
        >
          <!-- <img :src="icons.print" alt="" class="w-4 h-4" /> -->
          <span class="text-sm font-medium text-gray-900">In phiếu</span>
        </button>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-4">
        <div class="flex flex-col gap-6">
          <!-- Basic Info Card -->
          <div
            class="w-full bg-blue-50 border !border-[#bedbff] rounded-lg p-4"
          >
            <div class="grid grid-cols-2 gap-4">
              <!-- Left Column -->
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.calendar" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Ngày khám:</span>
                  <span class="text-base font-bold text-black">{{
                    examinationData.date
                  }}</span>
                  <span class="text-sm text-gray-500"
                    >lúc {{ examinationData.time }}</span
                  >
                </div>
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.doctor" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Bác sĩ:</span>
                  <span class="text-base font-bold text-black">{{
                    examinationData.doctor
                  }}</span>
                </div>
              </div>

              <!-- Right Column -->
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.owner" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Chủ nuôi:</span>
                  <span class="text-base font-bold text-black">{{
                    examinationData.ownerName
                  }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.phone" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">SĐT:</span>
                  <span class="text-base font-bold text-black">{{
                    examinationData.ownerPhone
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="w-full h-px bg-gray-300"></div>

          <!-- Patient Info Section -->
          <div class="w-full flex flex-col gap-3">
            <h3 class="text-lg font-semibold text-black">
              Thông tin Bệnh nhân
            </h3>
            <div class="w-full bg-gray-50 rounded-lg p-4 flex flex-col gap-2">
              <div class="flex items-center gap-2">
                <span class="text-base font-bold text-black"
                  >Tên thú cưng:</span
                >
                <span class="text-base text-gray-900"
                  >{{ examinationData.petName }} ({{
                    examinationData.petSpecies
                  }})</span
                >
              </div>
              <div class="flex items-center gap-2">
                <span class="text-base font-bold text-black">Lý do khám:</span>
                <span class="text-base text-gray-900">{{
                  examinationData.reason
                }}</span>
              </div>
            </div>
          </div>

          <!-- Vital Signs Section -->
          <div class="w-full flex flex-col gap-3">
            <div class="flex items-center gap-2">
              <!-- <img :src="icons.vitals" alt="" class="w-5 h-5" /> -->
              <h3 class="text-base font-semibold text-black">
                Chỉ số Sinh tồn
              </h3>
            </div>
            <div class="grid grid-cols-4 gap-4">
              <!-- Temperature -->
              <div
                class="bg-red-50 border !border-[#ffc9c9] rounded-lg p-3 flex flex-col gap-1"
              >
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.temperature" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Nhiệt độ</span>
                </div>
                <p class="text-xl text-black font-semibold">
                  {{ examinationData.vitals.temperature }}
                </p>
              </div>

              <!-- Heart Rate -->
              <div
                class="bg-pink-50 border !border-[#fccee8] rounded-lg p-3 flex flex-col gap-1"
              >
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.heartRate" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Nhịp tim</span>
                </div>
                <p class="text-xl text-black font-semibold">
                  {{ examinationData.vitals.heartRate }}
                </p>
              </div>

              <!-- Respiratory Rate -->
              <div
                class="bg-blue-50 border !border-[#bedbff] rounded-lg p-3 flex flex-col gap-1"
              >
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.respiratory" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Nhịp thở</span>
                </div>
                <p class="text-xl text-black font-semibold">
                  {{ examinationData.vitals.respiratory }}
                </p>
              </div>

              <!-- Weight -->
              <div
                class="bg-green-50 border !border-[#b9f8cf] rounded-lg p-3 flex flex-col gap-1"
              >
                <div class="flex items-center gap-2">
                  <!-- <img :src="icons.weight" alt="" class="w-4 h-4" /> -->
                  <span class="text-sm text-gray-600">Cân nặng</span>
                </div>
                <p class="text-xl text-black font-semibold">
                  {{ examinationData.vitals.weight }}
                </p>
              </div>
            </div>
          </div>

          <!-- Symptoms Section -->
          <div class="w-full flex flex-col gap-3">
            <div class="flex items-center gap-2">
              <!-- <img :src="icons.symptoms" alt="" class="w-5 h-5" /> -->
              <h3 class="text-base font-semibold text-black">Triệu chứng</h3>
            </div>
            <div
              class="w-full bg-orange-50 border !border-[#ffd6a7] rounded-lg p-4"
            >
              <p class="text-base text-gray-900">
                {{ examinationData.symptoms }}
              </p>
            </div>
          </div>

          <!-- Diagnosis Section -->
          <div class="w-full flex flex-col gap-3">
            <div class="flex items-center gap-2">
              <!-- <img :src="icons.diagnosis" alt="" class="w-5 h-5" /> -->
              <h3 class="text-base font-semibold text-black">Chẩn đoán</h3>
            </div>
            <div
              class="w-full bg-blue-50 border !border-[#bedbff] rounded-lg p-4"
            >
              <p class="text-base text-gray-900">
                {{ examinationData.diagnosis }}
              </p>
            </div>
          </div>

          <!-- Prescription Section -->
          <div class="w-full flex flex-col gap-3">
            <div class="flex items-center gap-2">
              <!-- <img :src="icons.prescription" alt="" class="w-5 h-5" /> -->
              <h3 class="text-base font-semibold text-black">Đơn thuốc</h3>
            </div>
            <div
              class="w-full border !border-gray-300 rounded-lg overflow-hidden"
            >
              <table class="w-full">
                <thead class="bg-gray-100">
                  <tr class="h-11">
                    <th class="text-left px-4 text-sm font-bold text-gray-700">
                      STT
                    </th>
                    <th class="text-left px-4 text-sm font-bold text-gray-700">
                      Tên thuốc
                    </th>
                    <th class="text-left px-4 text-sm font-bold text-gray-700">
                      Liều lượng
                    </th>
                    <th class="text-left px-4 text-sm font-bold text-gray-700">
                      Tần suất
                    </th>
                    <th class="text-left px-4 text-sm font-bold text-gray-700">
                      Thời gian
                    </th>
                    <th class="text-left px-4 text-sm font-bold text-gray-700">
                      Ghi chú
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(medicine, index) in examinationData.prescription"
                    :key="index"
                    class="border-t !border-gray-300 h-11"
                  >
                    <td class="px-4 text-sm text-gray-900">{{ index + 1 }}</td>
                    <td class="px-4 text-sm text-gray-900">
                      {{ medicine.name }}
                    </td>
                    <td class="px-4 text-sm text-gray-900">
                      {{ medicine.dosage }}
                    </td>
                    <td class="px-4 text-sm text-gray-900">
                      {{ medicine.frequency }}
                    </td>
                    <td class="px-4 text-sm text-gray-900">
                      {{ medicine.duration }}
                    </td>
                    <td class="px-4 text-sm text-gray-600">
                      {{ medicine.note }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Notes Section -->
          <div class="w-full flex flex-col gap-3">
            <h3 class="text-lg font-semibold text-black">Ghi chú</h3>
            <div
              class="w-full bg-yellow-50 border !border-[#fff085] rounded-lg p-4"
            >
              <p class="text-base text-gray-900">
                {{ examinationData.notes }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer - Fixed -->
      <div class="w-full border-t !border-gray-300 px-6 py-4 flex justify-end">
        <button
          @click="closeModal"
          class="bg-white border !border-gray-300 rounded-lg h-9 px-4 hover:bg-gray-50"
        >
          <span class="text-sm font-medium text-gray-900">Đóng</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

// Icons
const icons = {
  print:
    "https://www.figma.com/api/mcp/asset/5d5a2786-34bd-4573-a400-99048b08691d",
  calendar:
    "https://www.figma.com/api/mcp/asset/56afdeed-0e62-42e4-863b-8f09692271c4",
  doctor:
    "https://www.figma.com/api/mcp/asset/22f3d5c3-3c6b-4d7e-80a9-4c59ccdeca31",
  owner:
    "https://www.figma.com/api/mcp/asset/94b608b7-8293-4227-a095-54e9f85cb5e7",
  phone:
    "https://www.figma.com/api/mcp/asset/6a6008ef-b424-4d60-bea1-89cd5c71a4e3",
  vitals:
    "https://www.figma.com/api/mcp/asset/f433ad1c-0629-4d68-abc0-d7327b6d7331",
  temperature:
    "https://www.figma.com/api/mcp/asset/1b16bbaa-31b4-442b-b016-20811f39ac53",
  heartRate:
    "https://www.figma.com/api/mcp/asset/98bd780f-82f2-4437-8146-0daa3c7e079a",
  respiratory:
    "https://www.figma.com/api/mcp/asset/a7710ae5-e77f-44b4-bbb6-b6db129c61e9",
  weight:
    "https://www.figma.com/api/mcp/asset/01b87b21-f20b-4786-9f7e-86030fb9ba93",
  symptoms:
    "https://www.figma.com/api/mcp/asset/65935341-9258-4c79-8082-36ddd67bee35",
  diagnosis:
    "https://www.figma.com/api/mcp/asset/03475182-f0a6-4df6-8a20-4ddd41e2ae70",
  prescription:
    "https://www.figma.com/api/mcp/asset/3936b78c-2b8f-43be-8bc9-f02a5c77c3b9",
};

// Sample examination data
const examinationData = ref({
  date: "01/12/2024",
  time: "14:30",
  doctor: "BS. Trần Văn B",
  ownerName: "Nguyễn Văn A",
  ownerPhone: "0901234567",
  petName: "Milo",
  petSpecies: "Chó Golden Retriever",
  reason: "Ngứa ngáy, rụng lông vùng bụng",
  vitals: {
    temperature: "38.5°C",
    heartRate: "110 lần/phút",
    respiratory: "25 lần/phút",
    weight: "28 kg",
  },
  symptoms:
    "Thú cưng liên tục ngứa vùng bụng và chân, rụng lông nhiều. Da có vết đỏ và vảy. Chủ nhân cho biết triệu chứng xuất hiện sau khi đổi thức ăn mới.",
  diagnosis:
    "Viêm da dị ứng (Allergic Dermatitis) do phản ứng với thành phần trong thức ăn mới. Vùng da bị nhiễm trùng thứ phát do gãi nhiều.",
  prescription: [
    {
      name: "Ketoconazole Cream 2%",
      dosage: "Bôi mỏng",
      frequency: "2 lần/ngày",
      duration: "7 ngày",
      note: "Bôi vào vùng da bị viêm",
    },
    {
      name: "Amoxicillin 500mg",
      dosage: "1 viên",
      frequency: "2 lần/ngày",
      duration: "7 ngày",
      note: "Uống sau ăn",
    },
    {
      name: "Cetirizine 10mg",
      dosage: "1/2 viên",
      frequency: "1 lần/ngày",
      duration: "10 ngày",
      note: "Giảm ngứa",
    },
  ],
  notes:
    "Khuyên chủ nhân ngừng thức ăn mới, quay về thức ăn cũ. Tái khám sau 7 ngày để đánh giá tiến triển. Nếu không cải thiện, cần làm xét nghiệm dị ứng.",
});

const emit = defineEmits(["close"]);

// Methods
const closeModal = () => {
  emit("close");
};
</script>
