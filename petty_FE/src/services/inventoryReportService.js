import axios from 'axios'
import { getToken } from '@/utils/auth'

const getAuthHeaders = () => {
  const token = getToken('admin') || getToken('staff')
  return { Authorization: `Bearer ${token}` }
}

export const getInventoryReport = async (params = {}) => {
  const { data } = await axios.get('/api/statistics/inventory', {
    params,
    headers: getAuthHeaders(),
  })
  return data
}

export const exportInventoryReport = async (params = {}) => {
  const response = await axios.get('/api/statistics/inventory', {
    params: { ...params, export: 'excel' },
    headers:      getAuthHeaders(),
    responseType: 'blob',
  })
  const url  = window.URL.createObjectURL(new Blob([response.data]))
  const link = document.createElement('a')
  link.href  = url
  link.download = `bao-cao-kho-${params.period ?? 'this_year'}.xlsx`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  window.URL.revokeObjectURL(url)
}