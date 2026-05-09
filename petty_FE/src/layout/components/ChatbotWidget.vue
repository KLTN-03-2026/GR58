<template>
  <div v-if="isVisible" class="petty-chatbot-root">
    <!-- FAB Button -->
    <Transition name="fab-pop">
      <button
        v-if="!isOpen"
        class="petty-chatbot-fab"
        type="button"
        @click="openPanel"
        aria-label="Mở trợ lý AI Petty"
      >
        <div class="petty-fab-inner">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
            <path
              d="M12 2C6.48 2 2 5.58 2 10c0 2.24 1.12 4.27 2.94 5.74-.16 1.8-.94 3.34-2.02 4.46a.5.5 0 00.36.84c2.1-.06 4.04-.88 5.48-2.12.72.14 1.48.08 2.24.08 5.52 0 10-3.58 10-8s-4.48-8-10-8z"
              fill="currentColor"
            />
          </svg>
          <span class="petty-fab-pulse"></span>
        </div>
      </button>
    </Transition>

    <!-- Chat Panel -->
    <Transition name="panel-slide">
      <div v-if="isOpen" class="petty-chatbot-panel">
        <!-- Header -->
        <div class="petty-chatbot-header">
          <div class="petty-header-left">
            <div class="petty-avatar">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M12 2C6.48 2 2 5.58 2 10c0 2.24 1.12 4.27 2.94 5.74-.16 1.8-.94 3.34-2.02 4.46a.5.5 0 00.36.84c2.1-.06 4.04-.88 5.48-2.12.72.14 1.48.08 2.24.08 5.52 0 10-3.58 10-8s-4.48-8-10-8z" fill="currentColor"/>
              </svg>
            </div>
            <div class="petty-header-info">
              <span class="petty-header-title">Petty AI</span>
              <span class="petty-header-status">Trợ lý thú cưng</span>
            </div>
          </div>
          <button type="button" class="petty-close-btn" @click="closePanel" aria-label="Đóng">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Messages -->
        <div class="petty-chatbot-messages" ref="messagesEl">
          <div v-for="(msg, idx) in messages" :key="idx" :class="['petty-msg', msg.role]">
            <div v-if="msg.content" class="petty-msg-bubble">
              <div class="petty-msg-text" v-html="formatMessage(msg.content)"></div>
            </div>

            <div v-if="msg.images && msg.images.length" class="petty-msg-images">
              <img v-for="(img, imageIndex) in msg.images" :key="imageIndex" :src="img.data" :alt="img.name" />
            </div>

            <!-- Action Cards -->
            <div v-if="msg.actions && msg.actions.length" class="petty-msg-actions">
              <div
                v-for="(action, aIdx) in msg.actions"
                :key="aIdx"
                class="petty-action-card"
              >
                <template v-if="action.type === 'appointment_booked'">
                  <div class="petty-action-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M9 12l2 2 4-4"/>
                      <circle cx="12" cy="12" r="10"/>
                    </svg>
                  </div>
                  <div class="petty-action-content">
                    <div class="petty-action-title">Đặt lịch thành công</div>
                    <div class="petty-action-detail">{{ action.data.thu_cung }} · {{ action.data.dich_vu }}</div>
                    <div class="petty-action-time">{{ action.data.ngay_gio }}</div>
                  </div>
                </template>
              </div>
            </div>
          </div>

          <!-- Typing indicator -->
          <div v-if="loading" class="petty-msg assistant">
            <div class="petty-msg-bubble">
              <div class="petty-typing-dots">
                <span></span><span></span><span></span>
              </div>
            </div>
          </div>
        </div>

        <!-- Image Preview -->
        <div v-if="attachedImages.length" class="petty-chatbot-preview">
          <div v-for="(img, index) in attachedImages" :key="`${img.name}-${index}`" class="petty-preview-item">
            <img :src="img.data" :alt="img.name" />
            <button type="button" @click="removeImage(index)" aria-label="Xóa ảnh">
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                <path d="M18 6L6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <!-- Input Area -->
        <div class="petty-chatbot-input">
          <button class="petty-btn-attach" type="button" @click="triggerAttach" :disabled="loading" aria-label="Đính kèm ảnh">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
            </svg>
          </button>

          <input ref="imageInput" type="file" accept="image/*" multiple hidden @change="onImageSelect" />

          <div class="petty-input-wrap">
            <textarea
              v-model="input"
              rows="1"
              placeholder="Hỏi về thú cưng của bạn..."
              @keydown.enter.exact.prevent="sendMessage"
              @input="autoResize"
              ref="textareaEl"
            ></textarea>
          </div>

          <button
            class="petty-btn-send"
            type="button"
            :disabled="loading || (!input.trim() && !attachedImages.length)"
            @click="sendMessage"
            aria-label="Gửi"
          >
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
            </svg>
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { getToken } from '@/utils/auth'

