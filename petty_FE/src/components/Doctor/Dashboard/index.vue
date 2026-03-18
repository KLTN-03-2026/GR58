<template>
  <div class="w-full min-h-screen px-8 py-6 flex flex-col gap-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-2">
      <h1 class="text-2xl font-semibold text-black">Dashboard - Bác sĩ</h1>
      <p class="text-gray-500 font-medium text-base">
        Hôm nay bạn bận thế nào? Ai đang đợi bạn?
      </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-4 gap-4 h-[110px]">
      <!-- Card 1: Ca khám hôm nay -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col px-[25px] py-[25px]"
      >
        <div class="flex items-start justify-between w-full h-full">
          <div class="flex flex-col gap-1">
            <p
              class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
            >
              Ca khám hôm nay
            </p>
            <p
              class="font-normal text-[30px] leading-9 text-[#155dfc] tracking-[0.3955px]"
            >
              {{ stats.appointments }}
            </p>
          </div>
          <!-- <div
            class="bg-blue-100 rounded-[10px] w-12 h-12 flex items-center justify-center"
          >
            <img :src="icons.calendar" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>

      <!-- Card 2: Đã hoàn thành -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col px-[25px] py-[25px]"
      >
        <div class="flex items-start justify-between w-full h-full">
          <div class="flex flex-col gap-1">
            <p
              class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
            >
              Đã hoàn thành
            </p>
            <p
              class="font-normal text-[30px] leading-9 text-[#00a63e] tracking-[0.3955px]"
            >
              {{ stats.completed }}
            </p>
          </div>
          <!-- <div
            class="bg-green-100 rounded-[10px] w-12 h-12 flex items-center justify-center"
          >
            <img :src="icons.checkCircle" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>

      <!-- Card 3: Đang chờ -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col px-[25px] py-[25px]"
      >
        <div class="flex items-start justify-between w-full h-full">
          <div class="flex flex-col gap-1">
            <p
              class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
            >
              Đang chờ
            </p>
            <p
              class="font-normal text-[30px] leading-9 text-[#f54900] tracking-[0.3955px]"
            >
              {{ stats.waiting }}
            </p>
          </div>
          <!-- <div
            class="bg-[#ffedd4] rounded-[10px] w-12 h-12 flex items-center justify-center"
          >
            <img :src="icons.clock" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>

      <!-- Card 4: Doanh thu hôm nay -->
      <div
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] flex flex-col px-[25px] py-[25px]"
      >
        <div class="flex items-start justify-between w-full h-full">
          <div class="flex flex-col gap-1">
            <p
              class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
            >
              Doanh thu hôm nay
            </p>
            <p
              class="font-normal text-[30px] leading-9 text-[#009689] tracking-[0.3955px]"
            >
              {{ stats.revenue }}
            </p>
          </div>
          <!-- <div
            class="bg-[#cbfbf1] rounded-[10px] w-12 h-12 flex items-center justify-center"
          >
            <img :src="icons.money" alt="" class="w-6 h-6" />
          </div> -->
        </div>
      </div>
    </div>

    <!-- Waiting Patients Card -->
    <div
      class="bg-white border-2 border-[#bedbff] rounded-[14px] flex flex-col gap-4 py-6 px-4 w-full"
    >
      <!-- Card Header -->
      <div
        class="bg-blue-50 flex items-center justify-between px-4 py-2 rounded w-full"
      >
        <div class="flex items-center gap-2 h-5">
          <!-- <img :src="icons.patients" alt="" class="w-5 h-5" /> -->
          <p
            class="font-normal text-base leading-4 text-neutral-950 tracking-[-0.3125px]"
          >
            Bệnh nhân đang chờ
          </p>
        </div>
      </div>

      <!-- Card Content -->
      <div class="flex flex-col gap-6 w-full">
        <!-- Next Patient Section -->
        <div class="flex flex-col gap-4">
          <div class="flex gap-2 items-center h-5">
            <!-- <img :src="icons.star" alt="" class="w-5 h-5" /> -->
            <p
              class="font-bold text-sm leading-5 text-[#7e2a0c] tracking-[-0.1504px]"
            >
              Bệnh nhân tiếp theo:
            </p>
          </div>

          <!-- Next Patient Card -->
          <div
            v-if="nextPatient"
            class="border-2 !border-[#8ec5ff] rounded-[14px] flex gap-6 px-[26px] py-[26px]"
          >
            <div
              class="bg-transparent border-4 border-white rounded-[14px] shadow-md w-32 h-32 overflow-hidden flex-shrink-0"
            >
              <img
                :src="nextPatient.petImage"
                alt=""
                class="w-full h-full object-cover"
              />
            </div>

            <div class="flex-1 flex flex-col gap-4">
              <div class="flex items-start justify-between">
                <div class="flex flex-col gap-1">
                  <div class="flex items-center gap-2">
                    <h3
                      class="font-normal text-2xl leading-8 text-[#101828] tracking-[0.0703px]"
                    >
                      {{ nextPatient.petName }}
                    </h3>
                    <div
                      class="bg-blue-50 border !border-[#bedbff] rounded-lg px-[9px] py-1 flex items-center gap-1"
                    >
                      <!-- <img :src="icons.calendarBadge" alt="" class="w-3 h-3" /> -->
                      <p class="font-medium text-xs leading-4 text-[#1447e6]">
                        Đặt trước
                      </p>
                    </div>
                    <div
                      class="bg-blue-100 border !border-blue-300 rounded-lg px-[17px] py-[3px]"
                    >
                      <p class="font-medium text-xs leading-4 text-blue-700">
                        Sắp đến
                      </p>
                    </div>
                  </div>
                  <p
                    class="font-normal text-base leading-6 text-[#4a5565] tracking-[-0.3125px]"
                  >
                    {{ nextPatient.petType }}
                  </p>
                  <div class="flex gap-4 items-center">
                    <p
                      class="font-normal text-sm leading-5 text-gray-500 tracking-[-0.1504px]"
                    >
                      Hẹn lúc: {{ nextPatient.appointmentTime }}
                    </p>
                    <div class="flex items-center gap-1">
                      <!-- <img :src="icons.clockCheck" alt="" class="w-3 h-3" /> -->
                      <p class="font-normal text-sm text-gray-500">
                        Check-in: {{ nextPatient.checkInTime }}
                      </p>
                    </div>
                  </div>
                </div>
                <div
                  class="bg-red-50 border !border-red-300 rounded-lg px-2 py-0.5 flex items-center gap-2"
                >
                  <!-- <img :src="icons.clockRed" alt="" class="w-3 h-3" /> -->
                  <p class="font-medium text-xs leading-4 text-red-600">
                    Trễ {{ nextPatient.lateTime }}
                  </p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                  <p
                    class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                  >
                    Chủ nuôi
                  </p>
                  <p
                    class="font-normal text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                  >
                    {{ nextPatient.ownerName }}
                  </p>
                </div>
                <div class="flex flex-col">
                  <p
                    class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                  >
                    Dịch vụ
                  </p>
                  <p
                    class="font-normal text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                  >
                    {{ nextPatient.service }}
                  </p>
                </div>
              </div>

              <div
                class="bg-yellow-50 border !border-[#fff085] rounded-[10px] px-[13px] py-[13px] flex gap-1 items-center"
              >
                <!-- <img :src="icons.info" alt="" class="w-4 h-4" /> -->
                <p
                  class="font-bold text-sm leading-5 text-[#733e0a] tracking-[-0.1504px]"
                >
                  Ghi chú:
                </p>
                <p
                  class="font-normal text-sm leading-5 text-[#733e0a] tracking-[-0.1504px]"
                >
                  {{ nextPatient.note }}
                </p>
              </div>

              <div class="flex gap-3">
                <button
                  class="bg-white border !border-gray-300 rounded-lg px-4 py-2.5 flex items-center gap-3 hover:bg-gray-50"
                  @click="handleSkipPatient"
                >
                  <!-- <img :src="icons.skip" alt="" class="w-4 h-4" /> -->
                  <span
                    class="font-medium text-sm leading-5 text-[#364153] tracking-[-0.1504px]"
                  >
                    Bỏ qua
                  </span>
                </button>
                <button
                  class="bg-[#155dfc] rounded-lg px-4 py-2.5 flex items-center gap-3 hover:bg-blue-700"
                >
                  <!-- <img :src="icons.playCircle" alt="" class="w-4 h-4" /> -->
                  <span
                    class="font-medium text-sm leading-5 text-white tracking-[-0.1504px]"
                  >
                    BẮT ĐẦU KHÁM
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Queue List -->
        <div class="flex flex-col gap-3">
          <p
            class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
          >
            Tiếp theo trong hàng chờ:
          </p>

          <div class="flex flex-col gap-3">
            <div
              v-for="patient in queueList"
              :key="patient.id"
              class="bg-white border !border-gray-300 rounded-[10px] flex gap-4 items-center px-[17px] py-[17px]"
            >
              <div
                class="rounded-[10px] w-16 h-16 overflow-hidden flex-shrink-0"
              >
                <img
                  :src="patient.petImage"
                  alt=""
                  class="w-full h-full object-cover"
                />
              </div>

              <div class="flex-1 flex flex-col gap-1">
                <div class="flex items-center gap-2">
                  <span
                    class="font-bold text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                  >
                    {{ patient.petName }}
                  </span>
                  <span
                    class="font-normal text-base leading-6 text-[#101828] tracking-[-0.3125px]"
                  >
                    - {{ patient.petType }}
                  </span>
                  <div
                    v-if="patient.badge"
                    class="px-2 py-1 rounded-lg border flex items-center gap-2"
                    :class="getBadgeStyle(patient.badge)"
                  >
                    <!-- <img
                        :src="getBadgeIcon(patient.badge)"
                        alt=""
                        class="w-3 h-3"
                      /> -->
                    <p
                      class="font-medium text-xs leading-4"
                      :class="getBadgeTextStyle(patient.badge)"
                    >
                      {{ patient.badge }}
                    </p>
                  </div>
                  <div
                    v-if="patient.status"
                    class="bg-green-100 border !border-[#7bf1a8] rounded-lg px-[9px] py-[3px]"
                  >
                    <p class="font-medium text-xs leading-4 text-[#008236]">
                      {{ patient.status }}
                    </p>
                  </div>
                </div>
                <p
                  class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
                >
                  Chủ: {{ patient.ownerName }} | {{ patient.service }}
                </p>
                <div v-if="patient.checkInTime" class="flex gap-4 items-center">
                  <div
                    v-if="patient.appointmentTime"
                    class="flex items-center gap-1"
                  >
                    <p class="font-normal text-xs text-gray-500">
                      Hẹn lúc: {{ patient.appointmentTime }}
                    </p>
                  </div>
                  <div class="flex items-center gap-1">
                    <!-- <img :src="icons.clockCheck" alt="" class="w-3 h-3" /> -->
                    <p class="font-normal text-xs text-[#9810fa]">
                      Check-in: {{ patient.checkInTime }}
                    </p>
                  </div>
                </div>
              </div>

              <div class="flex flex-col gap-2 items-end">
                <div
                  class="rounded-lg px-2 py-0.5 flex items-center gap-2"
                  :class="patient.waitBadgeStyle"
                >
                  <!-- <img :src="patient.waitIcon" alt="" class="w-3 h-3" /> -->
                  <p
                    class="font-medium text-xs leading-4"
                    :class="patient.waitTextStyle"
                  >
                    {{ patient.waitTime }}
                  </p>
                </div>
                <button
                  v-if="patient.canStartNow"
                  class="bg-[#5a9690] rounded-lg px-2.5 py-2 flex items-center gap-4 hover:bg-[#4a7f79]"
                  @click="handlePriorityExam(patient)"
                >
                  <!-- <img :src="icons.playWhite" alt="" class="w-4 h-4" /> -->
                  <span class="font-medium text-xs text-white">
                    Khám ngay
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Today Card -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] flex flex-col gap-[30px] px-[25px] py-[25px]"
    >
      <!-- Card Title -->
      <div class="flex items-center gap-2">
        <!-- <img :src="icons.calendarToday" alt="" class="w-5 h-5" /> -->
        <p
          class="font-normal text-base leading-4 text-neutral-950 tracking-[-0.3125px]"
        >
          Lịch đặt trước
        </p>
      </div>

      <!-- Schedule List -->
      <div class="flex flex-col gap-2">
        <div
          v-for="schedule in scheduleToday"
          :key="schedule.id"
          class="bg-gray-100 rounded-[10px] flex gap-4 items-center px-3 py-3"
        >
          <div class="w-16 text-center">
            <p
              class="font-normal text-base leading-6 text-[#101828] tracking-[-0.3125px]"
            >
              {{ schedule.time }}
            </p>
          </div>

          <div class="flex-1 flex flex-col">
            <p
              class="font-normal text-base leading-6 text-[#101828] tracking-[-0.3125px]"
            >
              {{ schedule.petName }}
            </p>
            <p
              class="font-normal text-sm leading-5 text-[#4a5565] tracking-[-0.1504px]"
            >
              {{ schedule.ownerName }}
            </p>
          </div>

          <div
            class="bg-blue-100 border !border-blue-300 rounded-lg px-[17px] py-[3px]"
          >
            <p class="font-medium text-xs leading-4 text-blue-700">Sắp đến</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Doi Luot Kham Modal -->
    <DoiLuotKham
      :is-open="isDoiLuotKhamOpen"
      :current-patient="nextPatient.petName"
      :next-patient="nextInQueueName"
      @close="isDoiLuotKhamOpen = false"
      @confirm="handleConfirmSkip"
    />

    <!-- Uu Tien Kham Modal -->
    <UuTienKham
      :is-open="isUuTienKhamOpen"
      :patient-name="selectedPriorityPatient?.petName"
      :current-patient="nextPatient.petName"
      @close="isUuTienKhamOpen = false"
      @confirm="handleConfirmPriority"
    />
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import DoiLuotKham from "./DoiLuotKham/index.vue";
import UuTienKham from "./UuTienKham/index.vue";

