<template>
  <div class="min-h-screen font-nunitoSans">
    <div class="container mx-auto px-6 max-w-6xl">
      <!-- Header -->
      <div
        class="flex justify-between items-center mb-6 flex-col md:flex-row gap-4"
      >
        <div>
          <h1 class="text-xl font-bold text-black">Lịch hẹn khám</h1>
          <p class="text-lg font-medium text-gray-600">
            Quản lý các buổi khám đã đặt
          </p>
        </div>
        <button
          @click="handleNewAppointment"
          class="flex items-center gap-3 px-6 py-3 bg-[#5A9690] text-white rounded-lg font-medium text-lg hover:bg-teal-700 transition"
        >
          <AddIcon />
          Đặt lịch mới
        </button>
      </div>

      <!-- Tabs -->
      <div class="flex bg-zinc-100 rounded-2xl p-1 gap-1 mb-8 w-fit">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="
            activeTab === tab.id
              ? 'bg-white text-black shadow-sm'
              : 'text-black hover:opacity-70'
          "
          class="px-6 py-2.5 rounded-2xl font-semibold text-base transition"
        >
          {{ tab.label }} ({{ tab.count }})
        </button>
      </div>

      <!-- Click-outside overlay -->
      <div
        v-if="activeDropdown"
        class="fixed inset-0 z-10"
        @click="activeDropdown = null"
      />

      <!-- Filter -->
      <div class="bg-gray-200 border !border-black/20 rounded-2xl p-6 mb-8">
        <div class="flex flex-wrap items-center gap-4">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <path
                d="M3 5h14M7 10h6M5 15h10"
                stroke="#374151"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
            <span class="font-medium text-gray-700">Lọc theo:</span>
          </div>

          <div class="flex flex-1 flex-wrap gap-4">
            <!-- Dropdown: Thú cưng -->
            <div class="relative flex-1 min-w-48">
              <button
                @click.stop="toggleDropdown('pet')"
                :class="[
                  'w-full flex justify-between items-center px-4 py-2 bg-white rounded-lg transition',
                  selectedPet !== 'all'
                    ? 'ring-2 ring-[#5A9690]'
                    : 'hover:opacity-80',
                ]"
              >
                <span
                  :class="
                    selectedPet !== 'all'
                      ? 'text-[#5A9690] font-semibold'
                      : 'text-gray-500 font-medium'
                  "
                  >{{ selectedPetLabel }}</span
                >
                <svg
                  class="w-4 h-4 text-gray-400 transition-transform"
                  :class="activeDropdown === 'pet' ? 'rotate-180' : ''"
                  viewBox="0 0 16 16"
                  fill="none"
                >
                  <path
                    d="M2 5l6 6 6-6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  />
                </svg>
              </button>
              <div
                v-if="activeDropdown === 'pet'"
                class="absolute top-full left-0 right-0 z-20 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <button
                  @click.stop="selectPetOption('all')"
                  :class="[
                    'w-full px-4 py-2.5 text-left text-sm transition hover:bg-gray-50',
                    selectedPet === 'all'
                      ? 'text-[#5A9690] font-semibold bg-teal-50'
                      : 'text-gray-700 font-medium',
                  ]"
                >
                  Tất cả thú cưng
                </button>
                <button
                  v-for="p in pets"
                  :key="p.id"
                  @click.stop="selectPetOption(p.id)"
                  :class="[
                    'w-full px-4 py-2.5 text-left text-sm transition hover:bg-gray-50',
                    String(selectedPet) === String(p.id)
                      ? 'text-[#5A9690] font-semibold bg-teal-50'
                      : 'text-gray-700',
                  ]"
                >
                  {{ p.ten_thu_cung || p.name || p.ten }}
                </button>
              </div>
            </div>

            <!-- Dropdown: Dịch vụ -->
            <div class="relative flex-1 min-w-48">
              <button
                @click.stop="toggleDropdown('service')"
                :class="[
                  'w-full flex justify-between items-center px-4 py-2 bg-white rounded-lg transition',
                  selectedService !== 'all'
                    ? 'ring-2 ring-[#5A9690]'
                    : 'hover:opacity-80',
                ]"
              >
                <span
                  :class="
                    selectedService !== 'all'
                      ? 'text-[#5A9690] font-semibold'
                      : 'text-gray-500 font-medium'
                  "
                  >{{ selectedServiceLabel }}</span
                >
                <svg
                  class="w-4 h-4 text-gray-400 transition-transform"
                  :class="activeDropdown === 'service' ? 'rotate-180' : ''"
                  viewBox="0 0 16 16"
                  fill="none"
                >
                  <path
                    d="M2 5l6 6 6-6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  />
                </svg>
              </button>
              <div
                v-if="activeDropdown === 'service'"
                class="absolute top-full left-0 right-0 z-20 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg overflow-hidden"
              >
                <button
                  @click.stop="selectServiceOption('all')"
                  :class="[
                    'w-full px-4 py-2.5 text-left text-sm transition hover:bg-gray-50',
                    selectedService === 'all'
                      ? 'text-[#5A9690] font-semibold bg-teal-50'
                      : 'text-gray-700 font-medium',
                  ]"
                >
                  Tất cả dịch vụ
                </button>
                <button
                  v-for="s in services"
                  :key="s.id"
                  @click.stop="selectServiceOption(s.id)"
                  :class="[
                    'w-full px-4 py-2.5 text-left text-sm transition hover:bg-gray-50',
                    String(selectedService) === String(s.id)
                      ? 'text-[#5A9690] font-semibold bg-teal-50'
                      : 'text-gray-700',
                  ]"
                >
                  {{ s.ten_dich_vu || s.name || s.ten }}
                </button>
              </div>
            </div>

            <!-- Dropdown: Khoảng thời gian -->
            <div class="relative flex-1 min-w-48">
              <button
                @click.stop="toggleDropdown('date')"
                :class="[
                  'w-full flex justify-between items-center px-4 py-2 bg-white rounded-lg transition',
                  dateFrom || dateTo
                    ? 'ring-2 ring-[#5A9690]'
                    : 'hover:opacity-80',
                ]"
              >
                <span
                  :class="
                    dateFrom || dateTo
                      ? 'text-[#5A9690] font-semibold'
                      : 'text-gray-500 font-medium'
                  "
                  >{{ dateRangeLabel }}</span
                >
                <svg
                  class="w-4 h-4 text-gray-400 transition-transform"
                  :class="activeDropdown === 'date' ? 'rotate-180' : ''"
                  viewBox="0 0 16 16"
                  fill="none"
                >
                  <path
                    d="M2 5l6 6 6-6"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                  />
                </svg>
              </button>
              <div
                v-if="activeDropdown === 'date'"
                class="absolute top-full left-0 z-20 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 min-w-72"
                @click.stop
              >
                <p
                  class="text-xs font-semibold text-gray-500 mb-3 uppercase tracking-wide"
                >
                  Khoảng thời gian
                </p>
                <div class="flex flex-col gap-2">
                  <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 w-8 shrink-0">Từ</label>
                    <input
                      type="date"
                      v-model="dateFrom"
                      class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#5A9690]"
                    />
                  </div>
                  <div class="flex items-center gap-2">
                    <label class="text-sm text-gray-600 w-8 shrink-0"
                      >Đến</label
                    >
                    <input
                      type="date"
                      v-model="dateTo"
                      class="flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#5A9690]"
                    />
                  </div>
                </div>
                <button
                  @click.stop="applyDateFilter"
                  class="mt-3 w-full py-2 bg-[#5A9690] text-white text-sm font-semibold rounded-lg hover:bg-[#4a7d78] transition"
                >
                  Áp dụng
                </button>
              </div>
            </div>
          </div>

          <button
            @click="clearFilters"
            class="px-3 py-1 text-teal-700 font-medium hover:opacity-70 transition"
          >
            Xóa bộ lọc
          </button>
        </div>
      </div>

      <!-- TAB SẮP TỚI -->
      <div v-if="activeTab === 'upcoming'" class="grid grid-cols-1 gap-4">
        <div
          v-for="appt in paginatedList"
          :key="appt.id"
          class="group bg-white rounded-xl overflow-hidden border !border-teal-100/60 shadow-sm hover:shadow-md transition-all duration-300"
        >
          <!-- Card Header: Super Compact -->
          <div
            class="bg-gradient-to-r from-teal-50/30 to-white border-b !border-teal-50/50 px-5 py-3.5"
          >
            <div class="flex flex-row items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <div
                  class="p-2 bg-teal-50/50 rounded-lg border !border-teal-100/50 flex items-center justify-center"
                >
                  <svg
                    class="w-5 h-5 text-teal-600"
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
                </div>
                <div class="flex flex-wrap items-center gap-2">
                  <h3 class="text-lg font-bold text-gray-900 leading-tight">
                    {{ appt.service }}
                  </h3>
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full font-bold text-[10px] uppercase tracking-wider',
                      statusClass(appt.status),
                    ]"
                  >
                    {{ statusLabel(appt.status) }}
                  </span>
                  <span
                    class="text-gray-400 text-xs font-medium hidden sm:inline"
                    >#{{ appt.id }}</span
                  >
                </div>
              </div>

              <div
                class="flex items-center gap-3 bg-white p-2 px-4 rounded-lg border border-teal-50 shadow-sm"
              >
                <div class="flex items-baseline gap-1.5">
                  <span
                    class="text-gray-400 text-[10px] font-bold uppercase tracking-tight"
                    >Ngày:</span
                  >
                  <span class="text-gray-900 font-bold text-sm">{{
                    appt.date
                  }}</span>
                </div>
                <div class="w-px h-5 bg-teal-100 mx-1"></div>
                <div class="flex items-baseline gap-1.5">
                  <span
                    class="text-teal-600 text-[10px] font-bold uppercase tracking-tight"
                    >Giờ:</span
                  >
                  <span class="text-teal-600 font-black text-base">{{
                    appt.time
                  }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Card Body: Horizontal Layout & Minimal Spacing -->
          <div class="px-5 py-3.5">
            <div
              class="flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
              <div class="flex flex-1 flex-col sm:flex-row gap-8">
                <!-- Pet Info -->
                <div class="flex items-center gap-3">
                  <div
                    class="flex-shrink-0 w-10 h-10 bg-sky-50 rounded-lg flex items-center justify-center border !border-sky-100/50"
                  >
                    <svg
                      class="w-5 h-5 text-sky-500"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                  <div class="min-w-0">
                    <p
                      class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-0.5"
                    >
                      Thú cưng
                    </p>
                    <div class="flex items-baseline gap-2 truncate">
                      <span class="text-base font-bold text-gray-900">{{
                        appt.pet
                      }}</span>
                      <span class="text-gray-400 font-medium text-xs"
                        >• {{ appt.breed }}</span
                      >
                    </div>
                  </div>
                </div>

                <!-- Location Info -->
                <div class="flex items-center gap-3">
                  <div
                    class="flex-shrink-0 w-10 h-10 bg-teal-50 rounded-lg flex items-center justify-center border !border-teal-100/50"
                  >
                    <svg
                      class="w-5 h-5 text-teal-600"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                      />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p
                      class="text-gray-400 text-[10px] font-bold uppercase tracking-widest mb-0.5"
                    >
                      Địa điểm
                    </p>
                    <p class="text-sm font-bold text-gray-900 truncate">
                      {{ appt.clinic }}
                    </p>
                  </div>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center gap-3">
                <button
                  @click="handleReschedule(appt.id)"
                  class="p-2.5 border border-teal-100 rounded-lg text-gray-600 hover:bg-teal-50 hover:text-teal-700 transition-colors shadow-sm"
                  title="Đổi lịch"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                    />
                  </svg>
                </button>
                <button
                  @click="handleCancel(appt.id)"
                  class="p-2.5 border border-red-50 rounded-lg text-red-500 hover:bg-red-50 transition-colors shadow-sm"
                  title="Hủy hẹn"
                >
                  <svg
                    class="w-5 h-5"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
                <button
                  @click="openDetail(appt)"
                  class="px-5 py-2.5 bg-[#5A9690] text-white rounded-lg font-bold text-sm hover:bg-[#4a7d78] shadow-sm flex items-center gap-2 transition-all"
                >
                  Chi tiết
                  <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M9 5l7 7-7 7"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TAB ĐÃ QUA -->
      <div v-if="activeTab === 'past'" class="grid grid-cols-1 gap-4">
        <div
          v-for="appt in paginatedList"
          :key="appt.id"
          class="bg-white rounded-xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 opacity-90"
        >
          <div class="bg-gray-50/50 px-5 py-3.5 border-b border-gray-100">
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-3">
                <div class="flex flex-wrap items-center gap-3">
                  <h3 class="text-lg font-bold text-gray-700">
                    {{ appt.service }}
                  </h3>
                  <div class="flex gap-2">
                    <span
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                      :class="
                        appt.status === 'completed'
                          ? 'bg-emerald-100 text-green-700'
                          : 'bg-gray-200 text-gray-700'
                      "
                    >
                      {{ statusLabel(appt.status) }}
                    </span>
                    <span
                      v-if="
                        appt.raw.thanhToan?.trang_thai === 'paid' ||
                        appt.status === 'paid'
                      "
                      class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-[10px] font-bold uppercase tracking-wider"
                    >
                      Đã thanh toán
                    </span>
                  </div>
                </div>
              </div>
              <div class="flex items-center gap-3 text-right">
                <div class="text-gray-500 font-bold text-sm">
                  {{ appt.date }}
                </div>
                <div class="w-px h-4 bg-gray-300"></div>
                <div class="text-gray-400 font-medium text-sm">
                  {{ appt.time }}
                </div>
              </div>
            </div>
          </div>

          <div class="px-5 py-3.5">
            <div
              class="flex flex-col md:flex-row md:items-center justify-between gap-4"
            >
              <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                  <div
                    class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200"
                  >
                    <svg
                      class="w-4 h-4 text-gray-500"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                      />
                    </svg>
                  </div>
                  <span class="text-sm font-bold text-gray-600">{{
                    appt.pet
                  }}</span>
                </div>
                <div class="w-px h-4 bg-gray-200"></div>
                <div class="text-xs text-gray-500 font-medium">
                  Mã: #{{ appt.id }}
                </div>
              </div>

              <div class="flex items-center gap-3">
                <button
                  v-if="appt.status === 'completed'"
                  @click="viewResult(appt.id)"
                  class="px-4 py-2 bg-white border border-teal-600 text-teal-600 rounded-lg hover:bg-teal-50 transition font-bold text-xs"
                >
                  Xem kết quả
                </button>
                <button
                  v-if="appt.status !== 'cancelled'"
                  @click="rebook(appt.id)"
                  class="px-4 py-2 bg-[#5A9690] text-white rounded-lg hover:bg-[#4a7d78] transition font-bold text-xs shadow-sm"
                >
                  Đặt lại
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div
        v-if="totalPages > 1"
        class="flex items-center justify-center gap-2 mt-8"
      >
        <button
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage === 1"
          :class="[
            'px-3 py-2 rounded-lg text-sm font-semibold border transition-colors',
            currentPage === 1
              ? 'border-gray-200 text-gray-400 cursor-not-allowed bg-white'
              : 'border-gray-300 text-gray-700 hover:bg-gray-50 bg-white',
          ]"
        >
          Trang trước
        </button>

        <button
          v-for="page in totalPages"
          :key="page"
          @click="goToPage(page)"
          :class="[
            'w-9 h-9 rounded-lg text-sm font-semibold border transition-colors',
            page === currentPage
              ? 'bg-[#5A9690] text-white border-[#5A9690]'
              : 'border-gray-300 text-gray-700 hover:bg-gray-50 bg-white',
          ]"
        >
          {{ page }}
        </button>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          :class="[
            'px-3 py-2 rounded-lg text-sm font-semibold border transition-colors',
            currentPage === totalPages
              ? 'border-gray-200 text-gray-400 cursor-not-allowed bg-white'
              : 'border-gray-300 text-gray-700 hover:bg-gray-50 bg-white',
          ]"
        >
          Trang tiếp
        </button>
      </div>
    </div>

    <!-- POPUP CHI TIẾT LỊCH HẸN -->
    <teleport to="body">
      <ChiTietLichHen
        v-if="selectedAppt"
        :is-open="showDetail"
        :selected-appt="selectedAppt"
        @close="showDetail = false"
      />
    </teleport>

    <!-- POPUP ĐẶT LỊCH KHÁM -->
    <teleport to="body">
      <DatLichKham
        :is-open="showBookingPopup"
        :initial-data="rebookData"
        @close="showBookingPopup = false"
        @confirm="handleBookingConfirm"
        @openAddPet="() => {}"
      />
    </teleport>

    <!-- POPUP ĐỔI LỊCH HẸN -->
    <teleport to="body">
      <DoiLich
        v-if="rescheduleAppt"
        :is-open="showReschedulePopup"
        :old-appointment="rescheduleAppt"
        @close="showReschedulePopup = false"
        @save="handleRescheduleConfirm"
      />
    </teleport>

    <!-- POPUP HỦY LỊCH HẸN -->
    <teleport to="body">
      <HuyHen
        v-if="cancelAppt"
        :is-open="showCancelPopup"
        :appointment="cancelAppt"
        :cancel-status="cancelStatus"
        @close="showCancelPopup = false"
        @keep="showCancelPopup = false"
        @cancel="handleCancelConfirm"
      />
    </teleport>

    <!-- POPUP KẾT QUẢ KHÁM BỆNH -->
    <teleport to="body">
      <KetQuaKhambenh
        v-if="resultData"
        :is-open="showResultPopup"
        :result="resultData"
        @close="showResultPopup = false"
        @download="(file) => console.log('Downloading', file)"
      />
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import client from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import AddIcon from "@/assets/svg/add.svg";

