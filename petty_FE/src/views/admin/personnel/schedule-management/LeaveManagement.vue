<template>
  <div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-xl font-semibold text-black">Quản lý đơn xin nghỉ</h2>
        <p class="text-sm text-gray-500 mt-0.5">Duyệt hoặc từ chối lịch nghỉ của nhân viên</p>
      </div>

      <!-- Sinh lịch làm việc -->
      <div class="flex items-center gap-2">
        <select v-model="genMonth" class="h-9 border border-gray-300 rounded-lg px-2 text-sm focus:outline-none">
          <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
        </select>
        <input v-model.number="genYear" type="number" class="h-9 w-20 border border-gray-300 rounded-lg px-2 text-sm focus:outline-none" />
        <button @click="runGenerate" :disabled="generating" class="h-9 px-4 bg-[#009689] hover:bg-[#007a6e] text-white text-sm font-semibold rounded-lg disabled:opacity-50 transition-colors">
          {{ generating ? 'Đang sinh...' : 'Sinh lịch' }}
        </button>
      </div>
    </div>

    <p v-if="genMessage" class="text-sm" :class="genError ? 'text-red-600' : 'text-green-600'">{{ genMessage }}</p>

    <!-- Bộ lọc -->
    <div class="flex gap-3 flex-wrap bg-white border border-gray-200 rounded-xl p-4">
      <div class="flex flex-col gap-1">
        <label class="text-xs font-medium text-gray-600">Trạng thái</label>
        <select v-model="filters.trang_thai" @change="fetchLeaves" class="h-9 border border-gray-300 rounded-lg px-2 text-sm focus:outline-none">
          <option value="">Tất cả</option>
          <option value="cho_duyet">Chờ duyệt</option>
          <option value="da_duyet">Đã duyệt</option>
          <option value="tu_choi">Từ chối</option>
        </select>
      </div>
      <div class="flex flex-col gap-1">
        <label class="text-xs font-medium text-gray-600">Tháng</label>
        <select v-model="filters.thang" @change="fetchLeaves" class="h-9 border border-gray-300 rounded-lg px-2 text-sm focus:outline-none">
          <option value="">Tất cả</option>
          <option v-for="m in 12" :key="m" :value="m">Tháng {{ m }}</option>
        </select>
      </div>
    </div>

    <!-- Danh sách -->
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
      <div v-if="loading" class="text-center py-12 text-gray-500">Đang tải...</div>
      <div v-else-if="leaves.length === 0" class="text-center py-12 text-gray-500">Không có đơn xin nghỉ nào.</div>
      <table v-else class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Nhân viên</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Ngày nghỉ</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Lý do</th>
            <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Trạng thái</th>
            <th class="py-3 px-4"></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <tr v-for="item in leaves" :key="item.id" class="hover:bg-gray-50">
            <td class="py-3 px-4">
              <p class="font-medium text-black">{{ item.nhan_vien?.ho_ten }}</p>
              <p class="text-xs text-gray-500">{{ item.nhan_vien?.vai_tro }}</p>
            </td>
            <td class="py-3 px-4 text-gray-700">{{ formatDate(item.ngay_nghi) }}</td>
            <td class="py-3 px-4 text-gray-600 max-w-[200px] truncate">
              {{ item.ly_do || '—' }}
              <span v-if="item.ly_do_tu_choi" class="block text-xs text-red-500 mt-0.5">Từ chối: {{ item.ly_do_tu_choi }}</span>
            </td>
            <td class="py-3 px-4">
              <span class="text-xs font-semibold px-2 py-0.5 rounded"
                :class="{ 'bg-yellow-100 text-yellow-700': item.trang_thai==='cho_duyet', 'bg-green-100 text-green-700': item.trang_thai==='da_duyet', 'bg-red-100 text-red-700': item.trang_thai==='tu_choi' }">
                {{ item.trang_thai === 'cho_duyet' ? 'Chờ duyệt' : item.trang_thai === 'da_duyet' ? 'Đã duyệt' : 'Từ chối' }}
              </span>
            </td>
            <td class="py-3 px-4">
              <div v-if="item.trang_thai === 'cho_duyet'" class="flex gap-2">
                <button @click="approve(item.id)" class="text-xs font-semibold text-green-600 hover:text-green-800">Duyệt</button>
                <button @click="openReject(item)" class="text-xs font-semibold text-red-500 hover:text-red-700">Từ chối</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal từ chối -->
    <div v-if="rejectModal.open" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" @click.self="rejectModal.open = false">
      <div class="bg-white rounded-xl p-6 w-full max-w-sm space-y-4">
        <h3 class="text-base font-semibold text-black">Từ chối đơn xin nghỉ</h3>
        <p class="text-sm text-gray-600">
          Nhân viên: <strong>{{ rejectModal.item?.nhan_vien?.ho_ten }}</strong><br />
          Ngày nghỉ: <strong>{{ formatDate(rejectModal.item?.ngay_nghi) }}</strong>
        </p>
        <div class="space-y-1">
          <label class="text-xs font-medium text-gray-600">Lý do từ chối</label>
          <textarea v-model="rejectModal.ly_do" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#009689]" placeholder="Nhập lý do..." />
        </div>
        <p v-if="rejectModal.error" class="text-xs text-red-600">{{ rejectModal.error }}</p>
        <div class="flex gap-2 justify-end">
          <button @click="rejectModal.open = false" class="h-9 px-4 border border-gray-300 rounded-lg text-sm font-medium hover:bg-gray-50">Hủy</button>
          <button @click="submitReject" :disabled="rejectModal.submitting" class="h-9 px-4 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg disabled:opacity-50">
            {{ rejectModal.submitting ? 'Đang xử lý...' : 'Xác nhận từ chối' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getAdminLeaves, approveLeave, rejectLeave } from '@/services/lichNghiService'
import { generateSchedule } from '@/services/lichLamViecService'

const formatDate = (str) => {
  if (!str) return '—'
  const [y, m, d] = str.split('-')
  return `${d}/${m}/${y}`
}

const loading = ref(false)
const leaves  = ref([])
const filters = ref({ trang_thai: 'cho_duyet', thang: '' })

const _now     = new Date()
const genMonth = ref(_now.getMonth() === 11 ? 1 : _now.getMonth() + 2)
const genYear  = ref(_now.getMonth() === 11 ? _now.getFullYear() + 1 : _now.getFullYear())
const generating = ref(false)
const genMessage = ref('')
const genError   = ref(false)

const rejectModal = ref({ open: false, item: null, ly_do: '', submitting: false, error: '' })

const fetchLeaves = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.value.trang_thai) params.trang_thai = filters.value.trang_thai
    if (filters.value.thang)      params.thang      = filters.value.thang
    const res = await getAdminLeaves(params)
    leaves.value = res?.data || []
  } finally {
    loading.value = false
  }
}

