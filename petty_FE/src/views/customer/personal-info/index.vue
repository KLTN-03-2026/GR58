<template>
  <div class="min-h-screen">
    <div class="container max-w-6xl mx-auto md:px-12 lg:px-20">
      <div class="bg-white border !border-gray-300 rounded-2xl p-8 mb-6">
        <div class="mb-8">
          <h1 class="text-xl font-bold text-black mb-1">Thông tin cá nhân</h1>
          <p class="text-lg font-semibold text-gray-500">
            Quản lý thông tin tài khoản và cài đặt cá nhân của bạn
          </p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
          <div class="flex flex-col items-center gap-4">
            <div
              class="w-32 h-32 bg-teal-200 rounded-full flex items-center justify-center text-2xl font-semibold text-[#2f5755] overflow-hidden"
            >
              <img
                :src="avatarPreview || '/avatar.png'"
                alt="avatar"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
            </div>

            <div class="flex items-center gap-2">
              <input
                ref="fileInputRef"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleFileChange"
              />
              <button
                @click="onAvatarClick"
                class="flex items-center gap-3 px-4 py-2 border border-black/40 rounded-lg hover:bg-gray-100 transition"
              >
                <CameraSmIcon />
                <span class="font-semibold">Chọn ảnh</span>
              </button>

              <button
                v-if="avatarFile"
                @click="uploadAvatar"
                :disabled="avatarUploading"
                class="px-3 py-2 border rounded-lg bg-white hover:bg-gray-50 text-sm"
              >
                <span v-if="!avatarUploading">Tải lên</span>
                <span v-else>Đang tải...</span>
              </button>
            </div>
          </div>

          <form class="flex-1 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block font-semibold text-lg mb-2"
                  >Họ và tên *</label
                >
                <input
                  ref="nameRef"
                  v-model="form.name"
                  type="text"
                  :readonly="!isEditing"
                  class="w-full h-11 bg-gray-200 border border-black/10 rounded-lg px-3 font-semibold text-base focus:outline-none focus:border-teal-600"
                  :class="{
                    'opacity-50': !isEditing,
                    'opacity-100': isEditing,
                  }"
                />
                <!-- <div class="mt-2 text-sm text-gray-600 flex gap-4">
                  <div>
                    <span class="font-medium">Họ lót:</span>
                    <span>{{ hoLot }}</span>
                  </div>
                  <div>
                    <span class="font-medium">Tên:</span> <span>{{ ten }}</span>
                  </div>
                </div> -->
              </div>
              <div>
                <label class="block font-semibold text-lg mb-2"
                  >Số điện thoại *</label
                >
                <input
                  ref="phoneRef"
                  v-model="form.phone"
                  type="tel"
                  :readonly="!isEditing"
                  class="w-full h-11 bg-gray-200 border border-black/10 rounded-lg px-3 font-semibold text-base focus:outline-none focus:border-teal-600"
                  :class="{
                    'opacity-50': !isEditing,
                    'opacity-100': isEditing,
                  }"
                />
                <p
                  v-if="errors.so_dien_thoai"
                  class="text-sm text-red-600 mt-1"
                >
                  {{ errors.so_dien_thoai[0] }}
                </p>
              </div>
            </div>

            <div>
              <label class="block font-semibold text-lg mb-2">Email *</label>
              <input
                ref="emailRef"
                v-model="form.email"
                type="email"
                readonly
                class="w-full h-11 bg-gray-200 border border-black/10 rounded-lg px-3 font-semibold text-base focus:outline-none focus:border-teal-600 opacity-70"
              />
              <p v-if="errors.email" class="text-sm text-red-600 mt-1">
                {{ errors.email[0] }}
              </p>
            </div>

            <div>
              <label class="block font-semibold text-lg mb-2">Địa chỉ</label>
              <input
                ref="addressRef"
                v-model="form.address"
                type="text"
                :readonly="!isEditing"
                class="w-full h-11 bg-gray-200 border border-black/10 rounded-lg px-3 font-semibold text-base focus:outline-none focus:border-teal-600"
                :class="{ 'opacity-50': !isEditing, 'opacity-100': isEditing }"
              />
              <p v-if="errors.dia_chi" class="text-sm text-red-600 mt-1">
                {{ errors.dia_chi[0] }}
              </p>
            </div>

            <button
              type="button"
              @click="toggleEdit"
              :disabled="saving"
              class="px-5 py-2 border border-black/30 rounded-lg font-semibold text-lg hover:bg-gray-100 transition disabled:opacity-60"
            >
              <span v-if="!isEditing">Chỉnh sửa thông tin</span>
              <span v-else>{{ saving ? "Đang lưu..." : "Lưu thông tin" }}</span>
            </button>
          </form>
        </div>
      </div>

      <div class="bg-white border !border-gray-300 rounded-2xl p-8 mb-6">
        <div class="mb-8">
          <h2 class="text-xl font-bold text-black mb-1">Bảo mật tài khoản</h2>
          <p class="text-lg font-semibold text-gray-500">
            Quản lý mật khẩu và cài đặt bảo mật
          </p>
        </div>

        <div class="space-y-6">
          <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 p-4 border !border-gray-300 rounded-xl"
          >
            <div>
              <h3 class="font-semibold text-lg">Mật khẩu</h3>
              <p class="font-semibold text-gray-600">
                Thay đổi mật khẩu định kỳ để bảo vệ tài khoản
              </p>
            </div>
            <button
              @click="onChangePassword"
              class="flex items-center gap-2 px-4 py-2 border border-black/70 rounded-lg hover:bg-gray-100 transition"
            >
              <KeyIcon />
              <span class="font-semibold">Đổi mật khẩu</span>
            </button>
          </div>

          <hr class="border-t !border-gray-500" />

          <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 p-4 border !border-gray-300 rounded-xl"
          >
            <div>
              <h3 class="font-semibold text-lg">
                Đăng xuất khỏi tất cả thiết bị
              </h3>
              <p class="font-semibold text-gray-600">
                Đăng xuất khỏi tất cả các phiên đăng nhập khác
              </p>
            </div>
            <button
              @click="onLogoutAll"
              class="flex items-center gap-2 px-4 py-2 border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition"
            >
              <LogoutIcon />
              <span class="font-semibold">Đăng xuất tất cả</span>
            </button>
          </div>
        </div>
      </div>

      <div class="bg-white border !border-gray-300 rounded-2xl p-8 mb-6">
        <div class="mb-8">
          <h2 class="text-xl font-bold text-black mb-1">Tài khoản liên kết</h2>
          <p class="text-lg font-semibold text-gray-500">
            Quản lý các tài khoản mạng xã hội đã liên kết
          </p>
        </div>

        <div class="space-y-4">
          <div
            class="flex items-center justify-between p-4 border !border-gray-300 rounded-xl"
          >
            <div class="flex items-center gap-4">
              <img
                src="@/assets/img_imports/public_img/google.png"
                alt="Google"
                class="w-10 h-10"
              />
              <div>
                <div class="font-semibold">Google</div>
                <div class="text-sm text-gray-500">
                  Đã liên kết - {{ form.email }}
                </div>
              </div>
            </div>
            <div>
              <button
                @click="onUnlinkGoogle"
                class="px-3 py-1.5 font-semibold border !border-red-500 !text-red-500 rounded-md bg-white hover:bg-gray-50 text-sm"
              >
                Hủy liên kết
              </button>
            </div>
          </div>

          <div
            class="flex items-center justify-between p-4 border !border-gray-300 rounded-xl"
          >
            <div class="flex items-center gap-4">
              <img
                src="@/assets/img_imports/public_img/facebook.png"
                alt="Facebook"
                class="w-10 h-10"
              />
              <div>
                <div class="font-semibold">Facebook</div>
                <div class="text-sm text-gray-500">
                  Chưa liên kết với Facebook
                </div>
              </div>
            </div>
            <div>
              <button
                @click="onLinkFacebook"
                class="px-3 py-1.5 font-semibold border !border-blue-500 !text-blue-500 rounded-md bg-white hover:bg-gray-50 text-sm"
              >
                Liên kết
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-teal-50 border !border-teal-200 rounded-2xl p-8">
        <div class="space-y-4">
          <div class="flex justify-between">
            <span class="font-medium">Loại tài khoản:</span>
            <span class="font-bold text-gray-400">Silver</span>
          </div>
          <div class="flex justify-between">
            <span class="font-medium">Ngày tham gia:</span>
            <span class="font-bold text-gray-600">15/01/2024</span>
          </div>
          <div class="flex justify-between">
            <span class="font-medium">Số thú cưng đã đăng ký:</span>
            <span class="font-bold">2 bé</span>
          </div>
          <div class="flex justify-between">
            <span class="font-medium">Tổng số lần khám:</span>
            <span class="font-bold text-teal-600">8 lần</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onBeforeUnmount, watch, nextTick } from "vue";
