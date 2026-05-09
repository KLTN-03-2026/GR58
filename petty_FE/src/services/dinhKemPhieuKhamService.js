import api from '@/utils/api'

/**
 * Lấy danh sách đính kèm của phiếu khám.
 * @param {number|string} phieuKhamId
 */
export const list = (phieuKhamId) =>
  api.get(`/phieu-kham/${phieuKhamId}/dinh-kem`)

/**
 * Upload file đính kèm.
 * @param {number|string} phieuKhamId
 * @param {File} file
 * @param {function} onProgress - callback(percentComplete: number)
 */
export const upload = (phieuKhamId, file, onProgress) => {
  const formData = new FormData()
  formData.append('file', file)

  return api.post(`/phieu-kham/${phieuKhamId}/dinh-kem`, formData, {
    headers: { 'Content-Type': 'multipart/form-data' },
    onUploadProgress: (event) => {
      if (onProgress && event.total) {
        onProgress(Math.round((event.loaded * 100) / event.total))
      }
    },
  })
}

/**
 * Xóa file đính kèm.
 * @param {number|string} phieuKhamId
 * @param {number|string} dinhKemId
 */
export const remove = (phieuKhamId, dinhKemId) =>
  api.delete(`/phieu-kham/${phieuKhamId}/dinh-kem/${dinhKemId}`)

export default { list, upload, remove }