const API_BASE = import.meta.env.VITE_API_BASE || 'http://127.0.0.1:8000/api'

const route = useRoute()
const isOpen = ref(false)
const loading = ref(false)
const input = ref('')
const messagesEl = ref(null)
const imageInput = ref(null)
const textareaEl = ref(null)
const attachedImages = ref([])
const history = ref([])
const messages = ref([
  {
    role: 'assistant',
    content: 'Xin chào! Mình là trợ lý AI của phòng khám Petty 🐾\nBạn cần tư vấn gì về thú cưng?',
    images: [],
    actions: [],
  },
])

const hiddenPaths = ['/customer/login', '/customer/register', '/admin', '/doctor', '/nurse', '/staff']

const isVisible = computed(() => {
  const path = route?.path || ''
  if (hiddenPaths.some(p => path.startsWith(p))) {
    return false
  }
  return path === '/' || path.startsWith('/customer') || path.startsWith('/services') || path.startsWith('/forum')
})

function openPanel() {
  isOpen.value = true
  nextTick(() => textareaEl.value?.focus())
}

function closePanel() {
  isOpen.value = false
}

function triggerAttach() {
  imageInput.value?.click()
}

function autoResize() {
  const el = textareaEl.value
  if (el) {
    el.style.height = 'auto'
    el.style.height = Math.min(el.scrollHeight, 80) + 'px'
  }
}

async function fileToDataUrl(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader()
    reader.onload = () => resolve(reader.result)
    reader.onerror = reject
    reader.readAsDataURL(file)
  })
}

async function onImageSelect(event) {
  const files = Array.from(event.target.files || [])
  for (const file of files) {
    if (!file.type.startsWith('image/')) continue
    const data = await fileToDataUrl(file)
    attachedImages.value.push({ name: file.name, data, type: file.type })
  }
  event.target.value = ''
}

function removeImage(index) {
  attachedImages.value.splice(index, 1)
}

function formatMessage(content) {
  if (!content) return ''

  const lines = content.split('\n')
  const result = []
  let i = 0

  while (i < lines.length) {
    if (isTableRow(lines[i]) && i + 1 < lines.length && isTableSeparator(lines[i + 1])) {
      const tableLines = []
      while (i < lines.length && (isTableRow(lines[i]) || isTableSeparator(lines[i]))) {
        if (!isTableSeparator(lines[i])) {
          tableLines.push(lines[i])
        }
        i++
      }
      result.push(buildTable(tableLines))
    } else {
      result.push(escapeAndFormat(lines[i]))
      i++
    }
  }

  return result.join('')
}

function isTableRow(line) {
  return line && line.trim().startsWith('|') && line.trim().endsWith('|')
}

function isTableSeparator(line) {
  return line && /^\|[\s\-:|]+\|$/.test(line.trim())
}

