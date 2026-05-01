/**
 * Menu Configuration for different user roles
 * Supports: admin, doctor, nurse
 */

export const menuConfig = {
  admin: {
    title: "Petty Admin",
    subtitle: "Quản trị hệ thống",
    menuItems: [
      {
        key: "dashboard",
        label: "Dashboard",
        icon: "https://www.figma.com/api/mcp/asset/de829f5f-9250-441a-a9ed-1d80054e1c1a",
        path: "/admin/dashboard",
        type: "single",
      },
      {
        key: "resources",
        label: "Tài nguyên",
        icon: "https://www.figma.com/api/mcp/asset/bb90decb-1ed7-40e9-8d18-1420a1ac4fa1",
        type: "group",
        children: [
          {
            key: "serviceManagement",
            label: "Quản lý Dịch vụ",
            path: "/admin/service-management",
          },
        ],
      },
      {
        key: "personnel",
        label: "Nhân sự",
        icon: "https://www.figma.com/api/mcp/asset/88e96d06-b45f-4d93-aa39-5544f66e5d85",
        type: "group",
        children: [
          {
            key: "accountManagement",
            label: "Tài Khoản",
            path: "/admin/account-management",
          },
          {
            key: "scheduleManagement",
            label: "Lịch Làm Việc",
            path: "/admin/schedule-management",
          },
        ],
      },
      {
        key: "finance",
        label: "Tài chính & Hóa đơn",
        icon: "https://www.figma.com/api/mcp/asset/78f6f448-fb75-4de8-a234-173bbebee868",
        type: "group",
        children: [
          {
            key: "invoiceList",
            label: "Danh sách hóa đơn",
            path: "/admin/invoice-list",
          },
        ],
      },
      {
        key: "communication",
        label: "Truyền thông",
        icon: "https://www.figma.com/api/mcp/asset/420d4dcc-ebcb-4c4c-af6b-e3ef6d80dad3",
        type: "group",
        children: [
          {
            key: "posts",
            label: "Bài viết",
            path: "/admin/bai-viet",
          },
          {
            key: "promotions",
            label: "Khuyến Mãi",
            path: "/admin/khuyen-mai",
          },
        ],
      },
      {
        key: "reports",
        label: "Báo cáo",
        icon: "https://www.figma.com/api/mcp/asset/decd1476-2ded-4433-b7a9-727fd8bb498e",
        type: "group",
        children: [
          {
            key: "revenueReport",
            label: "Doanh thu",
            path: "/admin/report/revenue",
          },
          {
            key: "performanceReport",
            label: "Hiệu suất",
            path: "/admin/report/performance",
          },
          {
            key: "inventoryReport",
            label: "Thuốc & Vật Tư",
            path: "/admin/report/inventory",
          },
        ],
      },
      {
        key: "configuration",
        label: "Cấu hình",
        icon: "https://www.figma.com/api/mcp/asset/736ac9f6-dd30-419c-a308-56fafcbf1393",
        path: "/admin/configuration",
        type: "single",
      },
      {
        key: "permissions",
        label: "Phân quyền",
        icon: "https://www.figma.com/api/mcp/asset/10c0e793-cad4-4588-83be-8f9c526e34ee",
        path: "/admin/permissions",
        type: "single",
      },
    ],
  },

  doctor: {
    title: "Petty Doctor",
    subtitle: "Hệ thống Bác sĩ",
    menuItems: [
      {
        key: "dashboard",
        label: "Dashboard",
        icon: "https://www.figma.com/api/mcp/asset/7a583d8e-ed96-463a-ac5b-438fab8ba969",
        path: "/doctor/dashboard",
        type: "single",
      },
      {
        key: "appointments",
        label: "Lịch khám",
        icon: "https://www.figma.com/api/mcp/asset/76c679db-203c-4084-b717-f43fa7e417f4",
        path: "/doctor/appointments",
        type: "single",
      },
      {
        key: "medicalRecords",
        label: "Hồ sơ bệnh án",
        icon: "https://www.figma.com/api/mcp/asset/fa838dd6-b6d7-41bc-8be4-d8cfc7b095ee",
        path: "/doctor/medical-records",
        type: "single",
      },
      {
        key: "clinicalTesting",
        label: "Cận lâm sàng",
        icon: "https://www.figma.com/api/mcp/asset/d1cbc4bd-ea24-44d1-9608-e2eff73743e7",
        path: "/doctor/clinical-testing",
        type: "single",
      },
      {
        key: "pharmacy",
        label: "Kho thuốc",
        icon: "https://www.figma.com/api/mcp/asset/7a0d98a5-2dfb-4c54-949d-034a26c434b7",
        path: "/doctor/pharmacy",
        type: "single",
      },
      {
        key: "schedule",
        label: "Lịch Làm Việc",
        icon: "https://www.figma.com/api/mcp/asset/256654dc-1ff0-4db3-963c-1a5c47cb7984",
        path: "/doctor/schedule",
        type: "single",
      },
    ],
  },

  nurse: {
    title: "Petty Nurse",
    subtitle: "Hệ thống y tá",
    menuItems: [
      {
        key: "dashboard",
        label: "Dashboard",
        icon: "https://www.figma.com/api/mcp/asset/49fd7d49-99f7-4859-9ce2-4aa89c9f9eca",
        path: "/nurse/dashboard",
        type: "single",
        badge: 3,
      },
      {
        key: "appointments",
        label: "Lịch hẹn",
        icon: "https://www.figma.com/api/mcp/asset/44ef5d15-b156-4ce2-9e1c-1b3cac9a3922",
        path: "/nurse/appointments",
        type: "single",
      },
      {
        key: "customers",
        label: "Khách hàng",
        icon: "https://www.figma.com/api/mcp/asset/9db990e4-5cc7-4cbb-9d23-507f00b5efe4",
        path: "/nurse/customers",
        type: "single",
      },
      {
        key: "invoices",
        label: "Hóa đơn",
        icon: "https://www.figma.com/api/mcp/asset/1bce8405-8978-452f-b55e-19174a611030",
        path: "/nurse/invoices",
        type: "single",
        badge: 2,
      },
      {
        key: "schedule",
        label: "Lịch làm việc",
        icon: "https://www.figma.com/api/mcp/asset/16b37603-e2f8-43a0-9364-e9d3b40dd066",
        path: "/nurse/schedule",
        type: "single",
      },
      {
        key: "inventory",
        label: "Kho Thuốc & Vật Tư",
        icon: "https://www.figma.com/api/mcp/asset/a95c0cc7-7307-47cb-a25c-62e95b8fc3a9",
        path: "/nurse/inventory",
        type: "single",
      },
      {
        key: "expenseVouchers",
        label: "Phiếu Chi",
        icon: "https://www.figma.com/api/mcp/asset/380bf330-8557-432d-ae87-a709c49830ad",
        path: "/nurse/expense-vouchers",
        type: "single",
      },
    ],
  },
};

// Icon URLs - shared between roles
export const sharedIcons = {
  logo: "https://www.figma.com/api/mcp/asset/fae20d1b-2870-422a-b6b3-19faaf6051ee",
  chevron:
    "https://www.figma.com/api/mcp/asset/a133d436-5c70-4a4b-964c-aeee989f483b",
  logout:
    "https://www.figma.com/api/mcp/asset/35d9a009-5d0e-4369-a8f1-1e8b002fcb3b",
};

/**
 * Get menu configuration by role
 * @param {string} role - User role (admin, doctor, nurse)
 * @returns {object} Menu configuration object
 */
export function getMenuByRole(role = "admin") {
  return menuConfig[role] || menuConfig.admin;
}
