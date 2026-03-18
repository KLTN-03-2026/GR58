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
        <h2 class="text-base font-normal text-[#101828]">Viết bài mới</h2>
      </div>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto">
      <div class="flex gap-6 px-8 py-6">
        <!-- Main Content (Left) -->
        <div class="flex-1 flex flex-col gap-4">
          <input
            v-model="title"
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
                v-model="slug"
                type="text"
                placeholder="tieu-de-bai-viet"
                class="flex-1 text-sm px-3 py-2 bg-[#f3f3f5] rounded-lg border-0 focus:ring-0 text-[#717182]"
                readonly
              />
            </div>
            <p class="text-xs text-[#6a7282]">
              Slug sẽ tự động tạo từ tiêu đề. Nhập tiêu đề để cập nhật.
            </p>
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
              v-model="content"
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
              v-model="excerpt"
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

            <!-- Error Message -->
            <div
              v-if="publishError"
              class="bg-red-50 border border-red-200 rounded-lg px-3 py-2"
            >
              <p class="text-sm text-red-600">{{ publishError }}</p>
            </div>

            <!-- Success Message -->
            <div
              v-if="publishSuccess"
              class="bg-green-50 border border-green-200 rounded-lg px-3 py-2"
            >
              <p class="text-sm text-green-600">{{ publishSuccess }}</p>
            </div>

            <button
              @click="publishArticle"
              :disabled="isPublishing || !canPublish"
              class="w-full h-9 bg-[#5a9690] text-white rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-[#5a9690]/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span v-if="isPublishing" class="inline-block animate-spin"
                >⏳</span
              >
              {{ isPublishing ? "Đang xuất bản..." : "Xuất bản" }}
            </button>
          </div>

          <!-- Categories -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] p-4 flex flex-col gap-3"
          >
            <h3 class="text-sm font-semibold text-neutral-950">Phân loại</h3>
            <div class="flex flex-col gap-2">
              <!-- Loading state -->
              <div v-if="isLoadingCategories" class="text-sm text-[#6a7282]">
                Đang tải danh mục...
              </div>

              <!-- Empty state -->
              <div
                v-else-if="categories.length === 0"
                class="text-sm text-[#9ca3af]"
              >
                Chưa có danh mục nào. Hãy thêm danh mục mới!
              </div>

              <!-- Categories list -->
              <template v-else>
                <label
                  v-for="category in categories"
                  :key="category.id"
                  class="flex items-center gap-2 cursor-pointer"
                >
                  <input
                    :checked="selectedCategories.includes(category.id)"
                    @change="
                      (e) => {
                        if (e.target.checked) {
                          selectedCategories.push(category.id);
                        } else {
                          selectedCategories = selectedCategories.filter(
                            (id) => id !== category.id
                          );
                        }
                      }
                    "
                    type="checkbox"
                    class="w-4 h-4 rounded bg-[#f3f3f5] border border-[rgba(0,0,0,0.1)] text-[#00a63e] focus:ring-0 focus:ring-offset-0"
                  />
                  <span class="text-sm font-medium text-neutral-950">
                    {{ category.ten_phan_loai }}
                  </span>
                </label>
              </template>

              <button
                @click="isAddCategoryModalOpen = true"
                class="w-full h-8 bg-white border !border-gray-400 rounded-lg flex items-center justify-center gap-2 text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors mt-2"
              >
                <AddIcon />
                Thêm danh mục mới
              </button>
            </div>
          </div>

          <!-- Featured Image -->
          <div
            class="bg-white border !border-gray-400 rounded-[14px] p-4 flex flex-col gap-3"
          >
            <h3 class="text-sm font-semibold text-neutral-950">Ảnh đại diện</h3>

            <!-- Image Preview or Upload Area -->
            <div
              v-if="!featuredImage"
              class="border-2 border-dashed border-[#d1d5dc] rounded-[10px] h-[120px] flex flex-col items-center justify-center gap-2"
            >
              <ImageIcon class="w-8 h-8 text-gray-400" />
              <p class="text-sm text-[#4a5565]">Chưa có ảnh</p>
              <button
                @click="$refs.imageInput.click()"
                type="button"
                class="h-8 px-[10px] bg-white border !border-gray-400 rounded-lg flex items-center justify-center gap-2 text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors"
              >
                Upload ảnh
              </button>
            </div>

            <!-- Image Preview with Remove Button -->
            <div v-else class="relative">
              <img
                :src="featuredImage"
                alt="Featured"
                class="w-full h-[120px] object-cover rounded-[10px]"
              />
              <button
                @click="removeFeaturedImage"
                type="button"
                class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition-colors"
              >
                ✕
              </button>
            </div>

            <!-- Hidden File Input -->
            <input
              ref="imageInput"
              type="file"
              accept="image/*"
              @change="handleImageUpload"
              class="hidden"
            />

            <!-- Upload Progress -->
            <div
              v-if="uploadProgress > 0 && uploadProgress < 100"
              class="w-full"
            >
              <div class="flex items-center justify-between mb-1">
                <span class="text-xs text-[#6a7282]">Đang tải lên...</span>
                <span class="text-xs text-[#6a7282]"
                  >{{ uploadProgress }}%</span
                >
              </div>
              <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                <div
                  class="h-full bg-[#00a63e] transition-all"
                  :style="{ width: uploadProgress + '%' }"
                ></div>
              </div>
            </div>

            <!-- Error Message -->
            <div
              v-if="uploadError"
              class="bg-red-50 border border-red-200 rounded-lg px-3 py-2"
            >
              <p class="text-sm text-red-600">{{ uploadError }}</p>
            </div>

            <p class="text-xs text-[#6a7282]">Gợi ý: 1200x630px (tỷ lệ 16:9)</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Add Category Modal -->
    <div
      v-if="isAddCategoryModalOpen"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
      <ThemDanhMuc
        :modelValue="isAddCategoryModalOpen"
        @update:modelValue="isAddCategoryModalOpen = $event"
        @close="isAddCategoryModalOpen = false"
        @category-added="handleAddCategory"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import { useRouter } from "vue-router";