import DatLichKham from "./book-appointment/index.vue";
import ChiTietLichHen from "./appointment-detail/index.vue";
import DoiLich from "./reschedule-appointment/index.vue";
import HuyHen from "./cancel-appointment/index.vue";
import KetQuaKhambenh from "./medical-result/index.vue";

const activeTab = ref("upcoming");
// tabs counts are computed based on filtered lists (defined later)

const upcomingAppointments = ref([]);
const pastAppointments = ref([]);
const loading = ref(false);

// Pagination state
const currentPage = ref(1);
const perPage = 4;

const totalPages = computed(() => {
  const list =
    activeTab.value === "upcoming"
      ? upcomingAppointments.value
      : pastAppointments.value;
  return Math.max(1, Math.ceil(list.length / perPage));
});

const paginatedList = computed(() => {
  const list =
    activeTab.value === "upcoming"
      ? upcomingAppointments.value
      : pastAppointments.value;
  const start = (currentPage.value - 1) * perPage;
  return list.slice(start, start + perPage);
});

const mapAppointment = (item) => {
  // item: LichHen with relations thuCung, dichVu, nhanVien, thanhToan
  const ngayGio = item.ngay_gio ? new Date(item.ngay_gio) : null;

  return {
    id: item.id || item.ma || `LH${item.id}`,
    raw: item,
    service:
      item.dichVu?.name ||
      item.dichVu?.ten_dich_vu ||
      item.dich_vu?.ten ||
      item.dich_vu?.name ||
      "Dịch vụ",
    date: ngayGio
      ? `${String(ngayGio.getDate()).padStart(2, "0")}/${String(
          ngayGio.getMonth() + 1
        ).padStart(2, "0")}/${ngayGio.getFullYear()}`
      : "",
    time: ngayGio
      ? `${String(ngayGio.getHours()).padStart(2, "0")}:${String(
          ngayGio.getMinutes()
        ).padStart(2, "0")}`
      : "",
    pet:
      item.thuCung?.ten_thu_cung ||
      item.thuCung?.ten ||
      item.thuCung?.name ||
      item.thu_cung?.ten_thu_cung ||
      item.thu_cung?.ten ||
      item.thu_cung?.name ||
      (typeof item.thuCung === "string" ? item.thuCung : "") ||
      "Thú cưng",
    breed:
      item.thuCung?.giong_thu_cung ||
      item.thuCung?.giong ||
      item.thuCung?.breed ||
      item.thu_cung?.giong_thu_cung ||
      "-",
    age:
      item.thuCung?.tuoi_thu_cung ||
      item.thuCung?.age ||
      item.thu_cung?.tuoi_thu_cung ||
      "-",
    weight: item.thuCung?.can_nang
      ? `${item.thuCung.can_nang} kg`
      : item.thuCung?.weight
      ? `${item.thuCung.weight} kg`
      : "-",
    clinic:
      item.dia_diem || item.clinic || "Phòng khám Petty - Chi nhánh Quận 1",
    address: item.dia_chi || "123 Nguyễn Huệ, Q.1, TP.HCM",
    note: item.ghi_chu || "",
    instruction: item.huong_dan || "",
    status: item.trang_thai || (item.thanhToan ? "paid" : "pending"),
  };
};