import axios from "axios";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
import { getUser, logout, setAuth, getToken } from "../../../utils/auth";
import CameraSmIcon from "@/assets/svg/camerasm.svg";
import KeyIcon from "@/assets/svg/key.svg";
import LogoutIcon from "@/assets/svg/log-out.svg";

const isEditing = ref(false);

const user = ref(getUser() || null);

const initialName = (() => {
  if (user.value) {
    // prefer full_name (backend), then ho_ten, then name, then ho_lot+ten
    if (user.value.full_name) return String(user.value.full_name).trim();
    if (user.value.ho_ten) return String(user.value.ho_ten).trim();
    if (user.value.name) return String(user.value.name).trim();
    if (user.value.ho_lot || user.value.ten) {
      const hoLotPart = String(user.value.ho_lot || "").trim();
      const tenPart = String(user.value.ten || "").trim();
      if (hoLotPart || tenPart) return `${hoLotPart} ${tenPart}`.trim();
    }
  }
  return "";
})();

const form = ref({
  name: initialName,
  // prefer common phone/address keys, default to empty when missing
  phone:
    user.value?.so_dien_thoai ||
    user.value?.phone ||
    user.value?.phone_number ||
    "",
  email: user.value?.email || "",
  address: user.value?.dia_chi || user.value?.address || "",
});