function buildTable(rows) {
  if (rows.length === 0) return ''
  const html = ['<div class="petty-table-wrap"><table>']

  rows.forEach((row, idx) => {
    const cells = row.split('|').slice(1, -1).map(c => c.trim())
    const tag = idx === 0 ? 'th' : 'td'
    html.push('<tr>')
    cells.forEach(cell => {
      html.push(`<${tag}>${escapeHtml(cell)}</${tag}>`)
    })
    html.push('</tr>')
  })

  html.push('</table></div>')
  return html.join('')
}

function escapeHtml(text) {
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
}

function escapeAndFormat(line) {
  const escaped = escapeHtml(line)
  return escaped + '<br>'
}

async function sendMessage() {
  const message = input.value.trim()
  const imagesSnapshot = attachedImages.value.map((img) => ({ ...img }))

  if ((!message && !imagesSnapshot.length) || loading.value) return

  messages.value.push({ role: 'user', content: message, images: imagesSnapshot, actions: [] })
  history.value.push({
    role: 'user',
    content: message || 'Người dùng đã gửi hình ảnh thú cưng.',
  })

  input.value = ''
  attachedImages.value = []
  loading.value = true
  if (textareaEl.value) textareaEl.value.style.height = 'auto'
  scrollToBottom()

  try {
    const headers = { 'Content-Type': 'application/json' }
    const token = getToken('customer')
    if (token) {
      headers['Authorization'] = `Bearer ${token}`
    }

    const response = await fetch(`${API_BASE}/chatbot/message`, {
      method: 'POST',
      headers,
      body: JSON.stringify({
        message,
        images: imagesSnapshot.map((img) => img.data),
        history: history.value.slice(-10),
      }),
    })

    const data = await response.json()
    if (!response.ok || !data?.status) {
      throw new Error(data?.message || `Lỗi kết nối (${response.status})`)
    }

    const reply = data.reply || 'Không có nội dung phản hồi.'
    const actions = data.actions || []

    messages.value.push({ role: 'assistant', content: reply, images: [], actions })
    history.value.push({ role: 'assistant', content: reply })
  } catch (error) {
    messages.value.push({
      role: 'assistant',
      content: `Xin lỗi, đã xảy ra lỗi. Vui lòng thử lại sau.`,
      images: [],
      actions: [],
    })
  } finally {
    loading.value = false
    scrollToBottom()
  }
}

function scrollToBottom() {
  nextTick(() => {
    const el = messagesEl.value
    if (el) el.scrollTop = el.scrollHeight
  })
}
</script>

