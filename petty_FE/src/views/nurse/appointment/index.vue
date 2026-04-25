<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Page Header -->
    <div class="flex flex-col gap-1 mb-6">
      <h1 class="text-2xl font-semibold text-black">Quản lý Lịch hẹn</h1>
      <p class="text-base font-medium text-gray-500">
        Đặt lịch, check-in và theo dõi lịch khám
      </p>
    </div>

    <!-- Search and Create Button Card -->
    <div
      class="bg-white border !border-gray-300 rounded-[14px] p-4 shadow-sm mb-6"
    >
      <div class="flex items-center justify-between gap-4">
        <div class="relative flex-1 max-w-[572px]">
          <!-- <img
            :src="iconSearch"
            alt="Search"
            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4"
          /> -->
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Tìm theo SĐT, tên khách hàng hoặc tên thú cưng..."
            class="w-full h-10 bg-gray-50 border !border-gray-300 rounded-lg pl-4 pr-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#009689]"
          />
        </div>
        <button
          class="bg-[#009689] rounded-lg px-4 py-2.5 h-10 flex items-center gap-2 hover:bg-[#007d72] transition-colors"
          @click="createAppointment"
        >
          <!-- <img :src="iconPlus" alt="Create" class="w-4 h-4" /> -->
          <span class="text-sm font-medium text-white"> Tạo lịch khám </span>
        </button>
      </div>
    </div>

    <!-- Tabs Card -->
    <div class="bg-white border !border-gray-300 rounded-[14px] shadow-sm mb-6">
      <div class="border-b !border-gray-300 flex items-center">
        <button
          :class="[
            'flex-1 h-14 flex items-center justify-center border-b-2 transition-colors',
            activeTab === 'list'
              ? 'bg-blue-50 border-[#1447e6]'
              : 'border-transparent hover:bg-gray-50',
          ]"
          @click="activeTab = 'list'"
        >
          <span
            :class="[
              'text-sm font-medium',
              activeTab === 'list' ? 'text-[#1447e6]' : 'text-gray-600',
            ]"
          >
            Danh sách lịch hẹn
          </span>
        </button>
        <button
          :class="[
            'flex-1 h-14 flex items-center justify-center gap-2 border-b-2 transition-colors',
            activeTab === 'check-in'
              ? 'bg-blue-50 border-[#1447e6]'
              : 'border-transparent hover:bg-gray-50',
          ]"
          @click="activeTab = 'check-in'"
        >
          <span
            :class="[
              'text-sm font-medium',
              activeTab === 'check-in' ? 'text-[#1447e6]' : 'text-gray-600',
            ]"
          >
            Chờ Check-in
          </span>
          <span
            v-if="waitingCheckInCount > 0"
            class="bg-[#e7000b] rounded-lg px-2 py-1 min-w-[31px] text-center"
          >
            <span class="text-xs font-medium text-white">
              {{ waitingCheckInCount }}
            </span>
          </span>
        </button>
        <button
          :class="[
            'flex-1 h-14 flex items-center justify-center gap-2 border-b-2 transition-colors',
            activeTab === 'checked-in'
              ? 'bg-blue-50 border-[#1447e6]'
              : 'border-transparent hover:bg-gray-50',
          ]"
          @click="activeTab = 'checked-in'"
        >
          <span
            :class="[
              'text-sm font-medium',
              activeTab === 'checked-in' ? 'text-[#1447e6]' : 'text-gray-600',
            ]"
          >
            Đã Check-in
          </span>
        </button>
        <button
          :class="[
            'flex-1 h-14 flex items-center justify-center gap-2 border-b-2 transition-colors',
            activeTab === 'reminders'
              ? 'bg-blue-50 border-[#1447e6]'
              : 'border-transparent hover:bg-gray-50',
          ]"
          @click="activeTab = 'reminders'"
        >
          <span
            :class="[
              'text-sm font-medium',
              activeTab === 'reminders' ? 'text-[#1447e6]' : 'text-gray-600',
            ]"
          >
            Lịch Nhắc Hẹn
          </span>
          <span
            class="bg-[#e7000b] rounded-lg px-2 py-1 min-w-[31px] text-center"
          >
            <span class="text-xs font-medium text-white"> 15 </span>
          </span>
        </button>
      </div>
    </div>

    <!-- Appointments Table Card (Danh sách lịch hẹn) -->
    <div
      v-if="activeTab === 'list'"
      class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm"
    >
      <!-- Card Title -->
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-black">Danh sách lịch hẹn</h2>
        <button
          @click="fetchAllAppointments"
          class="bg-[#009689] hover:bg-[#007d72] rounded-lg px-4 py-2 h-9 flex items-center gap-2 transition-colors"
        >
          <svg
            class="w-4 h-4 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          <span class="text-sm font-medium text-white">Làm mới</span>
        </button>
      </div>

      <!-- Filters and Date Selector -->
      <div class="flex flex-col gap-4 border-b !border-gray-300 pb-4 mb-6">
        <div class="flex items-center justify-between">
          <!-- Filter Pills -->
          <div class="flex items-center gap-2">
            <button
              v-for="filter in filters"
              :key="filter.key"
              :class="[
                'h-10 rounded-full px-4 flex items-center gap-2 transition-colors',
                activeFilter === filter.key
                  ? 'bg-[#009689] shadow-sm'
                  : 'bg-gray-100 hover:bg-gray-200',
              ]"
              @click="activeFilter = filter.key"
            >
              <!-- <img
                v-if="filter.icon"
                :src="filter.icon"
                alt=""
                class="w-4 h-4"
              /> -->
              <span
                :class="[
                  'text-sm font-medium',
                  activeFilter === filter.key ? 'text-white' : 'text-gray-700',
                ]"
              >
                {{ filter.label }}
              </span>
              <span
                :class="[
                  'rounded-lg px-2 py-0.5 min-w-[22px] text-center',
                  activeFilter === filter.key ? 'bg-[#00786f]' : 'bg-gray-200',
                ]"
              >
                <span
                  :class="[
                    'text-xs font-medium',
                    activeFilter === filter.key
                      ? 'text-white'
                      : 'text-gray-700',
                  ]"
                >
                  {{ filter.count }}
                </span>
              </span>
            </button>
          </div>

          <!-- Divider -->
          <div class="bg-gray-300 w-px h-8" />

          <!-- Date Selector -->
          <button
            class="bg-[#4f39f6] rounded-full shadow-sm h-9 px-4 flex items-center gap-2 hover:bg-[#3d2ac4] transition-colors"
            @click="selectDate"
          >
            <!-- <img :src="iconCalendar" alt="Calendar" class="w-4 h-4" /> -->
            <span class="text-sm font-medium text-white"> Hôm nay </span>
            <!-- <img :src="iconChevronDown" alt="Expand" class="w-4 h-4" /> -->
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div
        v-if="loadingAppointments"
        class="py-12 flex flex-col items-center justify-center gap-4"
      >
        <svg
          class="animate-spin w-12 h-12 text-[#009689]"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <p class="text-sm text-gray-500">Đang tải danh sách...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="filteredAppointments.length === 0"
        class="py-12 flex flex-col items-center justify-center gap-4"
      >
        <svg
          class="w-20 h-20 text-gray-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          />
        </svg>
        <p class="text-lg text-gray-500">Không có lịch hẹn nào</p>
      </div>

      <!-- Table -->
      <div v-else class="overflow-x-auto">
        <table class="w-full">
          <!-- Table Header -->
          <thead>
            <tr class="border-b !border-gray-300">
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Thời gian & Nguồn
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Bệnh nhân
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Dịch vụ
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Phân công
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Trạng thái
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Thao tác
              </th>
            </tr>
          </thead>

          <!-- Table Body -->
          <tbody>
            <tr
              v-for="(appointment, index) in filteredAppointments"
              :key="index"
              :class="['border-b !border-gray-300', appointment.rowColor || '']"
            >
              <!-- Time & Source -->
              <td class="px-3 py-2">
                <div class="flex flex-col gap-1.5">
                  <div class="flex flex-col gap-0.5">
                    <div
                      v-if="appointment.appointmentTime"
                      class="flex items-center gap-0.5"
                    >
                      <span
                        :class="[
                          'text-sm font-bold',
                          appointment.checkInTime
                            ? 'text-purple-700'
                            : 'text-black',
                        ]"
                      >
                        {{ appointment.appointmentTime }}
                      </span>
                    </div>
                    <div class="flex items-center gap-0.5">
                      <!-- <img :src="iconClock" alt="Check-in" class="w-3 h-3" /> -->
                      <span class="text-xs text-gray-500">
                        {{ appointment.checkInTime || "--:--" }}
                      </span>
                    </div>
                  </div>
                  <span
                    v-if="appointment.source"
                    :class="[
                      'border rounded-lg px-2 py-1 flex items-center gap-1 w-fit',
                      appointment.source === 'scheduled'
                        ? 'bg-blue-50 !border-[#bedbff]'
                        : appointment.source === 'walkin'
                        ? 'bg-purple-50 !border-[#e9d4ff]'
                        : 'bg-blue-50 !border-blue-300',
                    ]"
                  >
                    <!-- <img
                      :src="
                        appointment.source === 'scheduled'
                          ? iconCalendarSmall
                          : appointment.source === 'walkin'
                          ? iconWalkIn
                          : iconMember
                      "
                      alt=""
                      class="w-3 h-3"
                    /> -->
                    <span
                      :class="[
                        'text-xs font-medium',
                        appointment.source === 'scheduled'
                          ? 'text-[#1447e6]'
                          : appointment.source === 'walkin'
                          ? 'text-[#8200db]'
                          : 'text-blue-700',
                      ]"
                    >
                      {{
                        appointment.source === "scheduled"
                          ? "Đặt trước"
                          : appointment.source === "walkin"
                          ? "Vãng lai"
                          : "Thành Viên"
                      }}
                    </span>
                  </span>
                </div>
              </td>

              <!-- Patient -->
              <td class="px-2 py-2.5">
                <div class="flex flex-col gap-0.5">
                  <div class="flex items-center gap-1">
                    <span class="text-sm font-bold text-black">
                      {{ appointment.petName }}
                    </span>
                    <!-- <img
                      v-if="appointment.hasMale"
                      :src="iconMale"
                      alt="Male"
                      class="w-3.5 h-3.5"
                    /> -->
                  </div>
                  <div class="flex items-center gap-1">
                    <!-- <img :src="iconUser" alt="Owner" class="w-3 h-3" /> -->
                    <span class="text-sm text-gray-600">
                      {{ appointment.ownerName }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Service -->
              <td class="px-2 py-5">
                <span class="text-base text-black">
                  {{ appointment.service }}
                </span>
              </td>

              <!-- Assignment -->
              <td class="px-2 py-3">
                <div class="flex flex-col gap-0.5">
                  <div class="flex items-center gap-1">
                    <!-- <img :src="iconDoctor" alt="doctor" class="w-3 h-3" /> -->
                    <span class="text-sm text-black">
                      {{ appointment.doctor }}
                    </span>
                  </div>
                  <div class="flex items-center gap-1">
                    <!-- <img :src="iconRoom" alt="Room" class="w-3 h-3" /> -->
                    <span class="text-xs text-gray-600">
                      {{ appointment.room }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Status -->
              <td class="px-2 py-5">
                <span
                  :class="[
                    'border rounded-lg px-4 py-1 inline-block',
                    appointment.status === 'upcoming'
                      ? 'bg-blue-100 !border-blue-300 text-blue-700'
                      : appointment.status === 'arrived'
                      ? 'bg-green-100 !border-[#7bf1a8] text-[#008236]'
                      : appointment.status === 'examining'
                      ? 'bg-orange-100 !border-orange-400 text-orange-600'
                      : 'bg-green-100 border-transparent text-[#008236]',
                  ]"
                >
                  <span class="text-xs font-medium">
                    {{
                      appointment.status === "upcoming"
                        ? "Sắp đến"
                        : appointment.status === "arrived"
                        ? "Đã đến"
                        : appointment.status === "examining"
                        ? "Đang khám"
                        : "Chờ thanh toán"
                    }}
                  </span>
                </span>
              </td>

              <!-- Action -->
              <td class="px-2 py-4">
                <button
                  v-if="appointment.action === 'checkin'"
                  :class="[
                    'rounded-lg h-10 w-full flex items-center justify-center gap-2 transition-colors',
                    appointment.status === 'upcoming'
                      ? 'bg-blue-600 hover:bg-blue-700'
                      : 'bg-gray-400 cursor-not-allowed',
                  ]"
                  :disabled="appointment.status !== 'upcoming'"
                  @click="checkIn(appointment)"
                >
                  <!-- <img :src="iconCheckIn" alt="Check-in" class="w-4 h-4" /> -->
                  <span class="text-sm font-medium text-white"> Check-in </span>
                </button>
                <button
                  v-else-if="appointment.action === 'payment'"
                  :class="[
                    'rounded-lg h-10 w-full flex items-center justify-center gap-2 transition-colors',
                    appointment.status === 'payment'
                      ? 'bg-green-600 hover:bg-green-700'
                      : 'bg-gray-400 cursor-not-allowed',
                  ]"
                  :disabled="appointment.status !== 'payment'"
                  @click="collectPayment(appointment)"
                >
                  <!-- <img :src="iconMoney" alt="Payment" class="w-4 h-4" /> -->
                  <span class="text-sm font-medium text-white"> Thu tiền </span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Chờ Check-in Tab -->
    <div
      v-else-if="activeTab === 'check-in'"
      class="bg-white border border-black/10 rounded-[14px] px-px py-6 flex flex-col gap-[30px]"
    >
      <!-- Card Title -->
      <div class="px-6 flex items-center justify-between">
        <div>
          <h2
            class="font-nunito text-lg leading-4 text-[#101828] tracking-[-0.3125px]"
          >
            Lịch hẹn chờ Check-in
          </h2>
          <p class="font-nunito text-sm text-gray-500 mt-1">
            Danh sách lịch hẹn đã xác nhận, chờ khách hàng check-in
          </p>
        </div>
        <button
          @click="refreshCheckInList"
          class="bg-[#009689] hover:bg-[#007d72] rounded-lg px-4 py-2 flex items-center gap-2 transition-colors"
        >
          <svg
            class="w-4 h-4 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          <span class="font-nunito font-medium text-sm text-white"
            >Làm mới</span
          >
        </button>
      </div>

      <!-- Loading State -->
      <div
        v-if="loadingCheckIn"
        class="px-6 py-12 flex flex-col items-center justify-center gap-4"
      >
        <svg
          class="animate-spin w-12 h-12 text-[#009689]"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <p class="font-nunito text-gray-500">Đang tải danh sách...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="waitingCheckInAppointments.length === 0"
        class="px-6 py-12 flex flex-col items-center justify-center gap-4"
      >
        <svg
          class="w-20 h-20 text-gray-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
          />
        </svg>
        <p class="font-nunito text-lg text-gray-500">
          Không có lịch hẹn chờ check-in
        </p>
      </div>

      <!-- Appointments Table -->
      <div v-else class="px-6 overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                STT
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Thời gian hẹn
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Khách hàng
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Thú cưng
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Dịch vụ
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Bác sĩ phụ trách
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Trạng thái
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Thao tác
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(appointment, index) in waitingCheckInAppointments"
              :key="appointment.id"
              class="border-b border-black/10 hover:bg-gray-50"
            >
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">{{
                  index + 1
                }}</span>
              </td>
              <td class="px-2 py-3">
                <div class="flex items-center gap-2">
                  <svg
                    class="w-4 h-4 text-[#009689]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  <span
                    class="font-nunito font-semibold text-sm text-[#101828]"
                  >
                    {{ formatDateTime(appointment.ngay_gio) }}
                  </span>
                </div>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.khach_hang?.full_name || "N/A" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.thu_cung?.ten_thu_cung || "N/A" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.dich_vu?.ten_dich_vu || "N/A" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <div
                  v-if="appointment.nhan_vien"
                  class="flex items-center gap-2"
                >
                  <div
                    class="w-8 h-8 rounded-full bg-[#009689] flex items-center justify-center"
                  >
                    <span class="font-nunito font-semibold text-xs text-white">
                      {{ appointment.nhan_vien.full_name?.charAt(0) || "N" }}
                    </span>
                  </div>
                  <div class="flex flex-col">
                    <span class="font-nunito font-medium text-sm text-gray-900">
                      {{ appointment.nhan_vien.full_name }}
                    </span>
                    <span class="font-nunito text-xs text-gray-500">
                      {{ appointment.nhan_vien.vai_tro || "Bác sĩ" }}
                    </span>
                  </div>
                </div>
                <span v-else class="font-nunito text-sm text-gray-400 italic">
                  Chưa phân công
                </span>
              </td>
              <td class="px-2 py-3">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                    appointment.trang_thai === 'confirmed'
                      ? 'bg-green-100 text-green-800'
                      : appointment.trang_thai === 'pending'
                      ? 'bg-yellow-100 text-yellow-800'
                      : 'bg-gray-100 text-gray-800',
                  ]"
                >
                  {{
                    appointment.trang_thai === "confirmed"
                      ? "Đã xác nhận"
                      : appointment.trang_thai === "pending"
                      ? "Chờ xác nhận"
                      : appointment.trang_thai
                  }}
                </span>
              </td>
              <td class="px-2 py-3">
                <button
                  @click="openCheckInModal(appointment)"
                  class="bg-[#009689] hover:bg-[#007d72] rounded-lg px-4 py-2 flex items-center gap-2 transition-colors"
                >
                  <svg
                    class="w-4 h-4 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                  <span class="font-nunito font-medium text-sm text-white"
                    >Check-in</span
                  >
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Đã Check-in Tab -->
    <div
      v-else-if="activeTab === 'checked-in'"
      class="bg-white border border-black/10 rounded-[14px] px-px py-6 flex flex-col gap-[30px]"
    >
      <!-- Card Title -->
      <div class="px-6 flex items-center justify-between">
        <div>
          <h2
            class="font-nunito text-lg leading-4 text-[#101828] tracking-[-0.3125px]"
          >
            Lịch hẹn đã Check-in
          </h2>
          <p class="font-nunito text-sm text-gray-500 mt-1">
            Danh sách lịch hẹn đã check-in hôm nay
          </p>
        </div>
        <button
          @click="refreshCheckedInList"
          class="bg-[#009689] hover:bg-[#007d72] rounded-lg px-4 py-2 flex items-center gap-2 transition-colors"
        >
          <svg
            class="w-4 h-4 text-white"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
            />
          </svg>
          <span class="font-nunito font-medium text-sm text-white"
            >Làm mới</span
          >
        </button>
      </div>

      <!-- Loading State -->
      <div
        v-if="loadingCheckedIn"
        class="px-6 py-12 flex flex-col items-center justify-center gap-4"
      >
        <svg
          class="animate-spin w-12 h-12 text-[#009689]"
          fill="none"
          viewBox="0 0 24 24"
        >
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
        <p class="font-nunito text-gray-500">Đang tải danh sách...</p>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="checkedInAppointments.length === 0"
        class="px-6 py-12 flex flex-col items-center justify-center gap-4"
      >
        <svg
          class="w-20 h-20 text-gray-300"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
        <p class="font-nunito text-lg text-gray-500">
          Chưa có lịch hẹn nào check-in hôm nay
        </p>
      </div>

      <!-- Appointments Table -->
      <div v-else class="px-6 overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-black/10">
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                STT
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Thời gian check-in
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Khách hàng
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Thú cưng
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Dịch vụ
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Bác sĩ phụ trách
              </th>
              <th
                class="text-left px-2 py-2.5 font-nunito font-medium text-sm text-[#101828]"
              >
                Trạng thái
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(appointment, index) in checkedInAppointments"
              :key="appointment.id"
              class="border-b border-black/10 hover:bg-gray-50"
            >
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">{{
                  index + 1
                }}</span>
              </td>
              <td class="px-2 py-3">
                <div class="flex flex-col gap-0.5">
                  <div class="flex items-center gap-2">
                    <svg
                      class="w-4 h-4 text-purple-600"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M5 13l4 4L19 7"
                      />
                    </svg>
                    <span
                      class="font-nunito font-semibold text-sm text-purple-700"
                    >
                      {{ formatDateTime(appointment.thoi_gian_checkin) }}
                    </span>
                  </div>
                  <span class="font-nunito text-xs text-gray-500 pl-6">
                    Hẹn: {{ formatDateTime(appointment.ngay_gio) }}
                  </span>
                </div>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.khach_hang?.full_name || "N/A" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.thu_cung?.ten_thu_cung || "N/A" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.dich_vu?.ten_dich_vu || "N/A" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <span class="font-nunito text-sm text-gray-700">
                  {{ appointment.nhan_vien?.full_name || "Chưa phân công" }}
                </span>
              </td>
              <td class="px-2 py-3">
                <span
                  :class="[
                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                    appointment.trang_thai === 'in-progress'
                      ? 'bg-blue-100 text-blue-800'
                      : appointment.trang_thai === 'completed'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-800',
                  ]"
                >
                  {{
                    appointment.trang_thai === "in-progress"
                      ? "Đang khám"
                      : appointment.trang_thai === "completed"
                      ? "Hoàn thành"
                      : appointment.trang_thai
                  }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Reminders Table Card (Lịch Nhắc Hẹn) -->
    <div
      v-else-if="activeTab === 'reminders'"
      class="bg-white border !border-gray-300 rounded-[14px] p-6 shadow-sm"
    >
      <!-- Card Title -->
      <div class="mb-6">
        <h2 class="text-lg font-semibold text-black">Lịch Nhắc Hẹn</h2>
      </div>

      <!-- Reminder Filters and Time Selector -->
      <div class="flex flex-col gap-4 border-b !border-gray-300 pb-4 mb-6">
        <div class="flex items-center gap-2">
          <!-- Type Filter Pills -->
          <button
            v-for="reminderType in reminderTypes"
            :key="reminderType.key"
            :class="[
              'h-10 rounded-full px-4 flex items-center gap-2 transition-colors',
              activeReminderType === reminderType.key
                ? 'bg-[#009689] shadow-sm'
                : 'bg-gray-100 hover:bg-gray-200',
            ]"
            @click="activeReminderType = reminderType.key"
          >
            <!-- <img
              v-if="reminderType.icon"
              :src="reminderType.icon"
              alt=""
              class="w-4 h-4"
            /> -->
            <span
              :class="[
                'text-sm font-medium',
                activeReminderType === reminderType.key
                  ? 'text-white'
                  : 'text-gray-700',
              ]"
            >
              {{ reminderType.label }}
            </span>
            <span
              :class="[
                'rounded-lg px-2 py-0.5 min-w-[22px] text-center',
                activeReminderType === reminderType.key
                  ? 'bg-[#00786f]'
                  : 'bg-gray-200',
              ]"
            >
              <span
                :class="[
                  'text-xs font-medium',
                  activeReminderType === reminderType.key
                    ? 'text-white'
                    : 'text-gray-700',
                ]"
              >
                {{ reminderType.count }}
              </span>
            </span>
          </button>
        </div>

        <!-- Time Filter -->
        <div class="flex items-center gap-2">
          <span class="text-sm text-gray-600"> Thời gian: </span>
          <button
            v-for="timeFilter in timeFilters"
            :key="timeFilter.key"
            :class="[
              'h-9 rounded-[10px] px-4 flex items-center gap-2 transition-colors',
              activeTimeFilter === timeFilter.key
                ? timeFilter.key === 'overdue'
                  ? 'bg-[#e7000b]'
                  : 'bg-[#4f39f6]'
                : 'bg-gray-100 hover:bg-gray-200',
            ]"
            @click="activeTimeFilter = timeFilter.key"
          >
            <!-- <img :src="timeFilter.icon" alt="" class="w-4 h-4" /> -->
            <span
              :class="[
                'text-sm',
                activeTimeFilter === timeFilter.key
                  ? 'text-white'
                  : 'text-gray-700',
              ]"
            >
              {{ timeFilter.label }}
            </span>
          </button>
        </div>
      </div>

      <!-- Reminders Table -->
      <div class="overflow-x-auto">
        <table class="w-full">
          <!-- Table Header -->
          <thead>
            <tr class="border-b !border-gray-300">
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Khách hàng
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Loại nhắc
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Nội dung
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Hạn xử lý
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Trạng thái
              </th>
              <th class="text-left px-2 py-2.5 text-sm font-medium text-black">
                Thao tác
              </th>
            </tr>
          </thead>

          <!-- Table Body -->
          <tbody>
            <tr
              v-for="(reminder, index) in filteredReminders"
              :key="index"
              :class="[
                'border-b !border-gray-300',
                reminder.isOverdue ? 'bg-red-50' : '',
              ]"
            >
              <!-- Customer -->
              <td class="px-2 py-2">
                <div class="flex flex-col gap-0.5">
                  <div class="flex items-start gap-0.5">
                    <span class="text-sm font-bold text-black">
                      {{ reminder.petName }}
                    </span>
                    <span class="text-sm text-black">
                      ({{ reminder.breed }})
                    </span>
                  </div>
                  <div class="flex items-center gap-1">
                    <!-- <img :src="iconUser" alt="Owner" class="w-3 h-3" /> -->
                    <span class="text-sm text-gray-600">
                      {{ reminder.ownerName }} - {{ reminder.phone }}
                    </span>
                  </div>
                </div>
              </td>

              <!-- Type -->
              <td class="px-2 py-5">
                <span
                  :class="[
                    'border rounded-lg px-2 py-1 flex items-center gap-1 w-fit',
                    reminder.type === 'recheck'
                      ? 'bg-blue-100 !border-transparent'
                      : reminder.type === 'vaccination'
                      ? 'bg-purple-100 !border-transparent'
                      : 'bg-green-100 !border-transparent',
                  ]"
                >
                  <!-- <img
                    :src="
                      reminder.type === 'recheck'
                        ? iconReminderRecheck
                        : reminder.type === 'vaccination'
                        ? iconReminderVaccine
                        : iconReminderDeworming
                    "
                    alt=""
                    class="w-3 h-3"
                  /> -->
                  <span
                    :class="[
                      'text-xs font-medium',
                      reminder.type === 'recheck'
                        ? 'text-[#1447e6]'
                        : reminder.type === 'vaccination'
                        ? 'text-[#8200db]'
                        : 'text-[#008236]',
                    ]"
                  >
                    {{
                      reminder.type === "recheck"
                        ? "Tái Khám"
                        : reminder.type === "vaccination"
                        ? "Tiêm Chủng"
                        : "Tẩy Giun"
                    }}
                  </span>
                </span>
              </td>

              <!-- Content -->
              <td class="px-2 py-5">
                <span class="text-base text-black">
                  {{ reminder.content }}
                </span>
              </td>

              <!-- Deadline -->
              <td class="px-2 py-5">
                <div class="flex items-center gap-1">
                  <!-- <img
                    v-if="reminder.isOverdue"
                    :src="iconAlert"
                    alt="Alert"
                    class="w-4 h-4"
                  /> -->
                  <span
                    :class="[
                      'text-sm',
                      reminder.isOverdue ? 'text-[#e7000b]' : 'text-black',
                    ]"
                  >
                    {{ reminder.deadline }}
                  </span>
                </div>
              </td>

              <!-- Status -->
              <td class="px-2 py-5">
                <span
                  :class="[
                    'border rounded-lg px-2 py-1 inline-flex items-center gap-1',
                    reminder.status === 'not-called'
                      ? '!border-[#d1d5dc]'
                      : reminder.status === 'called'
                      ? '!border-[#ffdf20]'
                      : reminder.status === 'agreed'
                      ? '!border-[#7bf1a8]'
                      : '!border-[#ffb86a]',
                  ]"
                >
                  <!-- <img
                    :src="
                      reminder.status === 'not-called'
                        ? iconStatusNotCalled
                        : reminder.status === 'called'
                        ? iconStatusCalled
                        : reminder.status === 'agreed'
                        ? iconStatusAgreed
                        : iconStatusKNM
                    "
                    alt=""
                    class="w-3 h-3"
                  /> -->
                  <span
                    :class="[
                      'text-xs font-medium',
                      reminder.status === 'not-called'
                        ? 'text-gray-600'
                        : reminder.status === 'called'
                        ? 'text-[#d08700]'
                        : reminder.status === 'agreed'
                        ? 'text-[#00a63e]'
                        : 'text-[#f54900]',
                    ]"
                  >
                    {{
                      reminder.status === "not-called"
                        ? "Chưa gọi"
                        : reminder.status === "called"
                        ? "Đã gọi"
                        : reminder.status === "agreed"
                        ? "Đồng ý"
                        : "KNM"
                    }}
                  </span>
                </span>
              </td>

              <!-- Actions -->
              <td class="px-2 py-4">
                <div class="flex items-center gap-2">
                  <!-- Call button -->
                  <button
                    class="bg-white border !border-[#7bf1a8] rounded-lg w-10 h-10 flex items-center justify-center hover:bg-green-50 transition-colors"
                    @click="callCustomer(reminder)"
                  >
                    <!-- <img :src="iconPhone" alt="Call" class="w-4 h-4" /> -->
                  </button>

                  <!-- Book appointment button -->
                  <button
                    :class="[
                      'rounded-lg h-10 px-3 flex items-center gap-2 transition-colors',
                      reminder.type === 'recheck'
                        ? 'bg-[#155dfc] hover:bg-[#0d4ed4]'
                        : reminder.type === 'vaccination'
                        ? 'bg-[#9810fa] hover:bg-[#7a0cc9]'
                        : 'bg-[#00a63e] hover:bg-[#008532]',
                    ]"
                    @click="bookFromReminder(reminder)"
                  >
                    <!-- <img :src="iconCalendarBook" alt="Book" class="w-4 h-4" /> -->
                    <span class="text-sm font-medium text-white">
                      Đặt lịch
                    </span>
                  </button>

                  <!-- Edit button -->
                  <button
                    class="bg-white border !border-[#ffb86a] rounded-lg w-10 h-10 flex items-center justify-center hover:bg-orange-50 transition-colors"
                    @click="editReminder(reminder)"
                  >
                    <!-- <img :src="iconEdit" alt="Edit" class="w-4 h-4" /> -->
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Check-in Modal -->
    <CheckInModal
      :is-open="isCheckInModalOpen"
      :appointment="selectedAppointment"
      @close="isCheckInModalOpen = false"
      @success="handleCheckInSuccess"
    />

    <!-- Create Appointment Modal -->
    <CreateAppointmentModal
      :is-open="isCreateAppointmentModalOpen"
      @close="isCreateAppointmentModalOpen = false"
      @submit="handleCreateAppointmentSubmit"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useRouter } from "vue-router";