const initials = computed(() => {
  const n = String(form.value.name || "").trim();
  if (!n) return "";
  const parts = n.split(/\s+/);
  if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
  return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
});

function formatNameRaw(input) {
  if (!input) return "";
  const s = String(input).trim().replace(/\s+/g, " ");
  const parts = s.split(/\s+/).filter(Boolean);
  const formatted = parts
    .map((w) => {
      if (w === w.toUpperCase()) return w;
      const lower = w.toLocaleLowerCase("vi");
      return lower.charAt(0).toLocaleUpperCase("vi") + lower.slice(1);
    })
    .join(" ");
  return formatted;
}

const hoLot = computed(() => {
  if (user.value && (user.value.ho_lot || user.value.ten)) {
    return String(user.value.ho_lot || "").trim();
  }
  const full = formatNameRaw(form.value.name || "").trim();
  if (!full) return "";
  const parts = full.split(/\s+/).filter(Boolean);
  if (parts.length <= 1) return "";
  return parts.slice(0, -1).join(" ");
});

const ten = computed(() => {
  if (user.value && (user.value.ho_lot || user.value.ten)) {
    return String(user.value.ten || "").trim();
  }
  const full = formatNameRaw(form.value.name || "").trim();
  if (!full) return "";
  const parts = full.split(/\s+/).filter(Boolean);
  return parts.length ? parts[parts.length - 1] : "";
});

const toggleEdit = () => {
  if (isEditing.value) {
    saveProfile();
    return;
  }
  isEditing.value = true;
};

// When entering edit mode, focus the email input for convenience
watch(isEditing, (val) => {
  if (val) {
    nextTick(() => {
      try {
        // Prefer to focus the name input, but if user clicked email area it will be focused
        if (emailRef.value && typeof emailRef.value.focus === "function") {
          emailRef.value.focus();
        } else if (nameRef.value && typeof nameRef.value.focus === "function") {
          nameRef.value.focus();
        }
      } catch (e) {}
    });
  }
});