<style scoped>
/* ─── FAB Button ─── */
.petty-chatbot-fab {
  position: fixed;
  right: 24px;
  bottom: 24px;
  width: 56px;
  height: 56px;
  border-radius: 16px;
  border: none;
  background: linear-gradient(135deg, #5a9690 0%, #009689 100%);
  color: #fff;
  box-shadow:
    0 4px 14px rgba(0, 150, 137, 0.3),
    0 1px 3px rgba(0, 0, 0, 0.08);
  z-index: 9998;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s;
}

.petty-chatbot-fab:hover {
  transform: scale(1.06);
  box-shadow:
    0 8px 24px rgba(0, 150, 137, 0.4),
    0 2px 6px rgba(0, 0, 0, 0.1);
}

.petty-chatbot-fab:active {
  transform: scale(0.96);
}

.petty-fab-inner {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.petty-fab-pulse {
  position: absolute;
  inset: -4px;
  border-radius: 18px;
  background: rgba(0, 150, 137, 0.25);
  animation: fab-pulse 2.5s ease-in-out infinite;
}

@keyframes fab-pulse {
  0%, 100% { transform: scale(1); opacity: 0.6; }
  50% { transform: scale(1.35); opacity: 0; }
}

/* ─── Panel ─── */
.petty-chatbot-panel {
  position: fixed;
  right: 24px;
  bottom: 24px;
  width: 440px;
  max-width: calc(100vw - 32px);
  height: 680px;
  max-height: calc(100vh - 48px);
  background: #ffffff;
  border-radius: 20px;
  box-shadow:
    0 24px 48px rgba(0, 0, 0, 0.12),
    0 4px 12px rgba(0, 0, 0, 0.06),
    0 0 0 1px rgba(90, 150, 144, 0.08);
  z-index: 9999;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

/* ─── Header ─── */
.petty-chatbot-header {
  background: linear-gradient(135deg, #5a9690 0%, #4a8580 100%);
  color: #fff;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.petty-header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.petty-avatar {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
}

.petty-header-info {
  display: flex;
  flex-direction: column;
}

.petty-header-title {
  font-family: 'Montserrat Alternates', 'Nunito Sans', sans-serif;
  font-weight: 700;
  font-size: 15px;
  letter-spacing: -0.2px;
}

.petty-header-status {
  font-size: 11px;
  opacity: 0.8;
  font-weight: 400;
}

.petty-close-btn {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: none;
  background: rgba(255, 255, 255, 0.15);
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}

.petty-close-btn:hover {
  background: rgba(255, 255, 255, 0.25);
}

/* ─── Messages Area ─── */
.petty-chatbot-messages {
  flex: 1;
  overflow-y: auto;
  padding: 16px 14px;
  background: #f8fafa;
  scroll-behavior: smooth;
}

.petty-chatbot-messages::-webkit-scrollbar {
  width: 4px;
}
.petty-chatbot-messages::-webkit-scrollbar-thumb {
  background: #c8d8d6;
  border-radius: 4px;
}

.petty-msg {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
}

.petty-msg.user {
  align-items: flex-end;
}

.petty-msg.assistant {
  align-items: flex-start;
}

.petty-msg-bubble {
  max-width: 85%;
  padding: 10px 14px;
  border-radius: 14px;
  font-size: 13.5px;
  line-height: 1.55;
  font-family: 'Nunito Sans', sans-serif;
}

.petty-msg.user .petty-msg-bubble {
  background: #5a9690;
  color: #fff;
  border-bottom-right-radius: 4px;
}

.petty-msg.assistant .petty-msg-bubble {
  background: #ffffff;
  color: #393e46;
  border: 1px solid #e8eded;
  border-bottom-left-radius: 4px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.petty-msg-text {
  word-break: break-word;
}

.petty-msg-text :deep(strong) {
  font-weight: 600;
}

/* ─── Markdown Tables ─── */
.petty-msg-text :deep(.petty-table-wrap) {
  margin: 8px 0 4px;
  overflow-x: auto;
  border-radius: 8px;
  border: 1px solid #e0e8e7;
}

.petty-msg-text :deep(table) {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
}

.petty-msg-text :deep(th) {
  background: #f0f7f6;
  font-weight: 600;
  color: #2f5755;
  padding: 7px 10px;
  text-align: left;
  border-bottom: 1px solid #d4e4e2;
}

.petty-msg-text :deep(td) {
  padding: 6px 10px;
  border-bottom: 1px solid #eef2f1;
  color: #393e46;
}

.petty-msg-text :deep(tr:last-child td) {
  border-bottom: none;
}

.petty-msg-text :deep(tr:hover td) {
  background: #f8fcfb;
}

/* ─── Typing Indicator ─── */
.petty-typing-dots {
  display: flex;
  gap: 4px;
  padding: 4px 0;
}

.petty-typing-dots span {
  width: 7px;
  height: 7px;
  background: #a0bfbc;
  border-radius: 50%;
  animation: typing-bounce 1.2s ease-in-out infinite;
}

.petty-typing-dots span:nth-child(2) { animation-delay: 0.15s; }
.petty-typing-dots span:nth-child(3) { animation-delay: 0.3s; }

@keyframes typing-bounce {
  0%, 60%, 100% { transform: translateY(0); }
  30% { transform: translateY(-4px); }
}

/* ─── Images ─── */
.petty-msg-images {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 8px;
  max-width: 85%;
}

.petty-msg-images img {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #e0e8e7;
}

/* ─── Action Cards ─── */
.petty-msg-actions {
  margin-top: 8px;
  max-width: 85%;
}

.petty-action-card {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px 14px;
  border-radius: 12px;
  background: linear-gradient(135deg, #f0faf9 0%, #e8f6f4 100%);
  border: 1px solid #c8e6e3;
}

.petty-action-icon {
  width: 34px;
  height: 34px;
  min-width: 34px;
  border-radius: 50%;
  background: #009689;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.petty-action-content {
  flex: 1;
  min-width: 0;
}

.petty-action-title {
  font-size: 12.5px;
  font-weight: 700;
  color: #009689;
  margin-bottom: 2px;
}

.petty-action-detail {
  font-size: 12.5px;
  color: #393e46;
}

.petty-action-time {
  font-size: 11.5px;
  color: #6b7c7a;
  margin-top: 2px;
}

/* ─── Image Preview ─── */
.petty-chatbot-preview {
  border-top: 1px solid #eef2f1;
  padding: 8px 12px;
  display: flex;
  gap: 8px;
  overflow-x: auto;
  background: #fff;
}

.petty-preview-item {
  position: relative;
  width: 48px;
  height: 48px;
  flex-shrink: 0;
}

.petty-preview-item img {
  width: 100%;
  height: 100%;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid #e0e8e7;
}

.petty-preview-item button {
  position: absolute;
  top: -5px;
  right: -5px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: none;
  background: #ef4444;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: transform 0.15s;
}

.petty-preview-item button:hover {
  transform: scale(1.15);
}

/* ─── Input Area ─── */
.petty-chatbot-input {
  border-top: 1px solid #eef2f1;
  padding: 10px 12px;
  display: flex;
  gap: 8px;
  background: #fff;
  align-items: flex-end;
}

.petty-btn-attach {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 10px;
  border: 1px solid #e0e8e7;
  background: #fff;
  color: #6b7c7a;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.petty-btn-attach:hover:not(:disabled) {
  border-color: #5a9690;
  color: #5a9690;
  background: #f0faf9;
}

.petty-btn-attach:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.petty-input-wrap {
  flex: 1;
  min-width: 0;
}

.petty-input-wrap textarea {
  width: 100%;
  resize: none;
  border: 1px solid #e0e8e7;
  border-radius: 12px;
  padding: 8px 12px;
  outline: none;
  font-size: 13.5px;
  font-family: 'Nunito Sans', sans-serif;
  line-height: 1.4;
  max-height: 80px;
  overflow-y: auto;
  transition: border-color 0.15s;
  background: #f8fafa;
}

.petty-input-wrap textarea:focus {
  border-color: #5a9690;
  background: #fff;
}

.petty-input-wrap textarea::placeholder {
  color: #a0b0ae;
}

.petty-btn-send {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 10px;
  border: none;
  background: #009689;
  color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s;
}

.petty-btn-send:hover:not(:disabled) {
  background: #008177;
  transform: scale(1.04);
}

.petty-btn-send:disabled {
  background: #c8d8d6;
  cursor: not-allowed;
  transform: none;
}

/* ─── Transitions ─── */
.fab-pop-enter-active {
  animation: pop-in 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.fab-pop-leave-active {
  animation: pop-in 0.2s ease reverse;
}

@keyframes pop-in {
  from { transform: scale(0); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}

.panel-slide-enter-active {
  animation: slide-up 0.35s cubic-bezier(0.22, 1, 0.36, 1);
}
.panel-slide-leave-active {
  animation: slide-up 0.2s ease reverse;
}

@keyframes slide-up {
  from {
    transform: translateY(20px) scale(0.95);
    opacity: 0;
  }
  to {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}

/* ─── Responsive ─── */
@media (max-width: 480px) {
  .petty-chatbot-panel {
    right: 8px;
    bottom: 8px;
    width: calc(100vw - 16px);
    height: calc(100vh - 16px);
    border-radius: 16px;
  }

  .petty-chatbot-fab {
    right: 16px;
    bottom: 16px;
  }
}
</style>