import CheckInModal from "./check-in-modal.vue";
import CreateAppointmentModal from "./create-appointment/index.vue";
import {
  getAppointmentsWaitingCheckIn,
  getCheckedInAppointments,
  getAllAppointments,
} from "@/services/lichHenService";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

const router = useRouter();

// Icons for Appointments
const iconSearch =
  "https://www.figma.com/api/mcp/asset/2e7c946a-889d-4a42-9d38-aea7bc8066ed";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/90360139-4011-4346-9a0b-8ca69fab969f";
const iconUpcoming =
  "https://www.figma.com/api/mcp/asset/7d6466b4-674b-48b6-ab95-8278fe0e9b84";
const iconArrived =
  "https://www.figma.com/api/mcp/asset/5df771d5-1550-4ae1-ad87-6aeabe3f1e44";
const iconExamining =
  "https://www.figma.com/api/mcp/asset/6494a3df-fb4e-4a27-a657-96047dc703fd";
const iconPayment =
  "https://www.figma.com/api/mcp/asset/16a0085a-a982-434f-ab56-44c541537376";
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/215b1bb0-3b69-4bb3-97ce-a85d2e46eb3a";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/2dc876c1-5158-435b-b6b1-0daf988c9192";
const iconClock =
  "https://www.figma.com/api/mcp/asset/3823d922-20bc-4121-a652-86e1377b69b9";
