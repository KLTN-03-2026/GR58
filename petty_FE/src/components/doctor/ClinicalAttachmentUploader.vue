<template>
  <div class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-[25px]">
    <!-- Header -->
    <div class="flex items-center justify-between mb-5">
      <h3 class="font-normal text-base leading-4 text-gray-900 tracking-[-0.3125px]">
        Đính kèm kết quả cận lâm sàng
      </h3>
      <!-- Upload trigger -->
      <label
        :class="[
          'cursor-pointer h-9 px-4 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors',
          uploading
            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
            : 'bg-[#155dfc] text-white hover:bg-[#1447e6]',
        ]"
      >
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <polyline points="17 8 12 3 7 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="12" y1="3" x2="12" y2="15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        {{ uploading ? `Đang tải... ${progress}%` : 'Tải lên file' }}
        <input
          v-if="!uploading"
          type="file"
          class="hidden"
          accept="image/jpeg,image/png,application/pdf"
          @change="handleFileSelect"
        />
      </label>
    </div>

    <!-- Progress bar -->
    <div v-if="uploading" class="mb-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
      <div
        class="h-full bg-[#155dfc] rounded-full transition-all duration-300"
        :style="{ width: `${progress}%` }"
      />
    </div>

    <!-- Loading state -->
    <div v-if="loadingList" class="py-6 text-center text-sm text-gray-400">
      Đang tải danh sách đính kèm...
    </div>

    <!-- Empty state -->
    <div
      v-else-if="attachments.length === 0"
      class="py-8 text-center border-2 border-dashed !border-gray-200 rounded-xl text-gray-400 text-sm"
    >
      Chưa có file đính kèm nào. Tải lên JPEG, PNG hoặc PDF (tối đa 10 MB).
    </div>

    <!-- Attachment list -->
    <div v-else class="flex flex-col gap-3">
      <!-- Image thumbnails grid -->
      <div
        v-if="imageAttachments.length"
        class="grid grid-cols-4 gap-3"
      >
        <div
          v-for="item in imageAttachments"
          :key="item.id"
          class="relative group rounded-xl overflow-hidden border !border-gray-200 aspect-square cursor-pointer"
          @click="openLightbox(item)"
        >
          <img
            :src="item.url"
            :alt="item.ten_file"
            class="w-full h-full object-cover"
          />
          <!-- Overlay on hover -->
          <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
            <button
              @click.stop="confirmDelete(item)"
              class="bg-white/90 hover:bg-red-50 text-red-600 rounded-lg px-2 py-1 text-xs font-medium"
            >
              Xóa
            </button>
          </div>
        </div>
      </div>

      <!-- PDF card list -->
      <div
        v-for="item in pdfAttachments"
        :key="item.id"
        class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border !border-gray-200"
      >
        <!-- PDF icon -->
        <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
          <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="none">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <polyline points="14 2 14 8 20 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-800 truncate">{{ item.ten_file }}</p>
          <p class="text-xs text-gray-400">{{ formatSize(item.kich_thuoc) }}</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <button
            @click="openPdf(item.url)"
            class="h-8 px-3 bg-white border !border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-50"
          >
            Xem
          </button>
          <button
            @click="confirmDelete(item)"
            class="h-8 px-3 bg-white border !border-red-200 rounded-lg text-xs font-medium text-red-600 hover:bg-red-50"
          >
            Xóa
          </button>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <div
      v-if="lightboxItem"
      class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center"
      @click.self="lightboxItem = null"
    >
      <button
        class="absolute top-4 right-4 w-10 h-10 bg-white/10 hover:bg-white/20 rounded-full text-white flex items-center justify-center"
        @click="lightboxItem = null"
      >
        ✕
      </button>
      <img
        :src="lightboxItem.url"
        :alt="lightboxItem.ten_file"
        class="max-w-[90vw] max-h-[90vh] object-contain rounded-lg shadow-2xl"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { showSuccessToast, showErrorToast } from '@/utils/toast'
import * as dinhKemService from '@/services/dinhKemPhieuKhamService'

const props = defineProps({
  phieuKhamId: {
    type: [Number, String],
    required: true,
  },
})

const attachments = ref([])
const uploading = ref(false)
const loadingList = ref(false)
const progress = ref(0)
const lightboxItem = ref(null)

const imageAttachments = computed(() =>
  attachments.value.filter((a) =>
    ['image/jpeg', 'image/png'].includes(a.loai_mime)
  )
)

const pdfAttachments = computed(() =>
  attachments.value.filter((a) => a.loai_mime === 'application/pdf')
)

const formatSize = (bytes) => {
  if (!bytes) return ''
  if (bytes >= 1024 * 1024) return `${(bytes / 1024 / 1024).toFixed(1)} MB`
  return `${(bytes / 1024).toFixed(0)} KB`
}

const openLightbox = (item) => {
  lightboxItem.value = item
}

const openPdf = (url) => {
  window.open(url, '_blank')
}

// Validate file on FE before sending
const validate = (file) => {
  const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf']
  if (!allowedTypes.includes(file.type)) {
    showErrorToast('Chỉ chấp nhận file JPEG, PNG hoặc PDF.')
    return false
  }
  if (file.size > 10 * 1024 * 1024) {
    showErrorToast('File không được vượt quá 10 MB.')
    return false
  }
  return true
}

const handleFileSelect = async (event) => {
  const file = event.target.files?.[0]
  if (!file) return
  // Reset input so same file can be re-selected
  event.target.value = ''

  if (!validate(file)) return

  uploading.value = true
  progress.value = 0

  try {
    const res = await dinhKemService.upload(props.phieuKhamId, file, (pct) => {
      progress.value = pct
    })

    const newItem = res.data?.data
    if (newItem) {
      attachments.value.unshift(newItem)
    }
    showSuccessToast('Upload file thành công!')
  } catch (err) {
    showErrorToast(err.response?.data?.message || 'Lỗi khi upload file.')
  } finally {
    uploading.value = false
    progress.value = 0
  }
}

const confirmDelete = async (item) => {
  if (!confirm(`Xóa file "${item.ten_file}"?`)) return

  try {
    await dinhKemService.remove(props.phieuKhamId, item.id)
    attachments.value = attachments.value.filter((a) => a.id !== item.id)
    showSuccessToast('Đã xóa file.')
  } catch (err) {
    showErrorToast(err.response?.data?.message || 'Lỗi khi xóa file.')
  }
}

const fetchAttachments = async () => {
  if (!props.phieuKhamId) return
  loadingList.value = true
  try {
    const res = await dinhKemService.list(props.phieuKhamId)
    attachments.value = res.data?.data ?? []
  } catch {
    // Silently fail — phiếu khám mới chưa có đính kèm
  } finally {
    loadingList.value = false
  }
}

onMounted(fetchAttachments)
</script>