// helpers to render status badge label and classes
const statusLabel = (s) => {
  if (!s) return "Đang xử lý";
  const st = String(s).toLowerCase();
  switch (st) {
    case "pending":
      return "Chờ xác nhận";
    case "confirmed":
    case "confirmed_by_staff":
      return "Đã xác nhận";
    case "paid":
      return "Đã thanh toán";
    case "completed":
      return "Hoàn thành";
    case "cancelled":
    case "canceled":
      return "Đã hủy";
    default:
      // fallback: if backend already returns a human label, use it
      return String(s);
  }
};

const statusClass = (s) => {
  const st = s ? String(s).toLowerCase() : "";
  if (st === "confirmed" || st === "confirmed_by_staff")
    return "bg-teal-100 text-teal-700";
  if (st === "pending") return "bg-yellow-100 text-yellow-800";
  if (st === "paid" || st === "completed")
    return "bg-emerald-100 text-green-700";
  if (st === "cancelled" || st === "canceled") return "bg-red-100 text-red-700";
  return "bg-gray-200 text-gray-700";
};

const fetchAppointments = async (filters = {}) => {
  loading.value = true;
  try {
    const params = { ...filters };
    const res = await client.get(`/lich-hen`, { params });

    let list = [];
    if (res.data && res.data.data) {
      if (Array.isArray(res.data.data)) list = res.data.data;
      else if (Array.isArray(res.data.data.data)) list = res.data.data.data;
    }

    const today = new Date();
    const todayStart = new Date(
      today.getFullYear(),
      today.getMonth(),
      today.getDate()
    );
    const upcoming = [];
    const past = [];
    list.forEach((it) => {
      const mapped = mapAppointment(it);
      const dt = it.ngay_gio ? new Date(it.ngay_gio) : null;
      const status = String(it.trang_thai || "").toLowerCase();
      const isCancelled = status === "cancelled" || status === "canceled";
      if (!isCancelled && dt && dt >= todayStart) {
        upcoming.push(mapped);
      } else {
        past.push(mapped);
      }
    });

    upcomingAppointments.value = upcoming;
    pastAppointments.value = past;
  } catch (err) {
    console.error("Lỗi khi lấy lịch hẹn:", err);
    showErrorToast("Lỗi", "Không thể tải lịch hẹn. Vui lòng thử lại.");
  } finally {
    loading.value = false;
  }
};