const iconCalendarSmall =
  "https://www.figma.com/api/mcp/asset/8930a10d-4d79-4f01-a1be-65bae7a271b1";
const iconMale =
  "https://www.figma.com/api/mcp/asset/34fd2f8e-6dd6-478b-9fda-333a17af3bb2";
const iconUser =
  "https://www.figma.com/api/mcp/asset/8716e4ba-208e-49c1-a9fe-baf53794550f";
const iconDoctor =
  "https://www.figma.com/api/mcp/asset/97632d8e-c43d-4aa5-a2d1-03a4c2edb6ff";
const iconRoom =
  "https://www.figma.com/api/mcp/asset/0b335040-3085-48c9-8186-11fb6d926008";
const iconCheckIn =
  "https://www.figma.com/api/mcp/asset/8adeff95-562c-4cdc-859e-7bcbad6ca375";
const iconWalkIn =
  "https://www.figma.com/api/mcp/asset/28293856-dcad-4d85-9d01-cd1b62ecb0f8";
const iconMember =
  "https://www.figma.com/api/mcp/asset/996ab4be-54e4-4a9e-b4be-33dc0567b200";
const iconMoney =
  "https://www.figma.com/api/mcp/asset/4c71be7b-2b82-43a3-acd7-c4c75d231666";

