<template>
  <div class="bg-white flex flex-col h-full">
    <!-- Header -->
    <div
      class="bg-white border-b border-gray-200 flex items-center h-[69px] px-6 py-4"
    >
      <div class="flex items-center gap-4">
        <button
          @click="router.back()"
          class="flex items-center justify-center w-9 h-9 hover:bg-gray-100 rounded-lg transition-colors"
        >
          <ArrowLeftIcon />
        </button>
        <div class="border-l border-[#d1d5dc] h-6"></div>
        <h2 class="text-base font-normal text-[#101828]">Chỉnh sửa bài viết</h2>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto">
      <!-- Loading State -->
      <div v-if="isLoading" class="flex items-center justify-center h-full">
        <p class="text-gray-500">Đang tải bài viết...</p>
      </div>

      <!-- Error State -->
      <div
        v-else-if="saveError"
        class="p-6 bg-red-50 border border-red-200 rounded-lg m-6"
      >
        <p class="text-red-800">❌ {{ saveError }}</p>
      </div>

      <!-- Success State -->
      <div
        v-else-if="saveSuccess"
        class="p-6 bg-green-50 border border-green-200 rounded-lg m-6"
      >
        <p class="text-green-800">✅ {{ saveSuccess }}</p>
      </div>

      <!-- Main Content -->
      <div v-else class="flex gap-6 px-8 py-6">
        <!-- Main Content (Left) -->
        <div class="flex-1 flex flex-col gap-4">
          <!-- Title -->
          <input
            v-model="post.title"
            @input="titleWatcher"
            type="text"
            placeholder="Nhập tiêu đề bài viết tại đây..."
            class="w-full text-sm px-3 py-2 bg-[#f3f3f5] rounded-lg border-0 focus:ring-0 text-[#717182] placeholder:text-[#717182]"
          />

          <!-- Slug -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-[#4a5565]"
              >Đường dẫn tĩnh (Slug)</label
            >
            <div class="flex items-center gap-2">
              <span class="text-sm text-[#6a7282]">petty.vn/</span>
              <input
                v-model="post.slug"
                type="text"
                class="flex-1 text-sm px-3 py-2 bg-[#f3f3f5] rounded-lg border-0 focus:ring-0 text-neutral-950"
              />
            </div>
          </div>

          <!-- Editor -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] flex flex-col"
          >
            <!-- Toolbar -->
            <div class="flex items-center gap-2 p-4 pb-3">
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <BoldIcon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <ItalicIcon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <UnderlineIcon />
              </button>
              <div class="border-l border-[#d1d5dc] h-6"></div>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <H1Icon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <H2Icon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <H3Icon />
              </button>
              <div class="border-l border-[#d1d5dc] h-6"></div>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <ListIcon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <NumberListIcon />
              </button>
              <div class="border-l border-[#d1d5dc] h-6"></div>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <AlignLeftIcon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <AlignCenterIcon />
              </button>
              <button
                class="w-8 h-7 bg-white border !border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50"
              >
                <AlignRightIcon />
              </button>
              <div class="border-l border-[#d1d5dc] h-6"></div>
              <button
                class="h-7 px-2 bg-white border !border-gray-300 rounded-lg flex items-center justify-center gap-1.5 hover:bg-gray-50"
              >
                <ImageIcon class="w-4 h-4" />
                <span class="text-xs font-medium text-neutral-950"
                  >Chèn ảnh/Video</span
                >
              </button>
            </div>
            <!-- Textarea -->
            <textarea
              v-model="post.content"
              class="flex-1 min-h-[320px] px-4 pb-4 resize-none bg-[#f3f3f5] rounded-b-[14px] border-0 focus:ring-0 text-sm text-[#717182] placeholder:text-[#717182] font-['Tinos']"
              placeholder="Nhập nội dung bài viết..."
            ></textarea>
          </div>

          <!-- Excerpt -->
          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-neutral-950"
              >Tóm tắt (Excerpt)</label
            >
            <textarea
              v-model="post.excerpt"
              rows="3"
              class="w-full px-3 py-2 bg-[#f3f3f5] rounded-lg border-0 focus:ring-0 text-sm text-[#717182] placeholder:text-[#717182]"
              placeholder="Mô tả ngắn hiển thị khi share Facebook/Zalo..."
            ></textarea>
            <p class="text-xs text-[#6a7282]">
              Gợi ý: 150-160 ký tự để hiển thị tốt trên social media
            </p>
          </div>
        </div>

        <!-- Sidebar (Right) -->
        <div class="w-[320px] flex flex-col gap-4">
          <!-- Publish Status -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] p-4 flex flex-col gap-3"
          >
            <h3 class="text-sm font-semibold text-neutral-950">
              Đăng (Publish)
            </h3>
            <button
              @click="updatePost"
              :disabled="isSaving"
              class="w-full h-9 bg-[#5a9690] text-white rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-[#5a9590]/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isSaving ? "Đang lưu..." : "Cập nhật" }}
            </button>
            <p class="text-xs text-[#6a7282]">
              <strong>Trạng thái:</strong> {{ post.statusLabel }}
            </p>
          </div>

          <!-- Categories -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] p-4 flex flex-col gap-3"
          >
            <h3 class="text-sm font-semibold text-neutral-950">Phân loại</h3>
            <div class="flex flex-col gap-2">
              <select
                v-model="post.categoryId"
                class="w-full px-3 py-2 bg-[#f3f3f5] rounded-lg border-0 focus:ring-0 text-sm text-neutral-950"
              >
                <option value="">Chọn danh mục</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.ten_phan_loai }}
                </option>
              </select>
              <p class="text-xs text-[#6a7282]">
                <strong>Danh mục hiện tại:</strong> {{ post.categoryName }}
              </p>
            </div>
          </div>

          <!-- Featured Image -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] p-4 flex flex-col gap-3"
          >
            <h3 class="text-sm font-semibold text-neutral-950">Ảnh đại diện</h3>
            <div class="relative h-[120px] bg-gray-100 rounded overflow-hidden">
              <img
                v-if="post.thumbnail"
                :src="post.thumbnail"
                class="w-full h-full object-cover"
                @error="onImageError"
              />
              <div
                v-else
                class="w-full h-full flex items-center justify-center text-gray-400"
              >
                <span class="text-sm">Chưa có ảnh</span>
              </div>
              <button
                v-if="post.thumbnail"
                @click="removeImage"
                class="absolute top-2 right-2 w-9 h-8 bg-[#d4183d] text-white rounded-lg flex items-center justify-center hover:bg-[#b01432] transition-colors"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>

            <!-- Upload Button -->
            <input
              ref="imageInput"
              type="file"
              accept="image/jpeg,image/png,image/gif,image/webp"
              @change="handleImageUpload"
              class="hidden"
            />
            <button
              @click="$refs.imageInput?.click()"
              class="w-full h-9 bg-[#f3f3f5] text-neutral-950 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-gray-200 transition-colors"
            >
              <ImageIcon class="w-4 h-4" />
              {{ post.thumbnail ? "Thay ảnh" : "Tải ảnh lên" }}
            </button>

            <p class="text-xs text-[#6a7282]">
              Gợi ý: 1200x630px (tỷ lệ 16:9), max 5MB
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";