// --- Filters state & helpers ---
const pets = ref([]);
const services = ref([]);
const selectedPet = ref("all");
const selectedService = ref("all");
const dateFrom = ref("");
const dateTo = ref("");
const activeDropdown = ref(null);

const selectedPetLabel = computed(() => {
  if (selectedPet.value === "all") return "Tất cả thú cưng";
  const p = pets.value.find((x) => String(x.id) === String(selectedPet.value));
  return p
    ? p.name || p.ten || p.ten_thu_cung || "Thú cưng"
    : "Tất cả thú cưng";
});

const selectedServiceLabel = computed(() => {
  if (selectedService.value === "all") return "Tất cả dịch vụ";
  const s = services.value.find(
    (x) => String(x.id) === String(selectedService.value)
  );
  return s ? s.name || s.ten || s.ten_dich_vu || "Dịch vụ" : "Tất cả dịch vụ";
});

const dateRangeLabel = computed(() => {
  if (!dateFrom.value && !dateTo.value) return "Khoảng thời gian";
  if (dateFrom.value && !dateTo.value) return `Từ ${dateFrom.value}`;
  if (!dateFrom.value && dateTo.value) return `Đến ${dateTo.value}`;
  return `${dateFrom.value} → ${dateTo.value}`;
});

const fetchFilterOptions = async () => {
  try {
    const [pRes, sRes] = await Promise.all([
      client.get(`/thu-cung?all=1`).catch(() => ({ data: [] })),
      client.get(`/dich-vu`).catch(() => ({ data: [] })),
    ]);
    pets.value = Array.isArray(pRes.data) ? pRes.data : pRes.data?.data || [];
    services.value = Array.isArray(sRes.data)
      ? sRes.data
      : sRes.data?.data || [];
  } catch (e) {
    console.warn("Không thể nạp tùy chọn lọc:", e);
  }
};