import ThemDanhMuc from "../ThemDanhMuc/index.vue";
import client from "../../../../../utils/api.js";
//Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";
import AddIcon from "@/assets/svg/add.svg";
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

const router = useRouter();

// Form data
const title = ref("");
const slug = ref("");
const content = ref("");
const excerpt = ref("");

// Categories
const categories = ref([]);
const selectedCategories = ref([]);
const isLoadingCategories = ref(false);

// Image
const featuredImage = ref("");
const imageInput = ref(null);
const uploadProgress = ref(0);
const uploadError = ref("");
const isUploadingImage = ref(false);

// Publishing
const isPublishing = ref(false);
const publishError = ref("");
const publishSuccess = ref("");

// Modal state
const isAddCategoryModalOpen = ref(false);

// Computed: Check if form is valid for publishing
const canPublish = computed(() => {
  return (
    title.value.trim().length >= 5 &&
    content.value.trim().length >= 10 &&
    selectedCategories.value.length > 0
  );
});

// Fetch all categories
const fetchCategories = async () => {
  isLoadingCategories.value = true;
  try {
    const response = await client.get("/phan-loai-bai-viet");
    if (response.data.status) {
      categories.value = response.data.data || [];
      console.log("✅ Categories loaded:", categories.value);
    } else {
      console.error("❌ Failed to fetch categories:", response.data.message);
    }
  } catch (error) {
    console.error("❌ Error fetching categories:", error);
  } finally {
    isLoadingCategories.value = false;
  }
};

// Load categories on mount
onMounted(() => {
  fetchCategories();
});

// Watch title changes and auto-generate slug
watch(
  () => title.value,
  (newTitle) => {
    if (newTitle.trim()) {
      slug.value = convertToSlug(newTitle);
    } else {
      slug.value = "";
    }
  }
);

const handleAddCategory = (newCategory) => {
  console.log("New category added:", newCategory);
  categories.value.push(newCategory);
  isAddCategoryModalOpen.value = false;
  if (newCategory.id) {
    selectedCategories.value.push(newCategory.id);
  }
};