// Icons for Reminders
const iconReminderRecheck =
  "https://www.figma.com/api/mcp/asset/22e604f9-99dd-4555-88aa-affc64c8b000";
const iconReminderVaccine =
  "https://www.figma.com/api/mcp/asset/011d06f7-56e5-4ec3-a486-c9843af12e88";
const iconReminderDeworming =
  "https://www.figma.com/api/mcp/asset/767464e6-a54c-482a-9c9d-b6230f97d974";
const iconCalendarTime =
  "https://www.figma.com/api/mcp/asset/60faa6c7-76c8-4c3d-a57c-14af5a8f4455";
const iconOverdue =
  "https://www.figma.com/api/mcp/asset/067c463b-e4e9-493b-b59b-03dc0dd9abdc";
const iconWeek =
  "https://www.figma.com/api/mcp/asset/7c665889-aca8-4450-bf58-05b870cb31e1";
const iconAlert =
  "https://www.figma.com/api/mcp/asset/8c439c25-dad6-4e5d-9b58-77a40dee4cb6";
const iconStatusNotCalled =
  "https://www.figma.com/api/mcp/asset/73db85ef-7831-4761-89ca-c1b09a00359d";
const iconStatusCalled =
  "https://www.figma.com/api/mcp/asset/db0fd1c6-447d-4bb3-b5fe-fe6ae8a87104";