// Router
const router = useRouter();
const route = useRoute();

// Import API client
import client from "../../../../../utils/api.js";

// Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";
import BoldIcon from "@/assets/svg/bold.svg";
import ItalicIcon from "@/assets/svg/italic.svg";
import UnderlineIcon from "@/assets/svg/underline.svg";
import H1Icon from "@/assets/svg/heading1.svg";
import H2Icon from "@/assets/svg/heading2.svg";
import H3Icon from "@/assets/svg/heading3.svg";
import ListIcon from "@/assets/svg/list.svg";
import NumberListIcon from "@/assets/svg/list-number.svg";
import AlignLeftIcon from "@/assets/svg/align-left.svg";
import AlignCenterIcon from "@/assets/svg/align-center.svg";
import AlignRightIcon from "@/assets/svg/align-right.svg";
import ImageIcon from "@/assets/svg/image.svg";
import TrashIcon from "@/assets/svg/trash.svg";
// Post Data
const post = ref({
  id: null,
  title: "",
  slug: "",
  content: "",
  excerpt: "",
  status: "published",
  statusLabel: "Đã xuất bản",
  categoryId: null,
  categoryName: "",
  thumbnail: null,
  nhanVienName: "",
});

const categories = ref([]);
const isLoading = ref(false);
const isSaving = ref(false);
const saveError = ref("");
const saveSuccess = ref("");