// Convert Vietnamese text to slug
const convertToSlug = (text) => {
  if (!text) return "";

  // Mapping for Vietnamese characters
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
    Á: "a",
    À: "a",
    Ả: "a",
    Ã: "a",
    Ạ: "a",
    Ă: "a",
    Ắ: "a",
    Ằ: "a",
    Ẳ: "a",
    Ẵ: "a",
    Ặ: "a",
    Â: "a",
    Ấ: "a",
    Ầ: "a",
    Ẩ: "a",
    Ẫ: "a",
    Ậ: "a",
    É: "e",
    È: "e",
    Ẻ: "e",
    Ẽ: "e",
    Ẹ: "e",
    Ê: "e",
    Ế: "e",
    Ề: "e",
    Ể: "e",
    Ễ: "e",
    Ệ: "e",
    Í: "i",
    Ì: "i",
    Ỉ: "i",
    Ĩ: "i",
    Ị: "i",
    Ó: "o",
    Ò: "o",
    Ỏ: "o",
    Õ: "o",
    Ọ: "o",
    Ô: "o",
    Ố: "o",
    Ồ: "o",
    Ổ: "o",
    Ỗ: "o",
    Ộ: "o",
    Ơ: "o",
    Ớ: "o",
    Ờ: "o",
    Ở: "o",
    Ỡ: "o",
    Ợ: "o",
    Ú: "u",
    Ù: "u",
    Ủ: "u",
    Ũ: "u",
    Ụ: "u",
    Ư: "u",
    Ứ: "u",
    Ừ: "u",
    Ử: "u",
    Ữ: "u",
    Ự: "u",
    Ý: "y",
    Ỳ: "y",
    Ỷ: "y",
    Ỹ: "y",
    Ỵ: "y",
    Đ: "d",
  };

  // Convert Vietnamese characters to ASCII
  let slug = text
    .split("")
    .map((char) => vietnameseMap[char] || char)
    .join("");

  // Convert to lowercase
  slug = slug.toLowerCase();

  // Remove special characters and keep only alphanumeric and hyphens
  slug = slug.replace(/[^\w\s-]/g, "");

  // Replace spaces with hyphens
  slug = slug.trim().replace(/\s+/g, "-");

  // Remove multiple consecutive hyphens
  slug = slug.replace(/-+/g, "-");

  return slug;
};

