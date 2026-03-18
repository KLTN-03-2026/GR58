<template>
  <div
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[1000] p-4"
  >
    <div
      class="bg-white border !border-gray-300 rounded-[10px] shadow-xl w-full max-w-[495px] max-h-[90vh] flex flex-col"
    >
      <!-- Fixed Header -->
      <div class="flex-shrink-0 p-6 pb-4 border-b border-gray-200">
        <div class="flex flex-col gap-2">
          <h2
            class="font-nunito font-semibold text-lg leading-[18px] text-neutral-950 tracking-tight"
          >
            Chi tiết Nhân viên
          </h2>
          <p
            class="font-nunito text-sm leading-5 text-[#717182] tracking-tight"
          >
            Thông tin đầy đủ về nhân viên
          </p>
        </div>
      </div>

      <!-- Scrollable Content -->
      <div class="flex-1 overflow-y-auto px-6 py-4">
        <div class="flex flex-col">
          <!-- Avatar -->
          <div class="flex justify-center mb-6">
            <div
              class="border-4 border-[#cbfbf1] rounded-full w-24 h-24 overflow-hidden"
            >
              <img
                :src="displayAvatar"
                alt="Avatar"
                class="w-full h-full object-cover"
              />
            </div>
          </div>

          <!-- Basic Information Section -->
          <div class="flex flex-col gap-3 mb-6">
            <div class="flex items-center gap-3 h-5">
              <div class="bg-[#009689] w-1 h-5 rounded"></div>
              <h3
                class="font-nunito font-medium text-sm leading-5 text-[#101828] tracking-tight"
              >
                Thông tin cơ bản
              </h3>
            </div>
            <div class="bg-gray-50 rounded-[10px] p-4 grid grid-cols-2 gap-4">
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Họ và tên</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{ displayFullName }}</span
                >
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Email</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{ staff.email }}</span
                >
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Số điện thoại</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{ displayPhone || "Chưa cập nhật" }}</span
                >
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Địa chỉ</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{ displayAddress || "Chưa cập nhật" }}</span
                >
              </div>
            </div>
          </div>

          <!-- Role & Assignment Section -->
          <div class="flex flex-col gap-3 mb-6">
            <div class="flex items-center gap-3 h-5">
              <div class="bg-[#009689] w-1 h-5 rounded"></div>
              <h3
                class="font-nunito font-medium text-sm leading-5 text-[#101828] tracking-tight"
              >
                Vai trò & Phân công
              </h3>
            </div>
            <div class="bg-gray-50 rounded-[10px] p-4 flex flex-col gap-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                  <span
                    class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                    >Vai trò hệ thống</span
                  >
                  <div class="mt-2">
                    <template v-if="staff.vai_tro">
                      <span
                        class="inline-flex items-center justify-center px-2 py-[3px] rounded-lg text-xs leading-4 font-nunito font-medium mr-1 bg-blue-50 border border-[#8ec5ff] text-[#1447e6]"
                      >
                        {{ displayVaiTro }}
                      </span>
                    </template>
                    <template v-else>
                      <span
                        v-for="(role, index) in staff.roles"
                        :key="index"
                        :class="[
                          'inline-flex items-center justify-center px-2 py-[3px] rounded-lg text-xs leading-4 font-nunito font-medium mr-1',
                          role.color === 'blue'
                            ? 'bg-blue-50 border border-[#8ec5ff] text-[#1447e6]'
                            : role.color === 'green'
                            ? 'bg-green-100 text-[#008236]'
                            : 'bg-purple-100 text-[#8200db]',
                        ]"
                      >
                        {{ role.name }}
                      </span>
                    </template>
                  </div>
                </div>
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Chức danh</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{ displayPosition || "Chưa cập nhật" }}</span
                >
              </div>
            </div>
          </div>

          <!-- Education & Experience Section -->
          <div class="flex flex-col gap-3 mb-6">
            <div class="flex items-center gap-3 h-5">
              <div class="bg-[#009689] w-1 h-5 rounded"></div>
              <h3
                class="font-nunito font-medium text-sm leading-5 text-[#101828] tracking-tight"
              >
                Trình độ & Kinh nghiệm
              </h3>
            </div>
            <div class="bg-gray-50 rounded-[10px] p-4 flex flex-col gap-4">
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Năm kinh nghiệm</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{
                    displayYears ? displayYears + " năm" : "Chưa cập nhật"
                  }}</span
                >
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight mb-2"
                  >Chứng chỉ hành nghề</span
                >
                <button
                  v-if="displayPracticeCertificate"
                  class="bg-white border border-[#96f7e4] rounded-lg h-8 px-3 flex items-center gap-2 w-fit hover:bg-gray-50 transition-colors"
                >
                  <img :src="iconDocument" alt="Document" class="w-4 h-4" />
                  <span
                    class="font-nunito font-medium text-sm leading-5 text-[#009689] tracking-tight"
                  >
                    {{ displayPracticeCertificate }}
                  </span>
                </button>
                <span
                  v-else
                  class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
                  >Chưa cập nhật</span
                >
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight mb-2"
                  >Bằng cấp chuyên môn</span
                >
                <button
                  v-if="displayProfessionalDegree"
                  class="bg-white border border-[#96f7e4] rounded-lg h-8 px-3 flex items-center gap-2 w-fit hover:bg-gray-50 transition-colors"
                >
                  <img :src="iconDocument" alt="Document" class="w-4 h-4" />
                  <span
                    class="font-nunito font-medium text-sm leading-5 text-[#009689] tracking-tight"
                  >
                    {{ displayProfessionalDegree }}
                  </span>
                </button>
                <span
                  v-else
                  class="font-nunito text-sm leading-5 text-[#6a7282] tracking-tight"
                  >Chưa cập nhật</span
                >
              </div>
            </div>
          </div>

          <!-- Account Status Section -->
          <div class="flex flex-col gap-3">
            <div class="flex items-center gap-3 h-5">
              <div class="bg-[#009689] w-1 h-5 rounded"></div>
              <h3
                class="font-nunito font-medium text-sm leading-5 text-[#101828] tracking-tight"
              >
                Trạng thái Tài khoản
              </h3>
            </div>
            <div class="bg-gray-50 rounded-[10px] p-4 flex flex-col gap-4">
              <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                  <span
                    class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                    >Ngày gia nhập</span
                  >
                  <span
                    class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                    >{{ staff.joinDate }}</span
                  >
                </div>
                <div class="flex flex-col">
                  <span
                    class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                    >Trạng thái</span
                  >
                  <span
                    :class="[
                      'inline-flex items-center gap-2 px-2 py-[3px] rounded-lg text-xs leading-4 font-nunito font-medium mt-[1px] w-fit',
                      isActive
                        ? 'bg-green-100 text-[#008236]'
                        : 'bg-gray-200 text-[#364153]',
                    ]"
                  >
                    {{ displayStatusLabel }}
                  </span>
                </div>
              </div>
              <div class="flex flex-col">
                <span
                  class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                  >Lần đăng nhập cuối</span
                >
                <span
                  class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                  >{{ staff.lastLogin }}</span
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Fixed Footer -->
      <div class="flex-shrink-0 p-6 pt-4 border-t border-gray-200">
        <div class="flex items-center justify-end gap-2">
          <button
            @click="$emit('close')"
            class="bg-white border !border-gray-300 rounded-lg px-4 py-2 hover:bg-gray-50 transition-colors"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
              >Đóng</span
            >
          </button>
          <button
            @click="$emit('edit', staff)"
            class="bg-[#5a9690] rounded-lg px-4 py-2 hover:bg-[#5a9690]/80 transition-colors"
          >
            <span
              class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
              >Chỉnh sửa</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";