// Icon URLs from Figma
const icons = {
  calendar:
    "https://www.figma.com/api/mcp/asset/505b9d05-3ae2-4f55-b6fd-9f358e7582a3",
  checkCircle:
    "https://www.figma.com/api/mcp/asset/58426816-a539-4956-87a9-e9f5515e93a6",
  clock:
    "https://www.figma.com/api/mcp/asset/2eed46a8-9149-4737-8cb0-335ff277f7e1",
  money:
    "https://www.figma.com/api/mcp/asset/db7c1a2d-396b-43ab-8020-a219068efaf0",
  patients:
    "https://www.figma.com/api/mcp/asset/4be5b55b-fbb1-4d3c-b86e-c0cd9877ffff",
  star: "https://www.figma.com/api/mcp/asset/5ac5d493-9ff1-447a-92b6-7e59ac50ac50",
  calendarBadge:
    "https://www.figma.com/api/mcp/asset/2c639aca-6da7-4a32-aaf2-7207f0a1597e",
  clockCheck:
    "https://www.figma.com/api/mcp/asset/e09bf559-b7a0-4c80-9cf6-85d9c2f89cae",
  clockRed:
    "https://www.figma.com/api/mcp/asset/5812d01a-b25e-4039-b434-547d4883db58",
  info: "https://www.figma.com/api/mcp/asset/3cbce1c8-6348-4d4a-b91d-f4f3c7108cb8",
  skip: "https://www.figma.com/api/mcp/asset/5c263c3a-198d-4cd1-9238-6501c761cea7",
  playCircle:
    "https://www.figma.com/api/mcp/asset/311c0828-3733-41b8-b46c-275ad82de2c7",
  userPurple:
    "https://www.figma.com/api/mcp/asset/87bda4bf-cdd2-43a2-93df-38f154a387d7",
  clockOrange:
    "https://www.figma.com/api/mcp/asset/cebf0755-a3dc-4228-8b0c-3918dffc3369",
  clockGray:
    "https://www.figma.com/api/mcp/asset/7eacc4cd-5d92-468a-854e-786fc9fd03a5",
  playWhite:
    "https://www.figma.com/api/mcp/asset/68a32c7f-4178-4015-b2b4-335c97173a0c",
  userBlue:
    "https://www.figma.com/api/mcp/asset/a62f8926-c59a-4fb9-b010-229ff806e7ab",
  clockCyan:
    "https://www.figma.com/api/mcp/asset/5761a5b1-bbf5-4e26-80c1-a7eb6f7d4417",
  calendarToday:
    "https://www.figma.com/api/mcp/asset/c8a03a5c-8a3f-497f-96ae-4e4f22b5fae4",
};