// Handle image upload
const handleImageUpload = async (event) => {
  const file = event.target.files?.[0];
  if (!file) return;

  uploadError.value = "";
  uploadProgress.value = 0;

  console.log(
    "📸 File selected:",
    file.name,
    "Size:",
    file.size,
    "Type:",
    file.type
  );

  // Validate file
  if (!file.type.startsWith("image/")) {
    uploadError.value = "Vui lòng chọn một file ảnh hợp lệ";
    console.error("❌ Invalid file type:", file.type);
    return;
  }

  if (file.size > 5 * 1024 * 1024) {
    uploadError.value = "Kích thước file không được vượt quá 5MB";
    console.error("❌ File too large:", file.size, "bytes");
    return;
  }

  isUploadingImage.value = true;

  try {
    const formData = new FormData();
    formData.append("image", file);

    console.log("📤 Uploading to /upload-image...");

    // Simulate progress
    const interval = setInterval(() => {
      uploadProgress.value += Math.random() * 30;
      if (uploadProgress.value > 90) {
        uploadProgress.value = 90;
      }
    }, 200);

    const response = await client.post("/upload-image", formData);

    clearInterval(interval);
    uploadProgress.value = 100;

    console.log("📥 Response status:", response.status);
    console.log("📥 Response data:", response.data);

    if (response.data.status) {
      // Assume the API returns { status: true, data: { url: '...' } }
      const imageUrl = response.data.data.url || response.data.data;
      if (!imageUrl) {
        console.error("❌ No URL in response:", response.data);
        uploadError.value = "Lỗi: Không nhận được URL ảnh từ server";
        return;
      }

      featuredImage.value = imageUrl;
      console.log("✅ Image uploaded successfully:", imageUrl);
      console.log("✅ Featured image set in UI");

      // Reset file input
      if (imageInput.value) {
        imageInput.value.value = "";
      }
    } else {
      const errorMsg = response.data.message || "Lỗi khi tải lên ảnh";
      uploadError.value = errorMsg;
      console.error("❌ Upload failed:", errorMsg);
      console.error("❌ Response:", response.data);
    }
  } catch (error) {
    console.error("❌ Exception during upload:", error);
    console.error("❌ Error response data:", error.response?.data);
    console.error("❌ Error status:", error.response?.status);
    console.error("❌ Error message:", error.message);

    // Better error messages
    if (error.response?.status === 401) {
      uploadError.value = "❌ 401 Unauthorized: Vui lòng đăng nhập lại.";
    } else if (error.response?.status === 403) {
      uploadError.value = "❌ 403 Forbidden: Bạn không có quyền tải lên ảnh.";
    } else if (error.response?.status === 422) {
      uploadError.value =
        "❌ 422 Validation Error: " +
        (error.response?.data?.message ||
          "File không hợp lệ. Chỉ nhận jpg/png/gif/webp ≤5MB");
    } else if (error.response?.status === 500) {
      uploadError.value =
        "❌ 500 Server Error: " +
        (error.response?.data?.message || "Lỗi server. Kiểm tra logs");
    } else {
      uploadError.value =
        "❌ " +
        (error.response?.data?.message ||
          error.message ||
          "Lỗi không xác định");
    }
  } finally {
    isUploadingImage.value = false;
    setTimeout(() => {
      uploadProgress.value = 0;
    }, 1000);
  }
};

// Remove featured image
const removeFeaturedImage = () => {
  featuredImage.value = "";
  uploadError.value = "";
  if (imageInput.value) {
    imageInput.value.value = "";
  }
};

// Publish article
const publishArticle = async () => {
  publishError.value = "";
  publishSuccess.value = "";

  // Validate required fields
  if (!canPublish.value) {
    publishError.value =
      "Vui lòng điền đầy đủ thông tin: tiêu đề (≥5 ký tự), nội dung (≥10 ký tự), và chọn ít nhất 1 danh mục";
    return;
  }

  isPublishing.value = true;

  try {
    // Generate slug from title if not manually set
    const finalSlug = slug.value.trim() || generateSlug(title.value);

    // Prepare request payload
    const payload = {
      ten_bai_viet: title.value.trim(),
      slug: finalSlug,
      noi_dung: content.value.trim(),
      mo_ta: excerpt.value.trim() || null,
      anh_bai_viet: featuredImage.value || null,
      trang_thai: "published",
      phan_loai_bai_viet_id: selectedCategories.value[0], // Backend expects single category
    };

    console.log("📤 Publishing article with payload:", payload);

    const response = await client.post("/bai-viet", payload);

    if (response.data.status) {
      publishSuccess.value =
        response.data.message || "Xuất bản bài viết thành công!";
      console.log("✅ Article published:", response.data.data);

      // Redirect after 2 seconds
      setTimeout(() => {
        console.log("🔄 Redirecting to article list...");
        router
          .push("/admin/bai-viet")
          .then(() => {
            console.log("✅ Redirect  successful");
          })
          .catch((error) => {
            console.error("❌ Redirect error:", error);
          });
      }, 2000);
    } else {
      publishError.value = response.data.message || "Lỗi khi xuất bản bài viết";
    }
  } catch (error) {
    console.error("❌ Error publishing article:", error);

    if (error.response?.data?.errors) {
      // Backend validation errors
      const backendErrors = error.response.data.errors;
      publishError.value = Object.values(backendErrors).flat().join(", ");
    } else if (error.response?.data?.message) {
      publishError.value = error.response.data.message;
    } else {
      publishError.value = "Lỗi không xác định khi xuất bản bài viết";
    }
  } finally {
    isPublishing.value = false;
  }
};

// Generate slug from title
const generateSlug = (text) => {
  return convertToSlug(text);
};
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
