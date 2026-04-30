<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
    @click="$emit('close')"
  >
    <div
      class="w-full max-w-lg max-h-[90vh] bg-white rounded-xl shadow-2xl border !border-black/15 flex flex-col"
      @click.stop
    >
      <!-- Fixed Header -->
      <div class="flex-shrink-0 p-6 pb-4 border-b border-gray-200">
        <div class="relative flex items-center justify-between py-2">
          <h2 class="text-xl font-bold">Chi tiết lịch hẹn</h2>
          <button
            @click="$emit('close')"
            class="absolute right-0 p-1 hover:bg-gray-100 rounded-lg transition"
          >
            <svg class="w-7 h-7" viewBox="0 0 28 28" fill="none">
              <path
                d="M21 7L7 21M7 7l14 14"
                stroke="black"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              />
            </svg>
          </button>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-4 space-y-6">
        <!-- Dịch vụ -->
        <div
          class="bg-teal-50 rounded-xl p-4 flex justify-between items-center"
        >
          <div>
            <p class="text-gray-500 font-medium">Dịch vụ</p>
            <p class="font-medium text-black">{{ selectedAppt.service }}</p>
          </div>
          <div class="text-right">
            <p class="text-gray-500 font-medium">Giá dịch vụ</p>
            <p class="text-teal-600 font-medium">
              {{ getServicePrice(selectedAppt) }}
            </p>
          </div>
        </div>

        <!-- Thú cưng -->
        <section>
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="#12a594">
              <path
                d="M10 12a4 4 0 100-8 4 4 0 000 8zM10 14c-4.42 0-8 1.79-8 4v1h16v-1c0-2.21-3.58-4-8-4z"
              />
            </svg>
            <h3 class="font-semibold">Thông tin thú cưng</h3>
          </div>
          <div class="bg-white rounded-xl p-6">
            <div class="grid grid-cols-2 gap-x-12 gap-y-4">
              <div>
                <span class="text-gray-500 font-medium">Tên</span>
                <p
                  class="font-medium"
                  :class="
                    selectedAppt.pet === 'Milo'
                      ? 'text-amber-700'
                      : 'text-sky-500'
                  "
                >
                  {{ stripIsoTail(selectedAppt.pet) }}
                </p>
              </div>
              <div>
                <span class="text-gray-500 font-medium">Giống</span>
                <p class="font-medium">
                  {{ stripIsoTail(selectedAppt.breed) }}
                </p>
              </div>
              <div>
                <span class="text-gray-500 font-medium">Tuổi</span>
                <p class="font-medium">{{ formatAgeDate(selectedAppt.age) }}</p>
              </div>
              <div>
                <span class="text-gray-500 font-medium">Cân nặng</span>
                <p class="font-medium">{{ getWeight(selectedAppt) }}</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Ngày giờ khám -->
        <section>
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <rect
                x="3"
                y="4"
                width="14"
                height="14"
                rx="2"
                stroke="#12a594"
                stroke-width="2"
              />
              <path
                d="M7 2v4M13 2v4M3 8h14"
                stroke="#12a594"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
            <h3 class="font-semibold">Ngày giờ khám</h3>
          </div>
          <div
            class="bg-white rounded-xl p-4 grid grid-cols-3 gap-4 text-center"
          >
            <div>
              <span class="text-gray-500 font-medium block">Ngày</span>
              <p class="font-medium">{{ selectedAppt.date }}</p>
            </div>
            <div>
              <span class="text-gray-500 font-medium block">Giờ</span>
              <p class="font-medium">{{ selectedAppt.time }}</p>
            </div>
            <div>
              <span class="text-gray-500 font-medium block">Mã lịch hẹn</span>
              <p class="font-medium">{{ selectedAppt.id }}</p>
            </div>
          </div>
        </section>

        <!-- Địa điểm -->
        <section>
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <path
                d="M10 11a3 3 0 100-6 3 3 0 000 6z"
                stroke="#12a594"
                stroke-width="2"
              />
              <path
                d="M10 2a7 7 0 00-7 7c0 4.5 7 9 7 9s7-4.5 7-9a7 7 0 00-7-7z"
                stroke="#12a594"
                stroke-width="2"
              />
            </svg>
            <h3 class="font-semibold">Địa điểm</h3>
          </div>
          <div class="bg-white rounded-xl p-4">
            <p class="font-medium">{{ selectedAppt.clinic }}</p>
            <p class="text-gray-500 font-medium">{{ selectedAppt.address }}</p>
          </div>
        </section>

        <!-- Hướng dẫn -->
        <section v-if="selectedAppt.instruction">
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <circle cx="10" cy="10" r="8" stroke="#12a594" stroke-width="2" />
              <path
                d="M10 10v4M10 6h.01"
                stroke="#12a594"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
            <h3 class="font-semibold">Hướng dẫn</h3>
          </div>
          <div class="bg-blue-50 border !border-blue-200 rounded-xl p-4">
            <p class="font-bold text-blue-900 text-sm">Hướng dẫn:</p>
            <p class="font-medium text-blue-900">{{ selectedAppt.instruction }}</p>
          </div>
        </section>

        <!-- Trạng thái -->
        <section>
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <path
                d="M7 10l2 2 4-4m5 2a8 8 0 11-16 0 8 8 0 0116 0z"
                stroke="#12a594"
                stroke-width="2"
                stroke-linecap="round"
              />
            </svg>
            <h3 class="font-semibold">Trạng thái</h3>
          </div>
          <div
            :class="[
              'w-full rounded-lg px-5 py-2 text-center',
              statusClass(getRawStatus(selectedAppt)),
            ]"
          >
            <span class="font-medium text-lg">{{
              statusLabel(getRawStatus(selectedAppt))
            }}</span>
          </div>
        </section>

        <!-- THANH TOÁN - GIỐNG HỆT MẪU -->
        <section>
          <div class="flex items-center gap-3 mb-3">
            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="none">
              <rect
                x="2"
                y="5"
                width="16"
                height="11"
                rx="2"
                stroke="#12a594"
                stroke-width="2"
              />
              <path d="M2 9h16" stroke="#12a594" stroke-width="2" />
            </svg>
            <h3 class="font-semibold">Thanh toán</h3>
          </div>
          <div class="bg-white rounded-xl p-4 space-y-4">
            <div class="flex justify-between items-center">
              <div>
                <p class="text-gray-500 font-medium">Số tiền</p>
                <p class="font-medium text-lg">
                  {{ getServicePrice(selectedAppt) }}
                </p>
              </div>
              <div
                class="bg-red-50 border !border-black/5 rounded-lg px-3 py-1"
              >
                <span class="text-red-700 font-medium text-lg"
                  >Chưa thanh toán</span
                >
              </div>
            </div>
            <!-- <hr class="border-black/5" />
            <div>
              <p class="text-gray-500 font-medium mb-3">
                Phương thức thanh toán
              </p>
              <div class="grid grid-cols-2 gap-3">
                <button
                  class="bg-blue-600 text-white font-medium text-lg py-3 rounded-lg hover:opacity-90 transition"
                >
                  Thanh toán VNPay
                </button>
                <button
                  class="bg-red-600 text-white font-medium text-lg py-3 rounded-lg hover:opacity-90 transition"
                >
                  Thanh toán Banking
                </button>
              </div>
            </div> -->
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  selectedAppt: {
    type: Object,
    required: true,
  },
});

