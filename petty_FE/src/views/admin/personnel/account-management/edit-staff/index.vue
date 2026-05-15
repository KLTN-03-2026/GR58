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
          <h2 class="font-semibold text-lg text-black">Chỉnh sửa nhân viên</h2>
          <p class="text-sm text-gray-500">
            Cập nhật thông tin và phân quyền cho nhân viên
          </p>
        </div>

        <!-- Form Content - 2 Column Layout -->
        <div class="grid grid-cols-2 gap-6 flex-1 overflow-y-auto pr-2">
          <!-- Left Column -->
          <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Họ và tên *</label>
              <input
                v-model="formData.fullName"
                type="text"
                placeholder="VD: BS. Nguyễn Văn A"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Email *</label>
              <input
                v-model="formData.email"
                type="email"
                placeholder="email@vcms.vn"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Số điện thoại *</label>
              <input
                v-model="formData.phone"
                type="tel"
                placeholder="0901234567"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Địa chỉ *</label>
              <input
                v-model="formData.address"
                type="text"
                placeholder="VD: 123 Nguyễn Huệ, Quận 1, TP.HCM"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Chức danh *</label>
              <input
                v-model="formData.position"
                type="text"
                placeholder="VD: Trưởng khoa"
                class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182]"
              />
            </div>

            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Năm kinh nghiệm *</label>
              <div class="flex items-center gap-2">
                <input
                  v-model.number="formData.yearsOfExperience"
                  type="number"
                  min="0"
                  placeholder="VD: 5"
                  class="bg-[#f3f3f5] border-none rounded-lg h-9 px-3 py-1 font-nunito text-sm text-neutral-950 outline-none placeholder:text-[#717182] w-32"
                />
                <span class="text-sm text-[#4a5565]">năm</span>
              </div>
            </div>
          </div>

          <!-- Right Column -->
          <div class="flex flex-col gap-4">
            <!-- System Roles -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Vai trò hệ thống *</label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="role in roleOptions"
                  :key="role"
                  type="button"
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
              <p class="text-xs text-[#6a7282]">
                Chọn vai trò: Bác sĩ hoặc Y tá.
              </p>
            </div>

            <!-- Avatar Upload -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Ảnh đại diện</label>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerAvatarInput"
                  class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                >
                  <span class="font-medium text-sm text-neutral-950">Đổi ảnh</span>
                </button>
                <div v-if="avatarName" class="text-sm text-[#4a5565]">{{ avatarName }}</div>
                <div v-else-if="formData.avatar" class="text-sm text-[#4a5565]">Đã có ảnh</div>
              </div>
              <input
                ref="avatarInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleAvatarChange"
              />
              <p class="text-xs text-[#6a7282]">JPG, PNG (Tối đa 2MB). Để trống nếu giữ nguyên.</p>
            </div>

            <!-- Practice Certificate -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Chứng chỉ hành nghề *</label>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerPracticeInput"
                  class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                >
                  <span class="font-medium text-sm text-neutral-950">Tải lên file mới</span>
                </button>
                <div v-if="practiceFileName" class="text-sm text-[#4a5565]">{{ practiceFileName }}</div>
                <div v-else-if="formData.practiceCertificate" class="text-sm text-[#4a5565] truncate max-w-[200px]">
                  {{ shortFileName(formData.practiceCertificate) }}
                </div>
              </div>
              <input
                ref="practiceInput"
                type="file"
                accept="application/pdf,image/*"
                class="hidden"
                @change="handlePracticeChange"
              />
              <p class="text-xs text-[#6a7282]">PDF, JPG, PNG (Tối đa 5MB). Để trống nếu giữ nguyên.</p>
            </div>

            <!-- Professional Degree -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Bằng cấp chuyên môn *</label>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="triggerDegreeInput"
                  class="bg-white border !border-gray-300 rounded-lg h-9 px-3 flex items-center gap-2 hover:bg-gray-50 transition-colors"
                >
                  <span class="font-medium text-sm text-neutral-950">Tải lên file mới</span>
                </button>
                <div v-if="degreeFileName" class="text-sm text-[#4a5565]">{{ degreeFileName }}</div>
                <div v-else-if="formData.professionalDegree" class="text-sm text-[#4a5565] truncate max-w-[200px]">
                  {{ shortFileName(formData.professionalDegree) }}
                </div>
              </div>
              <input
                ref="degreeInput"
                type="file"
                accept="application/pdf,image/*"
                class="hidden"
                @change="handleDegreeChange"
              />
              <p class="text-xs text-[#6a7282]">PDF, JPG, PNG (Tối đa 5MB). Để trống nếu giữ nguyên.</p>
            </div>

            <!-- Account Status -->
            <div class="flex flex-col gap-2">
              <label class="font-medium text-sm text-black">Trạng thái tài khoản</label>
              <div class="flex items-center gap-3">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="radio" v-model="formData.status" value="active" class="hidden" />
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
                  <input type="radio" v-model="formData.status" value="locked" class="hidden" />
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
            </div>
          </div>
        </div>

        <!-- Footer Buttons -->
        <div class="flex gap-2 justify-end mt-4 pt-4 border-t border-gray-200">
          <button
            type="button"
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg h-9 px-[17px] py-[9px] flex items-center justify-center hover:bg-gray-50 transition-colors"
          >
            <span class="font-medium text-sm text-neutral-950">Hủy</span>
          </button>
          <button
            type="button"
            @click="handleSubmit"
            class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 flex items-center justify-center hover:bg-[#5a9690]/80 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="!isFormValid || isSubmitting"
          >
            <span class="font-medium text-sm text-white">
              {{ isSubmitting ? "Đang lưu..." : "Lưu thay đổi" }}
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import api, { attachToken } from "@/utils/api";
import { updateNhanVien } from "@/utils/nhanVien";
import { showSuccessToast, showErrorToast } from "@/utils/toast";

const props = defineProps({
  staff: {
    type: Object,
    required: true,
  },
});

const emit = defineEmits(["close", "updated"]);

const roleOptions = ["Bác sĩ", "Y tá"];

const formData = ref({
  fullName: "",
  email: "",
  phone: "",
  address: "",
  avatar: null, // string url or File
  selectedRoles: [],
  position: "",
  yearsOfExperience: null,
  practiceCertificate: null, // string url or File
  professionalDegree: null,
  status: "active",
});

const avatarInput = ref(null);
const avatarName = ref("");
const practiceInput = ref(null);
const practiceFileName = ref("");
const degreeInput = ref(null);
const degreeFileName = ref("");
const isSubmitting = ref(false);

const roleSlugToLabel = (slug) => {
  if (!slug) return null;
  if (slug === "bac_si") return "Bác sĩ";
  if (slug === "y_ta") return "Y tá";
  return null;
};

// Prefill from incoming staff data (supports both backend keys and FE-mapped keys)
const fillFromStaff = (s) => {
  if (!s) return;
  formData.value.fullName = s.full_name || s.name || "";
  formData.value.email = s.email || "";
  formData.value.phone = s.so_dien_thoai || s.phone || "";
  formData.value.address = s.dia_chi || s.address || "";
  formData.value.avatar = s.anh_dai_dien || s.avatar || null;
  formData.value.position = s.chuc_danh || s.position || "";
  formData.value.yearsOfExperience =
    s.nam_kinh_nghiem ?? s.yearsOfExperience ?? null;
  formData.value.practiceCertificate =
    s.chung_chi_hanh_nghe || s.practiceCertificate || null;
  formData.value.professionalDegree =
    s.bang_cap_chuyen_mon || s.professionalDegree || null;

  // Role
  let roleLabel = roleSlugToLabel(s.vai_tro);
  if (!roleLabel && Array.isArray(s.roles) && s.roles.length) {
    roleLabel = s.roles[0]?.name || null;
  }
  formData.value.selectedRoles = roleLabel ? [roleLabel] : [];

  // Status
  const rawStatus = s.trang_thai || s.status || "active";
  formData.value.status =
    rawStatus === "hoat_dong" || rawStatus === "active"
      ? "active"
      : "locked";
};

watch(
  () => props.staff,
  (val) => fillFromStaff(val),
  { immediate: true, deep: true }
);

const isFormValid = computed(() => {
  return (
    formData.value.fullName &&
    formData.value.email &&
    formData.value.phone &&
    formData.value.address &&
    formData.value.position &&
    formData.value.yearsOfExperience !== null &&
    formData.value.yearsOfExperience !== "" &&
    formData.value.practiceCertificate &&
    formData.value.professionalDegree &&
    formData.value.avatar &&
    formData.value.selectedRoles.length > 0
  );
});

const toggleRole = (role) => {
  if (formData.value.selectedRoles.includes(role)) {
    formData.value.selectedRoles = [];
  } else {
    formData.value.selectedRoles = [role];
  }
};

const triggerAvatarInput = () => avatarInput.value?.click();
const triggerPracticeInput = () => practiceInput.value?.click();
const triggerDegreeInput = () => degreeInput.value?.click();

const handleAvatarChange = (e) => {
  const file = e.target.files?.[0];
  if (!file) return;
  if (file.size > 2 * 1024 * 1024) {
    showErrorToast("Lỗi", "Ảnh phải nhỏ hơn 2MB");
    return;
  }
  formData.value.avatar = file;
  avatarName.value = file.name;
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

const shortFileName = (urlOrName) => {
  if (!urlOrName || typeof urlOrName !== "string") return "";
  const parts = urlOrName.split("/");
  return parts[parts.length - 1] || urlOrName;
};

// Upload a File and return its absolute URL string
const uploadFile = async (file) => {
  const fd = new FormData();
  fd.append("file", file);
  const upRes = await api.post("/upload", fd, {
    headers: { "Content-Type": "multipart/form-data" },
  });
  let p = null;
  if (upRes && upRes.data) {
    p =
      (upRes.data.data && (upRes.data.data.path || upRes.data.data.url)) ||
      upRes.data.path ||
      upRes.data.url ||
      null;
  }
  if (p && !/^https?:\/\//i.test(p)) {
    const API_BASE =
      import.meta.env.VITE_API_BASE || "http://localhost:8001/api";
    const API_ORIGIN = API_BASE.replace(/\/api\/?$/, "");
    if (!p.startsWith("/")) p = "/" + p;
    p = API_ORIGIN + p;
  }
  return p;
};

const handleSubmit = async () => {
  if (!isFormValid.value || isSubmitting.value) return;
  if (!props.staff?.id) {
    showErrorToast("Lỗi", "Thiếu id nhân viên.");
    return;
  }

  isSubmitting.value = true;
  try {
    try {
      attachToken();
    } catch (e) {}

    // Upload any new files (avatar / practice certificate / degree)
    if (formData.value.avatar instanceof File) {
      try {
        formData.value.avatar = await uploadFile(formData.value.avatar);
      } catch (ue) {
        showErrorToast(
          "Lỗi upload",
          ue?.response?.data?.message || "Không thể tải ảnh đại diện."
        );
        isSubmitting.value = false;
        return;
      }
    }
    if (formData.value.practiceCertificate instanceof File) {
      try {
        formData.value.practiceCertificate = await uploadFile(
          formData.value.practiceCertificate
        );
      } catch (ue) {
        showErrorToast(
          "Lỗi upload",
          ue?.response?.data?.message || "Không thể tải chứng chỉ hành nghề."
        );
        isSubmitting.value = false;
        return;
      }
    }
    if (formData.value.professionalDegree instanceof File) {
      try {
        formData.value.professionalDegree = await uploadFile(
          formData.value.professionalDegree
        );
      } catch (ue) {
        showErrorToast(
          "Lỗi upload",
          ue?.response?.data?.message || "Không thể tải bằng cấp."
        );
        isSubmitting.value = false;
        return;
      }
    }

    const res = await updateNhanVien(props.staff.id, formData.value);
    showSuccessToast("Thành công", "Cập nhật nhân viên thành công.");
    emit("updated", res && res.data ? res.data : null);
    emit("close");
  } catch (err) {
    const msg =
      err?.response?.data?.message || "Có lỗi xảy ra khi cập nhật nhân viên.";
    showErrorToast("Lỗi", msg);
    console.error("updateNhanVien error", err);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
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