// Fetch post data from API
const fetchPost = async () => {
  isLoading.value = true;
  try {
    const response = await client.get(`/bai-viet/${route.params.id}`);
    if (response.data.status && response.data.data) {
      const postData = response.data.data;
      console.log("📝 Post loaded:", postData);

      post.value = {
        id: postData.id,
        title: postData.ten_bai_viet || "",
        slug: postData.slug || "",
        content: postData.noi_dung || "",
        excerpt: postData.mo_ta || "",
        status: postData.trang_thai || "published",
        statusLabel: getStatusLabel(postData.trang_thai),
        categoryId: postData.phan_loai_bai_viet_id,
        categoryName: postData.phan_loai?.ten_phan_loai || "Chưa chọn",
        thumbnail: postData.anh_bai_viet || null,
        nhanVienName: postData.nhan_vien?.ho_va_ten || "Không xác định",
      };
    }
  } catch (error) {
    console.error("❌ Error fetching post:", error);
    saveError.value = "Không thể tải bài viết. Vui lòng thử lại.";
  } finally {
    isLoading.value = false;
  }
};

// Fetch categories
const fetchCategories = async () => {
  try {
    const response = await client.get("/phan-loai-bai-viet");
    if (response.data.status) {
      categories.value = response.data.data || [];
      console.log("✅ Categories loaded:", categories.value);
    }
  } catch (error) {
    console.error("❌ Error fetching categories:", error);
  }
};

// Get status label
const getStatusLabel = (status) => {
  const labels = {
    published: "Đã xuất bản",
    draft: "Bản nháp",
    hidden: "Đã ẩn",
  };
  return labels[status] || "Không xác định";
};

// Update post
const updatePost = async () => {
  if (!post.value.title.trim()) {
    saveError.value = "Vui lòng nhập tiêu đề bài viết";
    return;
  }

  if (!post.value.content.trim()) {
    saveError.value = "Vui lòng nhập nội dung bài viết";
    return;
  }

  isSaving.value = true;
  saveError.value = "";
  saveSuccess.value = "";

  try {
    const payload = {
      ten_bai_viet: post.value.title,
      slug: post.value.slug,
      noi_dung: post.value.content,
      mo_ta: post.value.excerpt,
      trang_thai: post.value.status,
      phan_loai_bai_viet_id: post.value.categoryId,
      anh_bai_viet: post.value.thumbnail,
    };

    console.log("📤 Saving post:", payload);
    const response = await client.patch(`/bai-viet/${post.value.id}`, payload);

    if (response.data.status) {
      console.log("✅ Post updated successfully");
      saveSuccess.value = "Cập nhật bài viết thành công!";
      setTimeout(() => {
        router.push("/admin/bai-viet");
      }, 1500);
    } else {
      saveError.value = response.data.message || "Cập nhật thất bại";
    }
  } catch (error) {
    console.error("❌ Error updating post:", error);
    saveError.value =
      error.response?.data?.message || "Lỗi khi cập nhật bài viết";
  } finally {
    isSaving.value = false;
  }
};

