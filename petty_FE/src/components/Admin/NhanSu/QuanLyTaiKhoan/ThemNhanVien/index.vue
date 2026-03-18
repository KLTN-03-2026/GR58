<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-start justify-center z-[1000] pt-24"
    @click.self="$emit('close')"
  >
    <div
      class="relative bg-white border !border-gray-200 rounded-[10px] w-full max-w-4xl max-h-[85vh] overflow-hidden flex flex-col shadow-xl"
    >
      <div class="flex flex-col p-6 flex-1 overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col gap-2 mb-4">
          <h2 class="font-semibold text-lg text-black">Thêm nhân viên mới</h2>
          <p class="text-sm text-gray-500">
            Tạo tài khoản và cấp quyền truy cập cho nhân viên
          </p>
        </div>

        <!-- Form Content - 2 Column Layout -->
        <div class="grid grid-cols-2 gap-6 flex-1 overflow-y-auto pr-2">
          <!-- Left Column -->
          <div class="flex flex-col gap-4">
            <!-- Full Name -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Họ và tên *
              </label>
              <input
                v-model="formData.fullName"
                type="text"
                placeholder="VD: BS. Nguyễn Văn A"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <!-- Email -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Email (Tên đăng nhập) *
              </label>
              <input
                v-model="formData.email"
                type="email"
                placeholder="email@vcms.vn"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <!-- Phone -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Số điện thoại *
              </label>
              <input
                v-model="formData.phone"
                type="tel"
                placeholder="0901234567"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <!-- Address -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black"> Địa chỉ </label>
              <input
                v-model="formData.address"
                type="text"
                placeholder="VD: 123 Nguyễn Huệ, Quận 1, TP.HCM"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <!-- Position -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black"> Chức danh </label>
              <input
                v-model="formData.position"
                type="text"
                placeholder="VD: Trưởng khoa"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <!-- Years of Experience -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Năm kinh nghiệm
              </label>
              <div class="flex items-center gap-2">
                <input
                  v-model.number="formData.yearsOfExperience"
                  type="number"
                  placeholder="VD: 5"
                  class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182] w-32"
                />
                <span class="text-sm text-[#4a5565]">năm</span>
              </div>
            </div>

            <!-- Password -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Mật khẩu khởi tạo *
              </label>
              <input
                v-model="formData.password"
                type="password"
                placeholder="Nhập mật khẩu khởi tạo"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
              <p class="text-xs text-[#6a7282]">Mật khẩu tối thiểu 8 ký tự</p>
            </div>

            <!-- Confirm Password -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Xác nhận mật khẩu *
              </label>
              <input
                v-model="formData.passwordConfirmation"
                type="password"
                placeholder="Xác nhận mật khẩu"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
              <p
                v-if="
                  formData.passwordConfirmation &&
                  formData.password !== formData.passwordConfirmation
                "
                class="text-xs text-[#e53e3e]"
              >
                Xác nhận mật khẩu không khớp.
              </p>
            </div>
          </div>

          <!-- Right Column -->
          <div class="flex flex-col gap-4">
            <!-- System Roles -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Vai trò hệ thống *
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="role in roleOptions"
                  :key="role"
                  @click.prevent="toggleRole(role)"
                  :class="
                    formData.selectedRoles.includes(role)
                      ? 'bg-[#5a9690] text-white px-3 py-1 rounded-lg'
                      : 'bg-[#f3f3f5] text-[#364153] px-3 py-1 rounded-lg'
                  "
                >
                  <span class="text-sm">{{ role }}</span>
                </button>
              </div>

              <!-- Selected Roles Display -->
              <div
                v-if="formData.selectedRoles.length > 0"
                class="bg-gray-50 border border-gray-200 rounded-[10px] p-3 flex flex-wrap gap-2"
              >
                <span
                  v-for="(role, index) in formData.selectedRoles"
                  :key="index"
                  class="bg-green-100 border border-[#b9f8cf] rounded-lg px-3 py-1 flex items-center gap-1.5"
                >
                  <span class="text-sm text-[#008236]">{{ role }}</span>
                  <button
                    @click="removeRole(index)"
                    class="w-3.5 h-3.5 flex items-center justify-center"
                  >
                    <svg class="w-full h-full" viewBox="0 0 14 14" fill="none">
                      <path
                        d="M10.5 3.5L3.5 10.5M3.5 3.5L10.5 10.5"
                        stroke="#008236"
                        stroke-width="1.5"
                        stroke-linecap="round"
                      />
                    </svg>
                  </button>
                </span>
              </div>
              <p class="text-xs text-[#6a7282]">
                Chọn vai trò: Bác sĩ hoặc Y tá.
              </p>
            </div>

            <!-- Avatar Upload -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Ảnh đại diện
              </label>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerAvatarInput"
                  class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                >
                  <span class="font-medium text-sm text-neutral-950"
                    >Chọn ảnh</span
                  >
                </button>
                <div v-if="avatarPreview" class="text-sm text-[#4a5565]">
                  {{ avatarName }}
                </div>
              </div>
              <input
                ref="avatarInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleAvatarChange"
              />
              <p class="text-xs text-[#6a7282]">JPG, PNG (Tối đa 2MB)</p>
            </div>

            <!-- Practice Certificate -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Chứng chỉ hành nghề
              </label>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerPracticeInput"
                  class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                >
                  <span class="font-medium text-sm text-neutral-950"
                    >Tải lên file</span
                  >
                </button>
                <div v-if="practiceFileName" class="text-sm text-[#4a5565]">
                  {{ practiceFileName }}
                </div>
              </div>
              <input
                ref="practiceInput"
                type="file"
                accept="application/pdf,image/*"
                class="hidden"
                @change="handlePracticeChange"
              />
              <p class="text-xs text-[#6a7282]">PDF, JPG, PNG (Tối đa 5MB)</p>
            </div>

            <!-- Professional Degree -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Bằng cấp chuyên môn
              </label>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerDegreeInput"
                  class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                >
                  <span class="font-medium text-sm text-neutral-950"
                    >Tải lên file</span
                  >
                </button>
                <div v-if="degreeFileName" class="text-sm text-[#4a5565]">
                  {{ degreeFileName }}
                </div>
              </div>
              <input
                ref="degreeInput"
                type="file"
                accept="application/pdf,image/*"
                class="hidden"
                @change="handleDegreeChange"
              />
              <p class="text-xs text-[#6a7282]">PDF, JPG, PNG (Tối đa 5MB)</p>
            </div>

            <!-- Account Status -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">
                Trạng thái tài khoản
              </label>
              <div class="flex items-center gap-3">
                <label class="inline-flex items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="formData.status"
                    value="active"
                    class="hidden"
                  />
                  <span
                    :class="
                      formData.status === 'active'
                        ? 'bg-[#5a9690] text-white px-3 py-1 rounded-lg'
                        : 'bg-[#f3f3f5] text-[#364153] px-3 py-1 rounded-lg'
                    "
                    >Hoạt động</span
                  >
                </label>

                <label class="inline-flex items-center cursor-pointer">
                  <input
                    type="radio"
                    v-model="formData.status"
                    value="locked"
                    class="hidden"
                  />
                  <span
                    :class="
                      formData.status === 'locked'
                        ? 'bg-[#e53e3e] text-white px-3 py-1 rounded-lg'
                        : 'bg-[#f3f3f5] text-[#364153] px-3 py-1 rounded-lg'
                    "
                    >Đã khóa</span
                  >
                </label>
              </div>
              <p class="text-xs text-[#6a7282]">
                Chọn trạng thái cho tài khoản khi tạo.
              </p>
            </div>
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex gap-2 justify-end mt-4 pt-4 border-t border-gray-200">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg h-9 px-[17px] py-[9px] flex items-center justify-center hover:bg-gray-50 transition-colors"
          >
            <span class="font-medium text-sm text-neutral-950">Hủy</span>
          </button>
          <button
            @click="handleSubmit"
            class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 flex items-center justify-center hover:bg-[#5a9690]/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!isFormValid"
          >
            <span class="font-medium text-sm text-white">Tạo tài khoản</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import api, { attachToken } from "@/utils/api";
import { createNhanVien } from "@/utils/nhanVien";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

// Emits
const emit = defineEmits(["close", "submit"]);

// Form Data
const formData = ref({
  fullName: "",
  email: "",
  phone: "",
  address: "",
  avatar: null,
  selectedRoles: [], // selected roles (empty by default)
  department: "",
  position: "",
  yearsOfExperience: null,
  practiceCertificate: null,
  professionalDegree: null,
  password: "",
  passwordConfirmation: "",
  status: "active",
});

// Role options
const roleOptions = ["Bác sĩ", "Y tá"];

// Computed
const isFormValid = computed(() => {
  return (
    formData.value.fullName &&
    formData.value.email &&
    formData.value.phone &&
    formData.value.selectedRoles.length > 0 &&
    formData.value.password &&
    formData.value.password.length >= 8 &&
    formData.value.passwordConfirmation &&
    formData.value.password === formData.value.passwordConfirmation
  );
});

// Methods
const isSubmitting = ref(false);

// File input refs and previews
const avatarInput = ref(null);
const avatarPreview = ref(null);
const avatarName = ref("");
const practiceInput = ref(null);
const practiceFileName = ref("");
const degreeInput = ref(null);
const degreeFileName = ref("");

const toggleRole = (role) => {
  const idx = formData.value.selectedRoles.indexOf(role);
  if (idx === -1) formData.value.selectedRoles.push(role);
  else formData.value.selectedRoles.splice(idx, 1);
};
const removeRole = (index) => {
  formData.value.selectedRoles.splice(index, 1);
};

const triggerAvatarInput = () => avatarInput.value?.click();
const triggerPracticeInput = () => practiceInput.value?.click();
const triggerDegreeInput = () => degreeInput.value?.click();

const handleAvatarChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  // limit 2MB
  if (file.size > 2 * 1024 * 1024) {
    showErrorToast("Lỗi", "Ảnh phải nhỏ hơn 2MB");
    return;
  }
  formData.value.avatar = file;
  avatarName.value = file.name;
  const reader = new FileReader();
  reader.onload = (ev) => (avatarPreview.value = ev.target.result);
  reader.readAsDataURL(file);
};

const handlePracticeChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    showErrorToast("Lỗi", "File phải nhỏ hơn 5MB");
    return;
  }
  formData.value.practiceCertificate = file;
  practiceFileName.value = file.name;
};

const handleDegreeChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  if (file.size > 5 * 1024 * 1024) {
    showErrorToast("Lỗi", "File phải nhỏ hơn 5MB");
    return;
  }
  formData.value.professionalDegree = file;
  degreeFileName.value = file.name;
};

const handleSubmit = async () => {
  if (!isFormValid.value || isSubmitting.value) return;
  isSubmitting.value = true;
  try {
    // attach token
    try {
      attachToken();
    } catch (e) {}

    // If files present, upload and replace with returned URLs before calling createNhanVien
    // Upload avatar
    if (formData.value.avatar) {
      try {
        const fd = new FormData();
        fd.append("file", formData.value.avatar);
        const upRes = await api.post("/upload", fd, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        let avatarPath = null;
        if (upRes && upRes.data) {
          avatarPath =
            (upRes.data.data &&
              (upRes.data.data.path || upRes.data.data.url)) ||
            upRes.data.path ||
            upRes.data.url ||
            null;
        }
        if (avatarPath && !/^https?:\/\//i.test(avatarPath)) {
          const API_BASE =
            import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";
          const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");
          if (!avatarPath.startsWith("/")) avatarPath = "/" + avatarPath;
          avatarPath = API_ORIGIN + avatarPath;
        }
        formData.value.avatar = avatarPath;
      } catch (ue) {
        showErrorToast("Lỗi upload", "Không thể tải ảnh đại diện lên.");
        isSubmitting.value = false;
        return;
      }
    }

    // Upload practice certificate
    if (formData.value.practiceCertificate) {
      try {
        const fd = new FormData();
        fd.append("file", formData.value.practiceCertificate);
        const upRes = await api.post("/upload", fd, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        let pPath = null;
        if (upRes && upRes.data)
          pPath =
            (upRes.data.data &&
              (upRes.data.data.path || upRes.data.data.url)) ||
            upRes.data.path ||
            upRes.data.url ||
            null;
        if (pPath && !/^https?:\/\//i.test(pPath)) {
          const API_BASE =
            import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";
          const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");
          if (!pPath.startsWith("/")) pPath = "/" + pPath;
          pPath = API_ORIGIN + pPath;
        }
        formData.value.practiceCertificate = pPath;
      } catch (ue) {
        showErrorToast("Lỗi upload", "Không thể tải chứng chỉ hành nghề lên.");
        isSubmitting.value = false;
        return;
      }
    }

    // Upload professional degree
    if (formData.value.professionalDegree) {
      try {
        const fd = new FormData();
        fd.append("file", formData.value.professionalDegree);
        const upRes = await api.post("/upload", fd, {
          headers: { "Content-Type": "multipart/form-data" },
        });
        let dPath = null;
        if (upRes && upRes.data)
          dPath =
            (upRes.data.data &&
              (upRes.data.data.path || upRes.data.data.url)) ||
            upRes.data.path ||
            upRes.data.url ||
            null;
        if (dPath && !/^https?:\/\//i.test(dPath)) {
          const API_BASE =
            import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";
          const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");
          if (!dPath.startsWith("/")) dPath = "/" + dPath;
          dPath = API_ORIGIN + dPath;
        }
        formData.value.professionalDegree = dPath;
      } catch (ue) {
        showErrorToast("Lỗi upload", "Không thể tải bằng cấp lên.");
        isSubmitting.value = false;
        return;
      }
    }

    const res = await createNhanVien(formData.value);
    showSuccessToast("Thành công", "Tạo nhân viên thành công.");
    // emit submit with returned data if available
    emit("submit", res && res.data ? res.data : formData.value);
    // close modal
    emit("close");
  } catch (err) {
    const msg =
      err && err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : "Có lỗi xảy ra khi tạo nhân viên.";
    showErrorToast("Lỗi", msg);
    console.error("createNhanVien error", err);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* Custom scrollbar for modal */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #e5e7eb;
  border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: #d1d5db;
}
</style>
