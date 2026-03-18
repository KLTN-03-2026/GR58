<template>
  <div class="flex flex-col gap-6 h-full px-8 py-6">
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-12">
      <svg
        class="animate-spin h-8 w-8 text-[#009689]"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
      >
        <circle
          class="opacity-25"
          cx="12"
          cy="12"
          r="10"
          stroke="currentColor"
          stroke-width="4"
        ></circle>
        <path
          class="opacity-75"
          fill="currentColor"
          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        ></path>
      </svg>
    </div>

    <!-- Main Content -->
    <template v-else>
      <!-- Header -->
      <div class="flex items-center justify-between h-[60px]">
        <div class="flex flex-col gap-2">
          <h1 class="text-2xl font-semibold text-black">Phân quyền</h1>
          <p class="text-gray-500 font-medium text-base">
            Quản lý vai trò và quyền hạn truy cập
          </p>
        </div>
        <button
          @click="handleSaveAll"
          :disabled="saving"
          class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            ></path>
          </svg>
          <span class="text-sm font-medium">{{
            saving ? "Đang lưu..." : "Lưu tất cả thay đổi"
          }}</span>
        </button>
      </div>

      <!-- Roles List -->
      <div class="grid grid-cols-1 gap-4">
        <div
          v-for="role in roles"
          :key="role.id"
          class="bg-white rounded-[14px] border !border-gray-300 shadow-sm overflow-hidden"
        >
          <!-- Role Header -->
          <div
            @click="toggleRole(role.id)"
            class="flex items-center justify-between px-6 py-4 cursor-pointer hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-center gap-4">
              <div
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-white font-medium',
                  getRoleColor(role.ma_vai_tro),
                ]"
              >
                {{ getRoleIcon(role.ma_vai_tro) }}
              </div>
              <div>
                <h3 class="text-lg font-medium text-[#101828]">
                  {{ role.ten_vai_tro }}
                </h3>
                <p class="text-sm text-[#4a5565]">
                  {{ getRoleDescription(role.ma_vai_tro) }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-sm text-[#4a5565]">
                {{ countActivePermissions(role) }} quyền được bật
              </span>
              <svg
                :class="[
                  'w-5 h-5 text-gray-400 transition-transform',
                  expandedRoles.includes(role.id) ? 'rotate-180' : '',
                ]"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M19 9l-7 7-7-7"
                ></path>
              </svg>
            </div>
          </div>

          <!-- Permissions Grid -->
          <div
            v-show="expandedRoles.includes(role.id)"
            class="px-6 pb-6 border-t !border-gray-200"
          >
            <div
              class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-6"
            >
              <div
                v-for="(group, groupKey) in permissions"
                :key="groupKey"
                class="flex flex-col gap-3"
              >
                <!-- Group Header -->
                <div
                  class="flex items-center gap-2 pb-2 border-b !border-gray-300"
                >
                  <svg
                    class="w-4 h-4 text-[#009689]"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5z"
                    ></path>
                  </svg>
                  <h4 class="text-sm font-medium text-[#101828]">
                    {{ group.label }}
                  </h4>
                </div>

                <!-- Permissions Checkboxes -->
                <div class="flex flex-col gap-2">
                  <label
                    v-for="(permLabel, permKey) in group.permissions"
                    :key="permKey"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                  >
                    <input
                      type="checkbox"
                      :checked="role[permKey]"
                      @change="
                        updatePermission(role, permKey, $event.target.checked)
                      "
                      class="w-4 h-4 rounded border-2 border-gray-300 text-[#009689] focus:ring-[#009689] cursor-pointer"
                    />
                    <span class="text-sm text-[#364153]">{{ permLabel }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div
              class="flex items-center gap-3 mt-6 pt-6 border-t !border-gray-200"
            >
              <button
                @click="enableAllPermissions(role)"
                class="px-3 py-1.5 text-sm bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors"
              >
                Bật tất cả
              </button>
              <button
                @click="disableAllPermissions(role)"
                class="px-3 py-1.5 text-sm bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors"
              >
                Tắt tất cả
              </button>
              <button
                @click="saveRolePermissions(role)"
                :disabled="saving"
                class="ml-auto px-3 py-1.5 text-sm bg-[#5a9690] hover:bg-[#5a9690]/80 text-white rounded-lg transition-colors disabled:bg-gray-400"
              >
                {{ saving ? "Đang lưu..." : "Lưu vai trò này" }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Info Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="bg-blue-50 border !border-blue-200 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <svg
              class="w-5 h-5 text-blue-600 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                clip-rule="evenodd"
              ></path>
            </svg>
            <div>
              <h4 class="text-sm font-medium text-blue-900 mb-1">Admin</h4>
              <p class="text-xs text-blue-700">
                Có toàn quyền quản lý hệ thống, cấu hình và phân quyền
              </p>
            </div>
          </div>
        </div>

        <div class="bg-green-50 border !border-green-200 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <svg
              class="w-5 h-5 text-green-600 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
              ></path>
            </svg>
            <div>
              <h4 class="text-sm font-medium text-green-900 mb-1">Bác sĩ</h4>
              <p class="text-xs text-green-700">
                Quản lý lịch hẹn, bệnh án, kê đơn thuốc
              </p>
            </div>
          </div>
        </div>

        <div class="bg-purple-50 border !border-purple-200 rounded-lg p-4">
          <div class="flex items-start gap-3">
            <svg
              class="w-5 h-5 text-purple-600 mt-0.5"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"
              ></path>
            </svg>
            <div>
              <h4 class="text-sm font-medium text-purple-900 mb-1">
                Nhân viên
              </h4>
              <p class="text-xs text-purple-700">
                Xem lịch, quản lý khách hàng, thanh toán
              </p>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { phanQuyenAPI } from "@/utils/phanQuyen";

const roles = ref([]);
const permissions = ref({});
const loading = ref(false);
const saving = ref(false);
const expandedRoles = ref([]);

// Load data
const loadData = async () => {
  loading.value = true;
  try {
    // Load roles
    const rolesResponse = await phanQuyenAPI.getAll();
    if (rolesResponse.data.success) {
      roles.value = rolesResponse.data.data;
      // Expand first role by default
      if (roles.value.length > 0) {
        expandedRoles.value = [roles.value[0].id];
      }
    }

    // Load all permissions
    const permissionsResponse = await phanQuyenAPI.getAllPermissions();
    if (permissionsResponse.data.success) {
      permissions.value = permissionsResponse.data.data;
    }
  } catch (error) {
    console.error("Error loading data:", error);
    alert("Có lỗi xảy ra khi tải dữ liệu");
  } finally {
    loading.value = false;
  }
};

// Toggle role expansion
const toggleRole = (roleId) => {
  const index = expandedRoles.value.indexOf(roleId);
  if (index > -1) {
    expandedRoles.value.splice(index, 1);
  } else {
    expandedRoles.value.push(roleId);
  }
};

// Get role color based on role code
const getRoleColor = (roleCode) => {
  const colors = {
    admin: "bg-blue-600",
    bac_si: "bg-green-600",
    nhan_vien: "bg-purple-600",
    y_ta: "bg-pink-600",
    le_tan: "bg-orange-600",
  };
  return colors[roleCode] || "bg-gray-600";
};

// Get role icon
const getRoleIcon = (roleCode) => {
  const icons = {
    admin: "👑",
    bac_si: "👨‍⚕️",
    nhan_vien: "👤",
    y_ta: "👩‍⚕️",
    le_tan: "📋",
  };
  return icons[roleCode] || "👤";
};

// Get role description
const getRoleDescription = (roleCode) => {
  const descriptions = {
    admin: "Quản trị viên - Toàn quyền hệ thống",
    bac_si: "Bác sĩ - Khám bệnh, kê đơn",
    nhan_vien: "Nhân viên - Hỗ trợ và quản lý",
    y_ta: "Y tá - Hỗ trợ bác sĩ",
    le_tan: "Lễ tân - Tiếp nhận khách hàng",
  };
  return descriptions[roleCode] || "Vai trò tùy chỉnh";
};

// Count active permissions
const countActivePermissions = (role) => {
  let count = 0;
  for (const group of Object.values(permissions.value)) {
    for (const permKey of Object.keys(group.permissions)) {
      if (role[permKey] === 1 || role[permKey] === true) {
        count++;
      }
    }
  }
  return count;
};

// Update single permission
const updatePermission = (role, permKey, value) => {
  role[permKey] = value ? 1 : 0;
};

// Enable all permissions for a role
const enableAllPermissions = (role) => {
  for (const group of Object.values(permissions.value)) {
    for (const permKey of Object.keys(group.permissions)) {
      role[permKey] = 1;
    }
  }
};

// Disable all permissions for a role
const disableAllPermissions = (role) => {
  for (const group of Object.values(permissions.value)) {
    for (const permKey of Object.keys(group.permissions)) {
      role[permKey] = 0;
    }
  }
};

// Save single role permissions
const saveRolePermissions = async (role) => {
  saving.value = true;
  try {
    // Prepare data - only send permission fields
    const permissionData = {};
    for (const group of Object.values(permissions.value)) {
      for (const permKey of Object.keys(group.permissions)) {
        permissionData[permKey] = role[permKey] || 0;
      }
    }

    const response = await phanQuyenAPI.update(role.id, permissionData);

    if (response.data.success) {
      alert("Cập nhật quyền thành công!");
      await loadData(); // Reload data
    } else {
      alert("Có lỗi xảy ra: " + (response.data.message || "Unknown error"));
    }
  } catch (error) {
    console.error("Error saving role permissions:", error);

    if (error.response?.data?.message) {
      alert("Có lỗi xảy ra: " + error.response.data.message);
    } else {
      alert("Có lỗi xảy ra khi lưu quyền!");
    }
  } finally {
    saving.value = false;
  }
};

// Save all roles permissions
const handleSaveAll = async () => {
  if (!confirm("Bạn có chắc chắn muốn lưu tất cả thay đổi?")) {
    return;
  }

  saving.value = true;
  try {
    for (const role of roles.value) {
      const permissionData = {};
      for (const group of Object.values(permissions.value)) {
        for (const permKey of Object.keys(group.permissions)) {
          permissionData[permKey] = role[permKey] || 0;
        }
      }
      await phanQuyenAPI.update(role.id, permissionData);
    }

    alert("Lưu tất cả thay đổi thành công!");
    await loadData();
  } catch (error) {
    console.error("Error saving all permissions:", error);
    alert("Có lỗi xảy ra khi lưu!");
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadData();
});
</script>

<style scoped>
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f3f3f5;
}

::-webkit-scrollbar-thumb {
  background: #d1d5dc;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a1a1aa;
}

/* Checkbox custom styles */
input[type="checkbox"]:checked {
  background-color: #009689;
  border-color: #009689;
}
</style>