const onChangePassword = () => {
  alert("Chuyển đến trang đổi mật khẩu...");
};

const onLogoutAll = () => {
  if (confirm("Bạn có chắc muốn đăng xuất khỏi tất cả thiết bị?")) {
    logout();
    alert("Đã đăng xuất khỏi tất cả thiết bị!");
  }
};

const onUnlinkGoogle = () => {
  if (confirm("Bạn muốn hủy liên kết Google?")) {
    alert("Đã hủy liên kết Google");
  }
};

const onLinkFacebook = () => {
  alert("Chuyển đến Facebook để liên kết...");
};

const saving = ref(false);
const errors = ref({});

const API_BASE = "http://127.0.0.1:8000/api/khach-hang";

function ensureAuthHeader() {
  try {
    const t = getToken();
    if (t) axios.defaults.headers.common["Authorization"] = `Bearer ${t}`;
  } catch (e) {}
}

const nameRef = ref(null);
const phoneRef = ref(null);
const emailRef = ref(null);
const addressRef = ref(null);
const fileInputRef = ref(null);
const avatarFile = ref(null);
const avatarPreview = ref(user.value?.anh_dai_dien_url || user.value?.anh_dai_dien || null);
const avatarLocalUrl = ref(null); // object URL created for preview
const avatarUploading = ref(false);

function handleImageError(e) {
  // Nếu ảnh lỗi, fallback sang avatar.png
  e.target.src = '/avatar.png';
}

function focusFirstError(errs) {
  const order = ["ho_ten", "email", "so_dien_thoai", "dia_chi"];
  for (const key of order) {
    if (errs && errs[key]) {
      try {
        if (
          key === "ho_ten" &&
          nameRef.value &&
          typeof nameRef.value.focus === "function"
        )
          return nameRef.value.focus();
        if (
          key === "email" &&
          emailRef.value &&
          typeof emailRef.value.focus === "function"
        )
          return emailRef.value.focus();
        if (
          key === "so_dien_thoai" &&
          phoneRef.value &&
          typeof phoneRef.value.focus === "function"
        )
          return phoneRef.value.focus();
        if (
          key === "dia_chi" &&
          addressRef.value &&
          typeof addressRef.value.focus === "function"
        )
          return addressRef.value.focus();
      } catch (e) {}
      break;
    }
  }
}

function onAvatarClick() {
  if (fileInputRef.value) fileInputRef.value.click();
}

function handleFileChange(e) {
  const input = e.target;
  const file = input?.files?.[0] || null;
  if (!file) return;
  if (!file.type.startsWith("image/")) {
    showErrorToast("Định dạng không hợp lệ", "Vui lòng chọn tệp ảnh (jpg, png, ...)");
    return;
  }
  avatarFile.value = file;
  if (avatarLocalUrl.value) {
    try {
      URL.revokeObjectURL(avatarLocalUrl.value);
    } catch (e) {}
  }
  const url = URL.createObjectURL(file);
  avatarLocalUrl.value = url;
  avatarPreview.value = url;
}

onBeforeUnmount(() => {
  if (avatarLocalUrl.value) {
    try {
      URL.revokeObjectURL(avatarLocalUrl.value);
    } catch (e) {}
    avatarLocalUrl.value = null;
  }
});