const buildFilterParams = () => {
  const params = {};
  if (selectedPet.value !== "all") {
    const p = pets.value.find(
      (x) => String(x.id) === String(selectedPet.value)
    );
    if (p)
      params.pet_name =
        p.ten_thu_cung || p.name || p.ten || p.display_name || "";
  }
  if (selectedService.value !== "all") {
    params.dich_vu_id = selectedService.value;
  }
  if (dateFrom.value) params.from_date = dateFrom.value;
  if (dateTo.value) params.to_date = dateTo.value;
  return params;
};

onMounted(() => {
  fetchAppointments();
  fetchFilterOptions();
});

watch(activeTab, () => {
  activeDropdown.value = null;
  currentPage.value = 1;
});

const toggleDropdown = (type) => {
  activeDropdown.value = activeDropdown.value === type ? null : type;
};

const selectPetOption = (id) => {
  selectedPet.value = id;
  activeDropdown.value = null;
  currentPage.value = 1;
  fetchAppointments(buildFilterParams());
};

const selectServiceOption = (id) => {
  selectedService.value = id;
  activeDropdown.value = null;
  currentPage.value = 1;
  fetchAppointments(buildFilterParams());
};

const applyDateFilter = () => {
  activeDropdown.value = null;
  currentPage.value = 1;
  fetchAppointments(buildFilterParams());
};