// Props
const props = defineProps({
  staff: {
    type: Object,
    default: () => ({
      // include both frontend and backend sample keys for compatibility
      avatar:
        "https://www.figma.com/api/mcp/asset/192ea54b-05f0-477e-b541-a07eb038b747",
      anh_dai_dien:
        "https://www.figma.com/api/mcp/asset/192ea54b-05f0-477e-b541-a07eb038b747",
      name: "BS. Nguyễn Văn A",
      full_name: "BS. Nguyễn Văn A",
      email: "bsnguyen@vcms.vn",
      phone: "0901234567",
      address: "123 Nguyễn Huệ, Quận 1, TP.HCM",
      roles: [{ name: "Bác sĩ", color: "blue" }],
      department: "Khoa Nội",
      position: "Bác sĩ chuyên khoa cấp I",
      chuc_danh: "Bác sĩ chuyên khoa cấp I",
      yearsOfExperience: 8,
      nam_kinh_nghiem: 8,
      practiceCertificate: "Chứng chỉ hành nghề số 12345.pdf",
      chung_chi_hanh_nghe: "Chứng chỉ hành nghề số 12345.pdf",
      professionalDegree: "Bằng Bác sĩ Đa khoa - ĐH Y Hà Nội.pdf",
      bang_cap_chuyen_mon: "Bằng Bác sĩ Đa khoa - ĐH Y Hà Nội.pdf",
      joinDate: "2024-01-15",
      status: "active",
      trang_thai: "hoat_dong",
      lastLogin: "Vừa xong",
    }),
  },
});