async function uploadAvatar() {
  if (!avatarFile.value) return;
  if (avatarUploading.value) return;
  avatarUploading.value = true;
  const base = API_BASE;
  try {
    const fd = new FormData();
    fd.append("anh_dai_dien", avatarFile.value);
    // use backend field names
    fd.append("full_name", form.value.name || "");
    fd.append("email", form.value.email || "");
    fd.append("phone", form.value.phone || "");
    fd.append("address", form.value.address || "");

    ensureAuthHeader();
    const res = await axios.post(`${base}/cap-nhat`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    const updatedUser = res?.data?.user || res?.data?.data || res?.data;
    if (updatedUser) {
      user.value = updatedUser;
      if (updatedUser.anh_dai_dien_url) {
        avatarPreview.value = updatedUser.anh_dai_dien_url;
      } else if (updatedUser.anh_dai_dien) {
        avatarPreview.value = updatedUser.anh_dai_dien;
      }
      try {
        const token = getToken();
        if (token) setAuth(token, updatedUser, true);
      } catch (e) {}
    }

    window.dispatchEvent(new CustomEvent("user-updated", { detail: updatedUser }));
    showSuccessToast("Cập nhật thành công", "Ảnh đại diện đã được cập nhật");
    avatarFile.value = null;
    if (avatarLocalUrl.value) {
      try {
        URL.revokeObjectURL(avatarLocalUrl.value);
      } catch (e) {}
      avatarLocalUrl.value = null;
    }
  } catch (e) {
    const msg = e?.response?.data?.message || e?.message || "Tải ảnh thất bại";
    if (e?.response?.status === 422) {
      const respErrors = e.response.data.errors || {};
      const first = respErrors?.anh_dai_dien || Object.values(respErrors)[0];
      if (first && Array.isArray(first)) showErrorToast("Lỗi", first[0]);
      else showErrorToast("Lỗi", msg);
    } else if (e?.response?.status === 401) {
      showErrorToast("Phiên hết hạn", "Vui lòng đăng nhập lại.");
      logout();
    } else {
      showErrorToast("Lỗi", msg);
    }
    console.error("uploadAvatar error", e);
  } finally {
    avatarUploading.value = false;
  }
}

async function saveProfile() {
  if (saving.value) return;
  saving.value = true;
  const payload = {
    // backend expects these keys
    full_name: form.value.name,
    email: form.value.email,
    phone: form.value.phone,
    address: form.value.address,
  };

  const base = "http://127.0.0.1:8000/api/khach-hang";
  try {
    errors.value = {};
    ensureAuthHeader();
    let res = null;
    try {
      res = await axios.post(`${base}/cap-nhat`, payload);
    } catch (err) {
      const status = err?.response?.status;
      if (status === 404 || status === 405 || status === 422) {
        const id = user.value?.id || user.value?.ma || user.value?.user_id;
        if (id) {
          res = await axios.put(`${base}/${id}`, payload);
        } else throw err;
      } else {
        throw err;
      }
    }

    const updatedUser = res?.data?.user || res?.data?.data || res?.data;
    try {
      const token = getToken();
      if (token) setAuth(token, updatedUser, true);
    } catch (e) {}

    if (updatedUser) {
      user.value = updatedUser;
      if (updatedUser.ho_lot || updatedUser.ten) {
        const ho = String(updatedUser.ho_lot || "").trim();
        const t = String(updatedUser.ten || "").trim();
        form.value.name = `${ho} ${t}`.trim();
      } else {
        form.value.name =
          updatedUser.ho_ten || updatedUser.name || form.value.name;
      }
      form.value.phone =
        updatedUser.so_dien_thoai || updatedUser.phone || form.value.phone;
      form.value.email = updatedUser.email || form.value.email;
      form.value.address =
        updatedUser.dia_chi || updatedUser.address || form.value.address;
    }

    showSuccessToast("Cập nhật thành công", "Thông tin đã được cập nhật");
    isEditing.value = false;
  } catch (e) {
    const msg = e?.response?.data?.message || e?.message || "Cập nhật thất bại";
    if (e?.response?.status === 422) {
      const respErrors = e.response.data.errors || {};
      errors.value = respErrors;
      focusFirstError(respErrors);
      const first = Object.values(respErrors)[0];
      if (first && Array.isArray(first)) {
        showErrorToast("Lỗi validation", first[0]);
      } else {
        showErrorToast("Lỗi", msg);
      }
    } else if (e?.response?.status === 401) {
      showErrorToast("Phiên hết hạn", "Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng đăng nhập lại.");
      logout();
    } else {
      showErrorToast("Lỗi", msg);
    }
    console.error("saveProfile error", e);
  } finally {
    saving.value = false;
  }
}
</script>

<style scoped></style>