const clearFilters = () => {
  selectedPet.value = "all";
  selectedService.value = "all";
  dateFrom.value = "";
  dateTo.value = "";
  currentPage.value = 1;
  fetchAppointments();
};

const goToPage = (page) => {
  if (page < 1 || page > totalPages.value) return;
  currentPage.value = page;
  window.scrollTo({ top: 0, behavior: "smooth" });
};

const tabs = computed(() => [
  {
    id: "upcoming",
    label: "Sắp tới",
    count: upcomingAppointments.value.length,
  },
  { id: "past", label: "Đã qua", count: pastAppointments.value.length },
]);

const showDetail = ref(false);
const selectedAppt = ref(null);
const showBookingPopup = ref(false);
const rebookData = ref(null);
const showReschedulePopup = ref(false);
const rescheduleAppt = ref(null);
const showCancelPopup = ref(false);
const cancelAppt = ref(null);
const cancelStatus = ref("unpaid"); // 'late' | 'refundable' | 'unpaid'
const showResultPopup = ref(false);
const resultData = ref(null);

const openDetail = (appt) => {
  selectedAppt.value = { ...appt };
  showDetail.value = true;
};

const handleNewAppointment = () => {
  rebookData.value = null;
  showBookingPopup.value = true;
};

const handleBookingConfirm = async (bookingData) => {
  await fetchAppointments();
  showBookingPopup.value = false;
};