const iconStatusAgreed =
  "https://www.figma.com/api/mcp/asset/fe58dbc3-4dc2-4a33-bc86-be8f5b76cde4";
const iconStatusKNM =
  "https://www.figma.com/api/mcp/asset/f4bbf67a-674f-46eb-a80f-b7ea0c0c8db1";
const iconPhone =
  "https://www.figma.com/api/mcp/asset/1c34e33e-f895-4abc-a0fb-7d69db40e7b9";
const iconCalendarBook =
  "https://www.figma.com/api/mcp/asset/38aa92c1-c34f-444b-90ab-2d5e59d12680";
const iconEdit =
  "https://www.figma.com/api/mcp/asset/a0fe1a77-f466-43d7-93e4-43ed4e4b7208";

// State
const searchQuery = ref("");
const activeTab = ref("list");
const activeFilter = ref("all");
const activeReminderType = ref("all");
const activeTimeFilter = ref("today");
const isCheckInModalOpen = ref(false);
const isCreateAppointmentModalOpen = ref(false);
const selectedAppointment = ref(null);

// Check-in state
const waitingCheckInAppointments = ref([]);
const checkedInAppointments = ref([]);
const loadingCheckIn = ref(false);
const loadingCheckedIn = ref(false);

// Main appointments list state
const appointments = ref([]);
const loadingAppointments = ref(false);

