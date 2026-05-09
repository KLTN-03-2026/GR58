<template>
  <div v-if="isHomePage" class="petty-chatbot-root">
    <button
      v-if="!isOpen"
      class="petty-chatbot-fab"
      type="button"
      @click="isOpen = true"
      aria-label="Mở chatbot"
    >
      <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M8 10H16M8 14H13M21 12C21 16.4183 16.9706 20 12 20C10.6782 20 9.42295 19.7469 8.30273 19.2918L4 20L4.70916 16.1695C4.25244 14.9574 4 13.5153 4 12C4 7.58172 8.02944 4 13 4C17.9706 4 21 7.58172 21 12Z"
          stroke="currentColor"
          stroke-width="1.8"
          stroke-linecap="round"
          stroke-linejoin="round"
        />
      </svg>
    </button>

    <div v-if="isOpen" class="petty-chatbot-panel">
      <div class="petty-chatbot-header">
        <strong>Petty AI</strong>
        <button type="button" @click="closePanel">×</button>
      </div>

      <div class="petty-chatbot-messages" ref="messagesEl">
        <div v-for="(msg, idx) in messages" :key="idx" :class="['petty-msg', msg.role]">
          <div v-if="msg.content" class="petty-msg-text">{{ msg.content }}</div>

          <div v-if="msg.images && msg.images.length" class="petty-msg-images">
            <img v-for="(img, imageIndex) in msg.images" :key="imageIndex" :src="img.data" :alt="img.name" />
          </div>
        </div>

        <div v-if="loading" class="petty-msg assistant petty-msg-thinking">
          <span class="petty-thinking-icon">🤖</span>
          <div class="petty-thinking-content">
            <div class="petty-thinking-indicator">
              <span></span>
              <span></span>
              <span></span>
            </div>
            <span class="petty-thinking-text">Chatbot đang suy nghĩ...</span>
          </div>
        </div>
      </div>

      <div v-if="attachedImages.length" class="petty-chatbot-preview">
        <div v-for="(img, index) in attachedImages" :key="`${img.name}-${index}`" class="petty-preview-item">
          <img :src="img.data" :alt="img.name" />
          <button type="button" @click="removeImage(index)">×</button>
        </div>
      </div>

      <div class="petty-chatbot-input">
        <input ref="imageInput" type="file" accept="image/*" multiple hidden @change="onImageSelect" />

        <button class="petty-btn-attach" type="button" @click="triggerAttach" :disabled="loading">📎</button>

        <textarea
          v-model="input"
          rows="1"
          placeholder="Nhập câu hỏi về thú cưng..."
          @keydown.enter.exact.prevent="sendMessage"
        ></textarea>

        <button type="button" :disabled="loading" @click="sendMessage">Gửi</button>
      </div>
    </div>
  </div>
</template>

<script>
const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

export default {
  name: "ChatbotWidget",
  data() {
    return {
      isOpen: false,
      loading: false,
      input: "",
      messages: [
        {
          role: "assistant",
          content: "Xin chào! Mình hỗ trợ tư vấn chăm sóc thú cưng. Bạn cần hỏi gì?",
          images: [],
        },
      ],
      history: [],
      attachedImages: [],
    };
  },
  computed: {
    isHomePage() {
      return this.$route?.path === "/";
    },
  },
  methods: {
    closePanel() {
      this.isOpen = false;
      this.input = "";
      this.attachedImages = [];
    },
    triggerAttach() {
      this.$refs.imageInput?.click();
    },
    async fileToDataUrl(file) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(file);
      });
    },
    async onImageSelect(event) {
      const files = Array.from(event.target.files || []);
      for (const file of files) {
        if (!file.type.startsWith("image/")) continue;
        const data = await this.fileToDataUrl(file);
        this.attachedImages.push({ name: file.name, data, type: file.type });
      }
      event.target.value = "";
    },
    removeImage(index) {
      this.attachedImages.splice(index, 1);
    },
    async sendMessage() {
      const message = this.input.trim();
      const imagesSnapshot = this.attachedImages.map((img) => ({ ...img }));

      if ((!message && !imagesSnapshot.length) || this.loading) return;

      this.messages.push({ role: "user", content: message, images: imagesSnapshot });
      this.history.push({
        role: "user",
        content: message || "Người dùng đã gửi hình ảnh thú cưng.",
      });

      this.input = "";
      this.attachedImages = [];
      this.loading = true;
      this.scrollToBottom();

      try {
        const response = await fetch(`${API_BASE}/chatbot/message`, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            message,
            images: imagesSnapshot.map((img) => img.data),
            history: this.history.slice(-10),
          }),
        });

        const data = await response.json();
        if (!response.ok || !data?.status) {
          throw new Error(data?.message || `API Error: ${response.status}`);
        }

        const reply = data.reply || "Không có nội dung phản hồi.";
        this.messages.push({ role: "assistant", content: reply, images: [] });
        this.history.push({ role: "assistant", content: reply });
      } catch (error) {
        this.messages.push({
          role: "assistant",
          content: `Lỗi: ${error.message}`,
          images: [],
        });
      } finally {
        this.loading = false;
        this.scrollToBottom();
      }
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const el = this.$refs.messagesEl;
        if (el) {
          el.scrollTop = el.scrollHeight;
        }
      });
    },
  },
};
</script>