const handleReschedule = (id) => {
  const appt = upcomingAppointments.value.find((a) => a.id === id);
  if (appt) {
    rescheduleAppt.value = {
      id: appt.id,
      petName: appt.pet,
      serviceName: appt.service,
      dateTime: `${appt.time} - ${appt.date}`,
      date: appt.date,
      time: appt.time,
    };
    showReschedulePopup.value = true;
  }
};

const handleRescheduleConfirm = async (data) => {
  // data: { oldDateTime, newDate, newTime, newDateTime }
  try {
    const apptId = rescheduleAppt.value?.id || data.id;
    if (!apptId) throw new Error("Không xác định được lịch hẹn để đổi.");

    // newDate is DD/MM/YYYY -> convert to YYYY-MM-DD
    const parts = data.newDate.split("/");
    if (parts.length !== 3) throw new Error("Ngày mới không hợp lệ.");
    const day = parts[0].padStart(2, "0");
    const month = parts[1].padStart(2, "0");
    const year = parts[2];
    const ngayGio = `${year}-${month}-${day} ${data.newTime}:00`;

    // send PATCH to update ngay_gio
    const res = await client.patch(`/lich-hen/${apptId}/ngay-gio`, {
      ngay_gio: ngayGio,
    });

    if (res.data && res.data.status) {
      showSuccessToast("Đổi lịch", "Đã cập nhật ngày giờ lịch hẹn");
      // refresh appointments
      await fetchAppointments();
      showReschedulePopup.value = false;
      rescheduleAppt.value = null;
    } else {
      console.error("Unexpected response", res);
      showErrorToast("Đổi lịch", "Không thể đổi lịch. Vui lòng thử lại.");
    }
  } catch (err) {
    console.error("Lỗi khi đổi lịch:", err);
    const msg =
      err?.response?.data?.message || err.message || "Lỗi khi đổi lịch hẹn";
    showErrorToast("Đổi lịch", msg);
  }
};