// Appointment Filters
const filters = ref([
  { key: "all", label: "Tất cả", count: 11, icon: null },
  { key: "upcoming", label: "Sắp tới", count: 3, icon: iconUpcoming },
  { key: "arrived", label: "Đã đến", count: 1, icon: iconArrived },
  { key: "examining", label: "Đang khám", count: 3, icon: iconExamining },
  { key: "payment", label: "Chờ thanh toán", count: 2, icon: iconPayment },
]);

// Reminder Type Filters
const reminderTypes = ref([
  { key: "all", label: "Tất cả", count: 15, icon: null },
  { key: "recheck", label: "Tái Khám", count: 5, icon: iconReminderRecheck },
  {
    key: "vaccination",
    label: "Tiêm Chủng",
    count: 8,
    icon: iconReminderVaccine,
  },
  {
    key: "deworming",
    label: "Tẩy Giun",
    count: 2,
    icon: iconReminderDeworming,
  },
]);

// Time Filters
const timeFilters = ref([
  { key: "today", label: "Hôm nay", icon: iconCalendarTime },
  { key: "overdue", label: "Quá hạn", icon: iconOverdue },
  { key: "thisWeek", label: "Tuần này", icon: iconWeek },
]);

// Reminders data
const reminders = ref([
  {
    id: 1,
    petName: "Milo",
    breed: "Golden",
    ownerName: "A. Nam",
    phone: "0901234567",
    type: "recheck",
    content: "Tái khám sau phẫu thuật",
    deadline: "Hôm nay (03/12)",
    status: "not-called",
    isOverdue: false,
  },
  {
    id: 2,
    petName: "Luna",
    breed: "Mèo Ba Tư",
    ownerName: "B. Hương",
    phone: "0912345678",
    type: "recheck",
    content: "Kiểm tra vết mổ",
    deadline: "Hôm nay (03/12)",
    status: "not-called",
    isOverdue: false,
  },
  {
    id: 3,
    petName: "Max",
    breed: "Husky",
    ownerName: "C. Tuấn",
    phone: "0923456789",
    type: "recheck",
    content: "Tái khám da liễu",
    deadline: "Hôm nay (03/12)",
    status: "called",
    isOverdue: false,
  },
  {
    id: 4,
    petName: "Rocky",
    breed: "Golden",
    ownerName: "E. Minh",
    phone: "0945678901",
    type: "recheck",
    content: "Tái khám định kỳ",
    deadline: "Hôm nay (03/12)",
    status: "agreed",
    isOverdue: false,
  },
  {
    id: 5,
    petName: "Milo",
    breed: "Golden",
    ownerName: "A. Nam",
    phone: "0901234567",
    type: "vaccination",
    content: "Tiêm mũi 3 (7 bệnh)",
    deadline: "Hôm nay (03/12)",
    status: "not-called",
    isOverdue: false,
  },
  {
    id: 6,
    petName: "Simba",
    breed: "Mèo Ba Tư",
    ownerName: "G. Tuấn",
    phone: "0967890123",
    type: "vaccination",
    content: "Tiêm vaccine 5 bệnh",
    deadline: "Hôm nay (03/12)",
    status: "not-called",
    isOverdue: false,
  },
  {
    id: 7,
    petName: "Mimi",
    breed: "Mèo Anh",
    ownerName: "K. Lan",
    phone: "0990123456",
    type: "vaccination",
    content: "Tiêm vaccine 7 bệnh",
    deadline: "Hôm nay (03/12)",
    status: "called",
    isOverdue: false,
  },
  {
    id: 8,
    petName: "Milo",
    breed: "Golden",
    ownerName: "A. Nam",
    phone: "0901234567",
    type: "deworming",
    content: "Tẩy giun định kỳ 3 tháng",
    deadline: "Hôm nay (03/12)",
    status: "not-called",
    isOverdue: false,
  },
  {
    id: 9,
    petName: "Bella",
    breed: "Poodle",
    ownerName: "D. Linh",
    phone: "0934567890",
    type: "recheck",
    content: "Tái khám nội khoa",
    deadline: "Quá hạn (02/12)",
    status: "knm",
    isOverdue: true,
  },
  {
    id: 10,
    petName: "Buddy",
    breed: "Husky",
    ownerName: "I. Khoa",
    phone: "0989012345",
    type: "vaccination",
    content: "Tiêm phòng dại mũi 1",
    deadline: "Quá hạn (01/12)",
    status: "not-called",
    isOverdue: true,
  },
]);

