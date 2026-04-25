<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <h2 class="text-xl font-semibold text-black">Lịch làm việc của tôi</h2>
      <div class="flex items-center gap-2">
        <button @click="changeMonth(-1)" class="h-9 px-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium">← Trước</button>
        <span class="text-sm font-semibold text-black min-w-[110px] text-center">Tháng {{ currentMonth }}/{{ currentYear }}</span>
        <button @click="changeMonth(1)" class="h-9 px-3 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium">Sau →</button>
      </div>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-500">Đang tải...</div>
    <div v-else class="bg-white border border-gray-200 rounded-xl overflow-hidden">
      <div class="grid grid-cols-7 border-b border-gray-200">
        <div v-for="d in ['T2','T3','T4','T5','T6','T7','CN']" :key="d" class="py-2 text-center text-xs font-semibold text-gray-500 uppercase">{{ d }}</div>
      </div>
      <div class="grid grid-cols-7">
        <div
          v-for="cell in calendarCells"
          :key="cell.key"
          class="min-h-[80px] border-b border-r border-gray-100 p-1.5"
          :class="{ 'bg-gray-50': !cell.currentMonth }"
        >
          <span
            class="text-xs font-medium mb-1 inline-flex items-center justify-center w-6 h-6 rounded-full"
            :class="cell.isToday ? 'bg-[#009689] text-white' : 'text-gray-700'"
          >{{ cell.day }}</span>
          <div v-if="cell.shift" class="mt-0.5">
            <span class="block text-[10px] font-semibold px-1.5 py-0.5 rounded text-white"
              :class="cell.shift === 'ca_sang' ? 'bg-[#009689]' : cell.shift === 'ca_toi' ? 'bg-slate-600' : 'bg-blue-500'">
              {{ cell.shift === 'ca_sang' ? 'Ca sáng' : cell.shift === 'ca_toi' ? 'Ca tối' : 'Ca chiều' }}
            </span>
          </div>
          <div v-if="cell.leave" class="mt-0.5">
            <span class="block text-[10px] font-semibold px-1.5 py-0.5 rounded"
              :class="{ 'bg-red-100 text-red-700': cell.leave==='da_duyet', 'bg-yellow-100 text-yellow-700': cell.leave==='cho_duyet', 'bg-gray-100 text-gray-500': cell.leave==='tu_choi' }">
              {{ cell.leave === 'da_duyet' ? 'Nghỉ phép' : cell.leave === 'cho_duyet' ? 'Chờ duyệt' : 'Bị từ chối' }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-4">
      <h3 class="text-base font-semibold text-black">Đăng ký lịch nghỉ</h3>
      <p class="text-xs text-gray-500">Cửa sổ đăng ký: ngày 15–25 hàng tháng, cho tháng kế tiếp.</p>
      <div class="flex gap-3 flex-wrap">
        <div class="flex flex-col gap-1">
          <label class="text-xs font-medium text-gray-600">Ngày nghỉ</label>
          <input v-model="leaveForm.ngay_nghi" type="date" class="h-9 border border-gray-300 rounded-lg px-3 text-sm focus:outline-none focus:border-[#009689]" />
        </div>
        <div class="flex flex-col gap-1 flex-1 min-w-[180px]">
          <label class="text-xs font-medium text-gray-600">Lý do (tuỳ chọn)</label>
          <input v-model="leaveForm.ly_do" type="text" placeholder="Nhập lý do..." class="h-9 border border-gray-300 rounded-lg px-3 text-sm focus:outline-none focus:border-[#009689]" />
        </div>
        <div class="flex items-end">
          <button @click="submitLeave" :disabled="submitting" class="h-9 px-4 bg-[#009689] hover:bg-[#007a6e] text-white text-sm font-semibold rounded-lg disabled:opacity-50 transition-colors">
            {{ submitting ? 'Đang gửi...' : 'Gửi đơn' }}
          </button>
        </div>
      </div>
      <p v-if="leaveError" class="text-xs text-red-600">{{ leaveError }}</p>
      <p v-if="leaveSuccess" class="text-xs text-green-600">{{ leaveSuccess }}</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl p-5 space-y-3">
      <h3 class="text-base font-semibold text-black">Đơn xin nghỉ của tôi</h3>
      <div v-if="leaves.length === 0" class="text-sm text-gray-500">Chưa có đơn xin nghỉ nào.</div>
      <div v-else class="space-y-2">
        <div v-for="item in leaves" :key="item.id" class="flex items-center justify-between p-3 border border-gray-100 rounded-lg">
          <div>
            <p class="text-sm font-medium text-black">{{ formatDate(item.ngay_nghi) }}</p>
            <p class="text-xs text-gray-500">{{ item.ly_do || 'Không có lý do' }}</p>
            <p v-if="item.ly_do_tu_choi" class="text-xs text-red-500 mt-0.5">Lý do từ chối: {{ item.ly_do_tu_choi }}</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-2 py-0.5 rounded"
              :class="{ 'bg-yellow-100 text-yellow-700': item.trang_thai==='cho_duyet', 'bg-green-100 text-green-700': item.trang_thai==='da_duyet', 'bg-red-100 text-red-700': item.trang_thai==='tu_choi' }">
              {{ item.trang_thai === 'cho_duyet' ? 'Chờ duyệt' : item.trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Từ chối' }}
            </span>
            <button v-if="item.trang_thai === 'cho_duyet'" @click="cancelLeave(item.id)" class="text-xs text-red-500 hover:text-red-700 underline">Hủy</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getMySchedule } from '@/services/lichLamViecService'
import { getMyLeaves, requestLeave, cancelLeave as cancelLeaveApi } from '@/services/lichNghiService'

const pad       = (n) => String(n).padStart(2, '0')
const toYMD     = (y, m, d) => `${y}-${pad(m)}-${pad(d)}`
const daysInMonth = (y, m) => new Date(y, m, 0).getDate()
const addMonths = (y, m, delta) => {
  const d = new Date(y, m - 1 + delta, 1)
  return { year: d.getFullYear(), month: d.getMonth() + 1 }
}
const formatDate = (str) => {
  if (!str) return ''
  const [y, m, d] = str.split('-')
  return `${d}/${m}/${y}`
}

const now = new Date()
const currentYear  = ref(now.getFullYear())
const currentMonth = ref(now.getMonth() + 1)
const loading      = ref(false)
const scheduleMap  = ref({})
const leaves       = ref([])
const leaveMap     = ref({})
const leaveForm    = ref({ ngay_nghi: '', ly_do: '' })
const leaveError   = ref('')
const leaveSuccess = ref('')
const submitting   = ref(false)

const changeMonth = (delta) => {
  const next = addMonths(currentYear.value, currentMonth.value, delta)
  currentYear.value  = next.year
  currentMonth.value = next.month
  fetchData()
}

const fetchData = async () => {
  loading.value = true
  try {
    const days  = daysInMonth(currentYear.value, currentMonth.value)
    const start = toYMD(currentYear.value, currentMonth.value, 1)
    const end   = toYMD(currentYear.value, currentMonth.value, days)
    const [scheduleRes, leavesRes] = await Promise.all([getMySchedule(start, end), getMyLeaves()])
    scheduleMap.value = {}
    if (scheduleRes?.data?.schedule) {
      scheduleRes.data.schedule.forEach((day) => {
        if (day.lich_lam_viec?.length > 0) scheduleMap.value[day.date] = day.lich_lam_viec[0].thoi_gian_truc
      })
    }
    leaves.value   = leavesRes?.data || []
    leaveMap.value = {}
    leaves.value.forEach((l) => { leaveMap.value[l.ngay_nghi] = l.trang_thai })
  } finally {
    loading.value = false
  }
}

const calendarCells = computed(() => {
  const y    = currentYear.value
  const m    = currentMonth.value
  const days = daysInMonth(y, m)
  const todayStr = toYMD(now.getFullYear(), now.getMonth() + 1, now.getDate())
  const firstDow = new Date(y, m - 1, 1).getDay()
  let offset = firstDow === 0 ? 6 : firstDow - 1
  const cells = []
  const prevDays = daysInMonth(y, m - 1 || 12)
  for (let i = offset - 1; i >= 0; i--)
    cells.push({ key: `prev-${prevDays - i}`, day: prevDays - i, currentMonth: false, isToday: false, shift: null, leave: null })
  for (let d = 1; d <= days; d++) {
    const dateStr = toYMD(y, m, d)
    cells.push({ key: dateStr, day: d, currentMonth: true, isToday: dateStr === todayStr, shift: scheduleMap.value[dateStr] || null, leave: leaveMap.value[dateStr] || null })
  }
  const rem = cells.length % 7
  if (rem !== 0) for (let d = 1; d <= 7 - rem; d++)
    cells.push({ key: `next-${d}`, day: d, currentMonth: false, isToday: false, shift: null, leave: null })
  return cells
})

const submitLeave = async () => {
  leaveError.value   = ''
  leaveSuccess.value = ''
  if (!leaveForm.value.ngay_nghi) { leaveError.value = 'Vui lòng chọn ngày nghỉ'; return }
  submitting.value = true
  try {
    await requestLeave({ ngay_nghi: leaveForm.value.ngay_nghi, ly_do: leaveForm.value.ly_do })
    leaveSuccess.value = 'Đã gửi đơn xin nghỉ thành công!'
    leaveForm.value    = { ngay_nghi: '', ly_do: '' }
    await fetchData()
  } catch (err) {
    const errors = err?.response?.data?.errors
    leaveError.value = errors?.ngay_nghi?.[0] ?? (err?.response?.data?.message || 'Có lỗi xảy ra')
  } finally {
    submitting.value = false
  }
}

const cancelLeave = async (id) => {
  try { await cancelLeaveApi(id); await fetchData() }
  catch (err) { alert(err?.response?.data?.message || 'Không thể hủy đơn') }
}

onMounted(fetchData)
</script>