const handleCancel = (id) => {
  const appt = upcomingAppointments.value.find((a) => a.id === id);
  if (!appt) return;

  const raw = appt.raw;

  // Determine payment state from thanhToan relation
  const paid =
    raw.thanhToan &&
    (raw.thanhToan.trang_thai === "paid" ||
      raw.thanhToan.trang_thai === "da_hoan_thanh" ||
      raw.thanhToan.trang_thai === "da_thanh_toan");

  // Format paid amount if available
  const rawAmount = raw.thanhToan?.so_tien ?? raw.thanhToan?.tong_tien ?? null;
  const paidAmount =
    rawAmount != null ? new Intl.NumberFormat("vi-VN").format(rawAmount) : null;

  // Determine cancel status from real data
  let status;
  if (!paid) {
    status = "unpaid";
  } else {
    const apptTime = raw.ngay_gio ? new Date(raw.ngay_gio) : null;
    const hoursUntil = apptTime
      ? (apptTime - Date.now()) / 3_600_000
      : Infinity;
    status = hoursUntil < 24 ? "late" : "refundable";
  }

  cancelAppt.value = {
    id: appt.id,
    petName: appt.pet,
    serviceName: appt.service,
    paidAmount,
  };
  cancelStatus.value = status;
  showCancelPopup.value = true;
};

const handleCancelConfirm = async (data) => {
  // data: { appointmentId, reason, cancelStatus } emitted from HuyHen
  try {
    const id = data?.appointmentId || data?.id;
    if (!id) throw new Error("Không xác định được lịch hẹn để hủy.");

    const res = await client.delete(`/lich-hen/${id}`);
    if (res.data && res.data.status) {
      showSuccessToast("Hủy hẹn", "Lịch hẹn đã được hủy");
      // refresh list
      await fetchAppointments();
      showCancelPopup.value = false;
      cancelAppt.value = null;
    } else {
      console.error("Unexpected delete response", res);
      showErrorToast("Hủy hẹn", "Không thể hủy lịch hẹn. Vui lòng thử lại.");
    }
  } catch (err) {
    console.error("Lỗi khi hủy lịch hẹn:", err);
    const msg =
      err?.response?.data?.message || err.message || "Lỗi khi hủy lịch hẹn";
    showErrorToast("Hủy hẹn", msg);
  }
};

const viewResult = (id) => {
  const appt = pastAppointments.value.find((a) => a.id === id);
  if (appt) {
    // Mock result data
    resultData.value = {
      date: appt.date,
      time: appt.time,
      serviceName: appt.service,
      doctorName: appt.doctor,
      doctorSpecialty: "Nội khoa thú y",
      diagnosis:
        "Viêm da dị ứng do bọ chét. Thú cưng có biểu hiện ngứa nhiều vùng lưng và đuôi, da mẩn đỏ.",
      medicines: [
        {
          name: "Apoquel 5.4mg",
          dosage: "1 viên/ngày",
          instruction: "Uống sau khi ăn sáng",
        },
        {
          name: "Bravecto",
          dosage: "1 viên duy nhất",
          instruction: "Nhai trực tiếp hoặc trộn với thức ăn",
        },
      ],
      postCareGuideline:
        "Vệ sinh môi trường sống sạch sẽ, giặt giũ đệm nằm của thú cưng. Tái khám sau 2 tuần nếu triệu chứng không giảm.",
      attachments: [
        {
          name: "Ket_qua_xet_nghiem_mau.pdf",
          label: "Kết quả xét nghiệm",
          type: "pdf",
          url: "#",
        },
        {
          name: "Hinh_anh_vung_da.jpg",
          label: "Hình ảnh lâm sàng",
          type: "image",
          url: "#",
        },
      ],
    };
    showResultPopup.value = true;
  }
};

const rebook = (id) => {
  const appt =
    pastAppointments.value.find((a) => a.id === id) ||
    upcomingAppointments.value.find((a) => a.id === id);
  if (appt) {
    rebookData.value = {
      petName: appt.pet,
      serviceName: appt.service,
    };
    showBookingPopup.value = true;
  }
};
</script>

<style scoped>
@import url("https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700;800&family=Inter:wght@700&display=swap");

.font-nunito {
  font-family: "Nunito Sans", sans-serif;
}
</style>