// Computed
const filteredAppointments = computed(() => {
  let filtered = appointments.value;

  // Filter by active filter
  if (activeFilter.value !== "all") {
    filtered = filtered.filter((app) => app.status === activeFilter.value);
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (app) =>
        app.petName.toLowerCase().includes(query) ||
        app.ownerName.toLowerCase().includes(query)
    );
  }

  return filtered;
});

const filteredReminders = computed(() => {
  let filtered = reminders.value;

  // Filter by reminder type
  if (activeReminderType.value !== "all") {
    filtered = filtered.filter((rem) => rem.type === activeReminderType.value);
  }

  // Filter by time
  if (activeTimeFilter.value === "overdue") {
    filtered = filtered.filter((rem) => rem.isOverdue);
  } else if (activeTimeFilter.value === "today") {
    filtered = filtered.filter(
      (rem) => !rem.isOverdue && rem.deadline.includes("Hôm nay")
    );
  } else if (activeTimeFilter.value === "thisWeek") {
    // TODO: Implement week filtering
    filtered = filtered.filter((rem) => !rem.isOverdue);
  }

  return filtered;
});

// Computed for check-in count
const waitingCheckInCount = computed(
  () => waitingCheckInAppointments.value.length
);

// Methods
const createAppointment = () => {
  isCreateAppointmentModalOpen.value = true;
};

const handleCreateAppointmentSubmit = (data) => {
  console.log("Create appointment:", data);
  // TODO: Implement API call
  isCreateAppointmentModalOpen.value = false;
};

const selectDate = () => {
  console.log("Select date");
  // TODO: Implement date picker
};

const formatDateTime = (datetime) => {
  if (!datetime) return "N/A";

  try {
    const date = new Date(datetime);
    return date.toLocaleString("vi-VN", {
      year: "numeric",
      month: "2-digit",
      day: "2-digit",
      hour: "2-digit",
      minute: "2-digit",
      hour12: false,
    });
  } catch (e) {
    return datetime;
  }
};

// Check-in Methods
const fetchWaitingCheckInList = async () => {
  loadingCheckIn.value = true;
  try {
    const response = await getAppointmentsWaitingCheckIn({ per_page: 100 });

    if (response.status && response.data) {
      // Handle both array and paginated response
      if (Array.isArray(response.data)) {
        waitingCheckInAppointments.value = response.data;
      } else if (response.data.data && Array.isArray(response.data.data)) {
        waitingCheckInAppointments.value = response.data.data;
      } else {
        waitingCheckInAppointments.value = [];
      }

      console.log(
        "✅ Loaded waiting check-in appointments:",
        waitingCheckInAppointments.value.length
      );
    } else {
      waitingCheckInAppointments.value = [];
    }
  } catch (error) {
    console.error("Error fetching waiting check-in appointments:", error);
    showErrorToast("Không thể tải danh sách lịch hẹn chờ check-in");
    waitingCheckInAppointments.value = [];
  } finally {
    loadingCheckIn.value = false;
  }
};

const fetchCheckedInList = async () => {
  loadingCheckedIn.value = true;
  try {
    const response = await getCheckedInAppointments({ per_page: 100 });

    if (response.status && response.data) {
      // Handle both array and paginated response
      if (Array.isArray(response.data)) {
        checkedInAppointments.value = response.data;
      } else if (response.data.data && Array.isArray(response.data.data)) {
        checkedInAppointments.value = response.data.data;
      } else {
        checkedInAppointments.value = [];
      }

      console.log(
        "✅ Loaded checked-in appointments:",
        checkedInAppointments.value.length
      );
    } else {
      checkedInAppointments.value = [];
    }
  } catch (error) {
    console.error("Error fetching checked-in appointments:", error);
    showErrorToast("Không thể tải danh sách lịch hẹn đã check-in");
    checkedInAppointments.value = [];
  } finally {
    loadingCheckedIn.value = false;
  }
};

const openCheckInModal = (appointment) => {
  selectedAppointment.value = appointment;
  isCheckInModalOpen.value = true;
};

const handleCheckInSuccess = (updatedAppointment) => {
  console.log("✅ Check-in successful:", updatedAppointment);

  // Remove from waiting list (tab "Chờ Check-in")
  waitingCheckInAppointments.value = waitingCheckInAppointments.value.filter(
    (app) => app.id !== updatedAppointment.id
  );

  // Add to checked-in list (tab "Đã Check-in")
  checkedInAppointments.value.unshift(updatedAppointment);

  // Update in main appointments list (tab "Danh sách lịch hẹn")
  const index = appointments.value.findIndex(
    (a) => a.id === updatedAppointment.id
  );
  if (index !== -1) {
    // Update the appointment status in main list
    const checkInDate = updatedAppointment.thoi_gian_checkin
      ? new Date(updatedAppointment.thoi_gian_checkin)
      : new Date();

    appointments.value[index].checkInTime = checkInDate.toLocaleTimeString(
      "vi-VN",
      { hour: "2-digit", minute: "2-digit", hour12: false }
    );
    appointments.value[index].status = "examining"; // Chuyển sang "Đang khám"
    appointments.value[index].rowColor = "bg-orange-50";
    appointments.value[index].action = "payment";
    appointments.value[index].originalData = updatedAppointment;
  }

  // Refresh all lists to ensure data consistency
  fetchWaitingCheckInList();
  fetchCheckedInList();
  fetchAllAppointments();

  // Update filter counts
  updateFilterCounts();
};