// State for modal
const isDoiLuotKhamOpen = ref(false);
const isUuTienKhamOpen = ref(false);
const selectedPriorityPatient = ref(null);

// Stats data
const stats = ref({
  appointments: 12,
  completed: 5,
  waiting: 3,
  revenue: "8.5M",
});

// Next patient data
const nextPatient = ref({
  petName: "Milo",
  petType: "Chó Golden",
  petImage:
    "https://www.figma.com/api/mcp/asset/11853062-cea6-48f7-bbb7-c5806a6d16e0",
  ownerName: "Nguyễn Văn A",
  service: "Khám tổng quát",
  appointmentTime: "09:00",
  checkInTime: "--:--",
  lateTime: "30 phút",
  note: "Bé hơi dữ, cẩn thận khi tiếp cận",
});

// Queue list data
const queueList = ref([
  {
    id: 1,
    petName: "Lu",
    petType: "Mèo Anh Lông Ngắn",
    petImage:
      "https://www.figma.com/api/mcp/asset/5041ba45-eefc-4b15-a371-9387846090d4",
    ownerName: "Đỗ Thị D",
    service: "Khám tổng quát",
    badge: "Vãng lai",
    status: "Đã đến",
    checkInTime: "09:12",
    waitTime: "Chờ 18 phút",
    waitBadgeStyle: "bg-[#ffedd4] border !border-[#e9d4ff]",
    waitTextStyle: "text-[#ca3500]",
    waitIcon: icons.clockOrange,
    canStartNow: true,
  },
  {
    id: 2,
    petName: "Luna",
    petType: "Mèo Ba Tư",
    petImage:
      "https://www.figma.com/api/mcp/asset/7375820c-bb0e-4aa5-ad91-51671bea802f",
    ownerName: "Trần Thị B",
    service: "Tiêm phòng vắc-xin",
    badge: "Thành Viên",
    status: "Đã đến",
    checkInTime: "09:24",
    waitTime: "6 phút",
    waitBadgeStyle: "bg-gray-100 border !border-gray-300",
    waitTextStyle: "text-[#364153]",
    waitIcon: icons.clockGray,
    canStartNow: true,
  },
  {
    id: 3,
    petName: "Max",
    petType: "Chó Husky",
    petImage:
      "https://www.figma.com/api/mcp/asset/0d89bcb6-abeb-47c3-9ff5-f8f4360b2872",
    ownerName: "Lê Văn C",
    service: "Cắt tỉa lông",
    badge: "Đặt trước",
    status: null,
    appointmentTime: "10:30",
    checkInTime: "--:--",
    waitTime: "Còn 60 phút",
    waitBadgeStyle: "bg-cyan-50 border !border-cyan-200",
    waitTextStyle: "text-cyan-500",
    waitIcon: icons.clockCyan,
    canStartNow: false,
  },
]);