const removeImage = () => {
  post.value.thumbnail = null;
};

// Handle image upload
const handleImageUpload = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  // Validate file
  const validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
  if (!validTypes.includes(file.type)) {
    saveError.value = "Chỉ chấp nhận file JPG, PNG, GIF hoặc WEBP";
    return;
  }

  const maxSize = 5 * 1024 * 1024; // 5MB
  if (file.size > maxSize) {
    saveError.value = "Kích thước file không được vượt quá 5MB";
    return;
  }

  // Upload file
  const formData = new FormData();
  formData.append("image", file);

  try {
    console.log("📤 Uploading image...");
    const response = await client.post("/upload-image", formData, {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });

    if (response.data.status && response.data.data?.url) {
      post.value.thumbnail = response.data.data.url;
      saveError.value = "";
      console.log("✅ Image uploaded:", post.value.thumbnail);
    } else {
      saveError.value = "Upload ảnh thất bại";
    }
  } catch (error) {
    console.error("❌ Upload error:", error);
    saveError.value = error.response?.data?.message || "Lỗi khi upload ảnh";
  } finally {
    // Reset input
    if (event.target) {
      event.target.value = "";
    }
  }
};

// Handle image load error
const onImageError = () => {
  console.warn("⚠️ Image failed to load:", post.value.thumbnail);
  post.value.thumbnail = null;
};

const convertToSlug = (text) => {
  // Vietnamese slug conversion
  let slug = text.toLowerCase();

  const vietnameseMap = {
    á: "a",
    à: "a",
    ả: "a",
    ã: "a",
    ạ: "a",
    ă: "a",
    ắ: "a",
    ằ: "a",
    ẳ: "a",
    ẵ: "a",
    ặ: "a",
    â: "a",
    ấ: "a",
    ầ: "a",
    ẩ: "a",
    ẫ: "a",
    ậ: "a",
    é: "e",
    è: "e",
    ẻ: "e",
    ẽ: "e",
    ẹ: "e",
    ê: "e",
    ế: "e",
    ề: "e",
    ể: "e",
    ễ: "e",
    ệ: "e",
    í: "i",
    ì: "i",
    ỉ: "i",
    ĩ: "i",
    ị: "i",
    ó: "o",
    ò: "o",
    ỏ: "o",
    õ: "o",
    ọ: "o",
    ô: "o",
    ố: "o",
    ồ: "o",
    ổ: "o",
    ỗ: "o",
    ộ: "o",
    ơ: "o",
    ớ: "o",
    ờ: "o",
    ở: "o",
    ỡ: "o",
    ợ: "o",
    ú: "u",
    ù: "u",
    ủ: "u",
    ũ: "u",
    ụ: "u",
    ư: "u",
    ứ: "u",
    ừ: "u",
    ử: "u",
    ữ: "u",
    ự: "u",
    ý: "y",
    ỳ: "y",
    ỷ: "y",
    ỹ: "y",
    ỵ: "y",
    đ: "d",
  };

  for (const char in vietnameseMap) {
    slug = slug.replace(new RegExp(char, "g"), vietnameseMap[char]);
  }

  slug = slug.replace(/[^a-z0-9\s-]/g, "");
  slug = slug.replace(/\s+/g, "-");
  slug = slug.replace(/-+/g, "-");
  slug = slug.replace(/^-+|-+$/g, "");

  return slug;
};

// Watch title for auto-slug generation
const titleWatcher = () => {
  post.value.slug = convertToSlug(post.value.title);
};

onMounted(() => {
  fetchPost();
  fetchCategories();
});
</script>

<style scoped>
/* Custom font for textarea */
textarea {
  font-family: "Tinos", serif;
}

/* Remove focus outline for all inputs */
input:focus,
textarea:focus {
  outline: none;
}
</style>