const refreshCheckInList = () => {
  fetchWaitingCheckInList();
};

const refreshCheckedInList = () => {
  fetchCheckedInList();
};

// Fetch all appointments for main list
const fetchAllAppointments = async () => {
  loadingAppointments.value = true;
  try {
    const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
    const response = await getAllAppointments({
      per_page: 100,
      from_date: today, // Lấy lịch từ hôm nay
    });

    if (response.status && response.data) {
      let appointmentsList = [];

      // Handle both array and paginated response
      if (Array.isArray(response.data)) {
        appointmentsList = response.data;
      } else if (response.data.data && Array.isArray(response.data.data)) {
        appointmentsList = response.data.data;
      }

      // Transform data to match UI format
      appointments.value = appointmentsList.map((app) => {
        // Parse appointment time
        const appointmentDate = app.ngay_gio ? new Date(app.ngay_gio) : null;
        const appointmentTime = appointmentDate
          ? appointmentDate.toLocaleTimeString("vi-VN", {
              hour: "2-digit",
              minute: "2-digit",
              hour12: false,
            })
          : null;

        // Parse check-in time
        const checkInDate = app.thoi_gian_checkin
          ? new Date(app.thoi_gian_checkin)
          : null;
        const checkInTime = checkInDate
          ? checkInDate.toLocaleTimeString("vi-VN", {
              hour: "2-digit",
              minute: "2-digit",
              hour12: false,
            })
          : null;

        // Determine source (scheduled, walkin, member)
        let source = "scheduled"; // Default
        if (app.ghi_chu && app.ghi_chu.toLowerCase().includes("vãng lai")) {
          source = "walkin";
        } else if (
          app.ghi_chu &&
          app.ghi_chu.toLowerCase().includes("thành viên")
        ) {
          source = "member";
        }

        // Determine status and action
        let status = "upcoming";
        let action = "checkin";
        let rowColor = "";

        if (app.trang_thai === "pending" || app.trang_thai === "confirmed") {
          status = checkInTime ? "arrived" : "upcoming";
          rowColor = checkInTime ? "" : "bg-blue-50";
          action = "checkin";
        } else if (app.trang_thai === "in-progress") {
          status = "examining";
          rowColor = "bg-orange-50";
          action = "payment";
        } else if (app.trang_thai === "completed") {
          status = "payment";
          rowColor = "bg-green-50";
          action = "payment";
        }

        return {
          id: app.id,
          appointmentTime,
          checkInTime,
          source,
          petName: app.thu_cung?.ten_thu_cung || "N/A",
          hasMale:
            app.thu_cung?.giong_loai?.toLowerCase().includes("đực") || false,
          ownerName: app.khach_hang?.full_name || "N/A",
          service: app.dich_vu?.ten_dich_vu || "N/A",
          doctor: app.nhan_vien?.full_name || "Chưa phân công",
          room: "Phòng 102", // Mock - có thể thêm từ backend
          status,
          action,
          rowColor,
          // Keep original data for reference
          originalData: app,
        };
      });

      console.log("✅ Loaded appointments:", appointments.value.length);

      // Update filter counts
      updateFilterCounts();
    } else {
      appointments.value = [];
    }
  } catch (error) {
    console.error("Error fetching appointments:", error);
    showErrorToast("Không thể tải danh sách lịch hẹn");
    appointments.value = [];
  } finally {
    loadingAppointments.value = false;
  }
};

// Update filter counts based on current appointments
const updateFilterCounts = () => {
  const all = appointments.value.length;
  const upcoming = appointments.value.filter(
    (a) => a.status === "upcoming"
  ).length;
  const arrived = appointments.value.filter(
    (a) => a.status === "arrived"
  ).length;
  const examining = appointments.value.filter(
    (a) => a.status === "examining"
  ).length;
  const payment = appointments.value.filter(
    (a) => a.status === "payment"
  ).length;

  filters.value = [
    { key: "all", label: "Tất cả", count: all, icon: null },
    { key: "upcoming", label: "Sắp tới", count: upcoming, icon: iconUpcoming },
    { key: "arrived", label: "Đã đến", count: arrived, icon: iconArrived },
    {
      key: "examining",
      label: "Đang khám",
      count: examining,
      icon: iconExamining,
    },
    {
      key: "payment",
      label: "Chờ thanh toán",
      count: payment,
      icon: iconPayment,
    },
  ];
};

// Check-in from main list (tab "Danh sách lịch hẹn")
const checkIn = (appointment) => {
  // If appointment has originalData (from API), use it
  if (appointment.originalData) {
    selectedAppointment.value = appointment.originalData;
  } else {
    // Fallback for old format
    selectedAppointment.value = {
      id: appointment.id,
      ...appointment,
    };
  }
  isCheckInModalOpen.value = true;
};

// Handle successful check-in (unified for both tabs)
const handleCheckInConfirm = (patientInfo) => {
  // This function is deprecated, use handleCheckInSuccess instead
  console.warn("handleCheckInConfirm is deprecated, use handleCheckInSuccess");
  handleCheckInSuccess(patientInfo);
};

const collectPayment = (appointment) => {
  // Điều hướng sang trang hóa đơn với lich_hen_id
  router.push({
    path: '/nurse/invoices',
    query: { lich_hen_id: appointment.id }
  });
};

// Reminder Methods
const callCustomer = (reminder) => {
  console.log("Call customer:", reminder.ownerName, reminder.phone);
  // TODO: Implement call customer logic
};

const bookFromReminder = (reminder) => {
  console.log("Book appointment from reminder:", reminder.petName);
  // TODO: Implement booking from reminder
};

const editReminder = (reminder) => {
  console.log("Edit reminder:", reminder.id);
  // TODO: Implement edit reminder logic
};

// Watch for tab changes to load data
watch(activeTab, (newTab) => {
  if (newTab === "check-in") {
    fetchWaitingCheckInList();
  } else if (newTab === "checked-in") {
    fetchCheckedInList();
  } else if (newTab === "list") {
    fetchAllAppointments();
  }
});

// Load data on mount
onMounted(() => {
  console.log("✅ Nurse LichHen component mounted");

  // Load main appointments list by default
  fetchAllAppointments();

  // Load check-in data if starting on that tab
  if (activeTab.value === "check-in") {
    fetchWaitingCheckInList();
  } else if (activeTab.value === "checked-in") {
    fetchCheckedInList();
  }
});
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&display=swap");

* {
  font-family: "Nunito Sans", sans-serif;
}
</style>