<style scoped>
.petty-chatbot-fab {
  position: fixed;
  right: 24px;
  bottom: 24px;
  width: 64px;
  height: 64px;
  border-radius: 9999px;
  border: none;
  background: #0a2b9d;
  color: #fff;
  box-shadow: 0 10px 24px rgba(10, 43, 157, 0.35);
  z-index: 9998;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.petty-chatbot-fab svg {
  width: 28px;
  height: 28px;
}

.petty-chatbot-panel {
  position: fixed;
  right: 24px;
  bottom: 24px;
  width: 360px;
  max-width: calc(100vw - 24px);
  height: 540px;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 14px 36px rgba(0, 0, 0, 0.2);
  z-index: 9999;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.petty-chatbot-header {
  background: #0a2b9d;
  color: #fff;
  padding: 12px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.petty-chatbot-header button {
  border: none;
  background: transparent;
  color: #fff;
  font-size: 24px;
  line-height: 1;
  cursor: pointer;
}

.petty-chatbot-messages {
  flex: 1;
  overflow-y: auto;
  padding: 12px;
  background: #f7f8fb;
}

.petty-msg {
  max-width: 88%;
  padding: 10px 12px;
  border-radius: 12px;
  margin-bottom: 8px;
  white-space: pre-wrap;
  line-height: 1.4;
}

.petty-msg.user {
  margin-left: auto;
  background: #dbeafe;
}

.petty-msg.assistant {
  margin-right: auto;
  background: #ffffff;
  border: 1px solid #eceef4;
}

.petty-msg-images {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 6px;
  margin-top: 8px;
}

.petty-msg-images img {
  width: 100%;
  height: 90px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #d7dbe5;
}

.petty-chatbot-preview {
  border-top: 1px solid #eceef4;
  padding: 8px 10px;
  display: flex;
  gap: 8px;
  overflow-x: auto;
  background: #fff;
}

.petty-preview-item {
  position: relative;
  width: 56px;
  height: 56px;
  flex-shrink: 0;
}

.petty-preview-item img {
  width: 100%;
  height: 100%;
  border-radius: 8px;
  object-fit: cover;
  border: 1px solid #d7dbe5;
}

.petty-preview-item button {
  position: absolute;
  top: -6px;
  right: -6px;
  width: 18px;
  height: 18px;
  border-radius: 9999px;
  border: none;
  background: #111827;
  color: #fff;
  cursor: pointer;
  font-size: 12px;
  line-height: 18px;
  padding: 0;
}

.petty-chatbot-input {
  border-top: 1px solid #eceef4;
  padding: 10px;
  display: flex;
  gap: 8px;
  background: #fff;
  align-items: center;
}

.petty-btn-attach {
  border: 1px solid #d7dbe5;
  border-radius: 10px;
  width: 38px;
  height: 38px;
  background: #fff;
  cursor: pointer;
}

.petty-chatbot-input textarea {
  flex: 1;
  resize: none;
  border: 1px solid #d7dbe5;
  border-radius: 10px;
  padding: 8px 10px;
  outline: none;
}

.petty-chatbot-input button:last-child {
  border: none;
  border-radius: 10px;
  padding: 0 14px;
  height: 38px;
  background: #0a2b9d;
  color: #fff;
  cursor: pointer;
}

.petty-chatbot-input button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.petty-msg-thinking {
  display: flex;
  align-items: flex-start;
  gap: 8px;
}

.petty-thinking-icon {
  font-size: 20px;
  flex-shrink: 0;
  animation: petty-pulse 1.5s infinite;
}

.petty-thinking-content {
  display: flex;
  align-items: center;
  gap: 8px;
}

.petty-thinking-indicator {
  display: flex;
  gap: 4px;
  align-items: center;
}

.petty-thinking-indicator span {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #0a2b9d;
  animation: petty-bounce 1.4s infinite;
}

.petty-thinking-indicator span:nth-child(2) {
  animation-delay: 0.2s;
}

.petty-thinking-indicator span:nth-child(3) {
  animation-delay: 0.4s;
}

.petty-thinking-text {
  font-size: 14px;
  color: #666;
  font-style: italic;
}

@keyframes petty-bounce {
  0%, 80%, 100% {
    opacity: 0.5;
    transform: translateY(0);
  }
  40% {
    opacity: 1;
    transform: translateY(-8px);
  }
}

@keyframes petty-pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}
</style>