// Schedule today data
const scheduleToday = ref([
  {
    id: 1,
    time: "09:00",
    petName: "Milo",
    ownerName: "Nguyễn Văn A",
  },
  {
    id: 2,
    time: "10:30",
    petName: "Max",
    ownerName: "Lê Văn C",
  },
  {
    id: 3,
    time: "11:00",
    petName: "Bella",
    ownerName: "Hoàng Văn E",
  },
]);

// Computed for next patient in queue
const nextInQueueName = computed(() => {
  return queueList.value.length > 0
    ? queueList.value[0].petName
    : "Khách tiếp theo";
});

// Methods
const handleSkipPatient = () => {
  isDoiLuotKhamOpen.value = true;
};

const handleConfirmSkip = (data) => {
  console.log("Skipping patient:", data);
  // Implement logic to swap patients or move current to end of queue
  isDoiLuotKhamOpen.value = false;
};

const handlePriorityExam = (patient) => {
  selectedPriorityPatient.value = patient;
  isUuTienKhamOpen.value = true;
};

const handleConfirmPriority = (data) => {
  console.log("Priority exam for:", data);
  // Implement logic to move patient to top of queue or start exam
  isUuTienKhamOpen.value = false;
  selectedPriorityPatient.value = null;
};

// Badge styling helper methods
const getBadgeStyle = (badge) => {
  const styles = {
    "Vãng lai": "bg-purple-50 !border-[#e9d4ff]",
    "Thành Viên": "bg-blue-50 !border-blue-300",
    "Đặt trước": "bg-blue-50 !border-[#bedbff]",
  };
  return styles[badge] || "bg-gray-50 !border-gray-300";
};

const getBadgeIcon = (badge) => {
  const badgeIcons = {
    "Vãng lai": icons.userPurple,
    "Thành Viên": icons.userBlue,
    "Đặt trước": icons.calendarBadge,
  };
  return badgeIcons[badge] || "";
};

const getBadgeTextStyle = (badge) => {
  const styles = {
    "Vãng lai": "text-[#8200db]",
    "Thành Viên": "text-blue-700",
    "Đặt trước": "text-[#1447e6]",
  };
  return styles[badge] || "text-gray-700";
};
</script>

<style scoped>
/* Custom scrollbar for schedule list */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