// Emits
const emit = defineEmits(["close", "edit"]);

// Icons (from Figma - expire in 7 days)
const iconDocument =
  "https://www.figma.com/api/mcp/asset/1999c559-f5f3-4ca8-9746-18cb477ea7ae";
const iconCheck =
  "https://www.figma.com/api/mcp/asset/88e79f47-07af-4ad7-9625-fd73fe0e00fe";
const iconX =
  "https://www.figma.com/api/mcp/asset/1d86c67f-830e-4ef0-a6fe-45849993acf4";

// Computed display helpers to support both current frontend keys and backend keys
const displayAvatar = computed(
  () => props.staff.anh_dai_dien || props.staff.avatar || ""
);
const displayFullName = computed(
  () => props.staff.full_name || props.staff.name || ""
);
const displayVaiTro = computed(() => {
  if (props.staff.vai_tro) {
    if (props.staff.vai_tro === "bac_si") return "Bác sĩ";
    if (props.staff.vai_tro === "y_ta") return "Y tá";
    return props.staff.vai_tro;
  }
  // fallback to roles array
  if (Array.isArray(props.staff.roles) && props.staff.roles.length)
    return props.staff.roles[0].name;
  return "—";
});
const displayPosition = computed(
  () => props.staff.chuc_danh || props.staff.position || ""
);
// prefer backend 'nam_kinh_nghiem' then 'yearsOfExperience'; return null when absent so template can show a fallback
const displayYears = computed(() => {
  const v = props.staff.nam_kinh_nghiem ?? props.staff.yearsOfExperience;
  return typeof v === "number" && v >= 0 ? v : v ? v : null;
});

// Phone/address fallbacks (handle multiple possible key names)
const displayPhone = computed(() => {
  return (
    props.staff.phone ||
    props.staff.so_dien_thoai ||
    props.staff.sdt ||
    props.staff.phone_number ||
    ""
  );
});
const displayAddress = computed(() => {
  return props.staff.address || props.staff.dia_chi || "";
});
const displayPracticeCertificate = computed(
  () => props.staff.chung_chi_hanh_nghe || props.staff.practiceCertificate || ""
);
const displayProfessionalDegree = computed(
  () => props.staff.bang_cap_chuyen_mon || props.staff.professionalDegree || ""
);

// status mapping: backend 'trang_thai' uses 'hoat_dong'/'da_khoa'; frontend used 'active'
const isActive = computed(() => {
  const s = (props.staff.trang_thai || props.staff.status || "")
    .toString()
    .toLowerCase();
  return s === "hoat_dong" || s === "active";
});
const displayStatusLabel = computed(() =>
  isActive.value ? "Hoạt động" : "Đã khóa"
);
const displayStatusIcon = computed(() => (isActive.value ? iconCheck : iconX));
</script>

<style scoped>
/* Custom scrollbar for modal */
div::-webkit-scrollbar {
  width: 8px;
}

div::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 10px;
}

div::-webkit-scrollbar-thumb:hover {
  background: #555;
}
</style>