const approve = async (id) => {
  try { await approveLeave(id); await fetchLeaves() }
  catch (err) { alert(err?.response?.data?.message || 'Lỗi khi duyệt') }
}

const openReject  = (item) => { rejectModal.value = { open: true, item, ly_do: '', submitting: false, error: '' } }

const submitReject = async () => {
  if (!rejectModal.value.ly_do.trim()) { rejectModal.value.error = 'Vui lòng nhập lý do từ chối'; return }
  rejectModal.value.submitting = true
  rejectModal.value.error      = ''
  try {
    await rejectLeave(rejectModal.value.item.id, rejectModal.value.ly_do)
    rejectModal.value.open = false
    await fetchLeaves()
  } catch (err) {
    rejectModal.value.error = err?.response?.data?.message || 'Lỗi khi từ chối'
  } finally {
    rejectModal.value.submitting = false
  }
}

const runGenerate = async () => {
  generating.value = true
  genMessage.value = ''
  genError.value   = false
  try {
    const res = await generateSchedule(genYear.value, genMonth.value, false)
    genMessage.value = res?.message || 'Sinh lịch thành công'
  } catch (err) {
    genError.value   = true
    const msg = err?.response?.data?.message
    if (msg?.includes('đã tồn tại')) {
      const retry = confirm(msg + '\n\nBạn có muốn tạo lại không?')
      if (retry) {
        try {
          const res = await generateSchedule(genYear.value, genMonth.value, true)
          genMessage.value = res?.message || 'Đã tạo lại lịch thành công'
          genError.value   = false
        } catch (e) {
          genMessage.value = e?.response?.data?.message || 'Lỗi khi tạo lịch'
        }
      }
    } else {
      genMessage.value = msg || 'Lỗi khi sinh lịch'
    }
  } finally {
    generating.value = false
  }
}

onMounted(fetchLeaves)
</script>