defineEmits(["close"]);

const getWeight = (appt) => {
  if (!appt) return "-";
  const raw = appt.raw || appt;
  const w =
    raw?.thuCung?.can_nang ??
    raw?.thuCung?.weight ??
    raw?.thu_cung?.can_nang ??
    raw?.thu_cung?.weight ??
    appt.weight;
  if (w == null || w === "") return "-";
  const s = String(w);
  return /\d/.test(s) && !/kg/i.test(s) ? `${s} kg` : s;
};

const stripIsoTail = (v) => {
  if (v == null) return v;
  if (typeof v !== "string") return v;
  return v
    .replace(/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d+)?Z?/g, "")
    .trim();
};

const formatAgeDate = (v) => {
  if (v == null) return "-";
  if (typeof v !== "string") return String(v);
  const m = v.match(/(\d{4}-\d{2}-\d{2})(?:T|\s)/);
  if (m) return m[1];
  const m2 = v.match(/^(\d{4}-\d{2}-\d{2})$/);
  if (m2) return m2[1];
  return stripIsoTail(v) || "-";
};

const formatVNCurrency = (val) => {
  if (val == null || val === "") return "-";
  if (typeof val === "string" && /\d+.*đ$/.test(val.trim())) return val;
  const n = Number(String(val).replace(/[^0-9.-]/g, ""));
  if (Number.isNaN(n)) return String(val);
  const parts = Math.round(n).toString().split("");
  let s = Math.round(n).toString();
  s = s.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
  return `${s}đ`;
};

const getServicePrice = (appt) => {
  if (!appt) return "-";
  const raw = appt.raw || appt || {};
  const priceCandidates = [
    raw?.dichVu?.gia_tien,
    raw?.dichVu?.price,
    raw?.dich_vu?.gia_tien,
    raw?.dich_vu?.price,
    raw?.gia_tien,
    raw?.gia,
    raw?.price,
    appt.service_price,
    appt.price,
  ];

  for (const p of priceCandidates) {
    if (p != null && p !== "") return formatVNCurrency(p);
  }
  return "-";
};

const getRawStatus = (appt) => {
  if (!appt) return null;
  const raw = appt.raw || appt || {};
  return (
    appt.status ||
    raw?.trang_thai ||
    raw?.status ||
    raw?.trangThai ||
    raw?.state ||
    null
  );
};

const statusLabel = (s) => {
  if (!s) return "Đang xử lý";
  const st = String(s).toLowerCase();
  switch (st) {
    case "pending":
    case "chuaxacnhan":
      return "Chờ xác nhận";
    case "confirmed":
    case "confirmed_by_staff":
    case "daxacnhan":
      return "Đã xác nhận";
    case "paid":
    case "dathanhtoan":
      return "Đã thanh toán";
    case "completed":
    case "hoanthanh":
      return "Hoàn thành";
    case "cancelled":
    case "canceled":
    case "dahuy":
      return "Đã hủy";
    default:
      return String(s);
  }
};

const statusClass = (s) => {
  const st = s ? String(s).toLowerCase() : "";
  if (st === "confirmed" || st === "confirmed_by_staff" || st === "daxacnhan")
    return "bg-teal-100 text-teal-700 border border-black/5";
  if (st === "pending" || st === "chuaxacnhan")
    return "bg-yellow-100 text-yellow-800 border border-black/5";
  if (
    st === "paid" ||
    st === "completed" ||
    st === "dathanhtoan" ||
    st === "hoanthanh"
  )
    return "bg-emerald-100 text-green-700 border border-black/5";
  if (st === "cancelled" || st === "canceled" || st === "dahuy")
    return "bg-red-100 text-red-700 border border-black/5";
  return "bg-gray-100 text-gray-700 border border-black/5";
};
</script>
