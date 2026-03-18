<template>
  <div class="relative w-full h-full px-8 py-6">
    <!-- Header -->
    <div class="flex flex-col gap-1">
      <h1 class="font-semibold text-2xl text-black">Kho thuốc & Vật tư</h1>
      <p class="text-base font-medium text-gray-500">
        Quản lý hàng hóa, tồn kho và nhập xuất
      </p>
    </div>

    <!-- Tabs and Content -->
    <div class="flex flex-col gap-8 mt-6">
      <!-- Tab List -->
      <div
        class="bg-[#f3f4f6] flex items-center p-1 rounded-[10px] shadow-sm w-fit"
      >
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="
            activeTab === tab.id
              ? 'bg-white shadow-md text-[#0d9488]'
              : 'text-[#4b5563]'
          "
          class="font-nunito font-medium text-sm leading-5 px-6 py-2.5 rounded-lg transition-all"
        >
          {{ tab.name }}
        </button>
      </div>

      <!-- Tab Content -->
      <!-- Danh sách & Tồn kho Tab -->
      <div v-if="activeTab === 'danh-sach'" class="flex flex-col gap-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-4 gap-4 h-[90px]">
          <div
            v-for="stat in inventoryStats"
            :key="stat.id"
            class="bg-white border !border-gray-300 rounded-[14px] p-[17px] flex items-center gap-3"
          >
            <div
              class="size-12 rounded-[10px] flex items-center justify-center"
              :class="stat.bgColor"
            >
              <component
                :is="stat.icon"
                class="w-6 h-6"
                :class="stat.iconColor"
              />
            </div>
            <div class="flex flex-col gap-0">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                {{ stat.label }}
              </p>
              <p
                class="font-nunito text-base leading-6 tracking-tight"
                :class="stat.textColor"
              >
                {{ stat.count }}
              </p>
            </div>
          </div>
        </div>

        <!-- Main Content Card -->
        <div
          class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
        >
          <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex items-center justify-between h-9">
              <h3
                class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
              >
                Danh sách hàng hóa
              </h3>
              <div class="flex items-center gap-3">
                <button
                  class="bg-white border !border-[#5a9690] rounded-lg px-3 py-2 flex items-center gap-2 hover:bg-gray-50 transition-colors cursor-pointer"
                  @click="handleManageCategories"
                >
                  <FolderIcon class="w-4 h-4 text-[#0d9488]" />
                  <span class="text-[#0d9488]">Danh Mục Hàng Hoá</span>
                </button>
                <button
                  class="bg-[#5a9690] rounded-lg h-9 px-3 py-2 flex items-center gap-2 hover:bg-[#4a8580] transition-colors"
                  @click="isAddModalOpen = true"
                >
                  <AddIcon class="w-4 h-4 text-white" />
                  <span
                    class="font-nunito font-medium text-sm leading-5 text-white"
                  >
                    Thêm Hàng Hoá
                  </span>
                </button>
              </div>
            </div>

            <!-- Search and Filters -->
            <div class="flex items-center gap-4 h-9">
              <!-- Search -->
              <div class="relative flex-1">
                <SearchIcon class="absolute left-3 top-[10px] w-4 h-4" />
                <input
                  v-model="searchInventory"
                  type="text"
                  placeholder="Tìm tên thuốc, mã thuốc, hoạt chất..."
                  class="bg-[#f3f3f5] border-none rounded-lg h-9 w-full pl-10 pr-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
                />
              </div>

              <!-- Category Filter -->
              <button
                class="bg-[#f3f3f5] border-none rounded-lg h-9 w-48 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors"
              >
                <span
                  class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
                  >{{ filterCategory }}</span
                >
                <ChevronDownIcon />
              </button>

              <!-- Unit Filter -->
              <button
                class="bg-[#f3f3f5] border-none rounded-lg h-9 w-48 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors"
              >
                <span
                  class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
                  >{{ filterUnit }}</span
                >
                <ChevronDownIcon />
              </button>

              <!-- Status Filter -->
              <button
                class="bg-[#f3f3f5] border-none rounded-lg h-9 w-48 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors"
              >
                <span
                  class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
                  >{{ filterStatus }}</span
                >
                <ChevronDownIcon />
              </button>
            </div>

            <!-- Table -->
            <div class="overflow-hidden">
              <table class="w-full">
                <thead>
                  <tr class="border-b border-gray-200/60">
                    <th class="text-left py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Ảnh & Tên</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Phân loại</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Đơn vị</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Giá vốn / Giá bán</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Tồn kho</span
                      >
                    </th>
                    <th class="text-left py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Hạn sử dụng</span
                      >
                    </th>
                    <th class="text-right py-[10px] px-2">
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Thao tác</span
                      >
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="item in inventoryList"
                    :key="item.id"
                    class="border-b border-gray-200/60"
                  >
                    <!-- Image & Name -->
                    <td class="py-2 px-2">
                      <div class="flex items-center gap-3 h-12">
                        <div
                          class="size-12 rounded-[10px] bg-gray-100 overflow-hidden flex items-center justify-center"
                        >
                          <img
                            v-if="item.image"
                            :src="item.image"
                            alt=""
                            class="w-full h-full object-cover"
                          />
                        </div>
                        <div class="flex flex-col gap-0">
                          <span
                            class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                            >{{ item.name }}</span
                          >
                          <span
                            class="font-nunito text-xs leading-4 text-[#6a7282]"
                            >Mã: {{ item.code }}</span
                          >
                        </div>
                      </div>
                    </td>

                    <!-- Category -->
                    <td class="py-2 px-2">
                      <span
                        class="inline-block px-2 py-[3px] border border-gray-200/60 rounded-lg text-xs font-medium text-neutral-950"
                      >
                        {{ item.category }}
                      </span>
                    </td>

                    <!-- Unit -->
                    <td class="py-2 px-2">
                      <span
                        class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                        >{{ item.unit }}</span
                      >
                    </td>

                    <!-- Prices -->
                    <td class="py-2 px-2">
                      <div class="flex flex-col gap-0">
                        <span
                          class="font-nunito text-base leading-6 text-[#4a5565] tracking-tight"
                          >{{ formatCurrency(item.costPrice) }}</span
                        >
                        <span
                          class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                          >{{ formatCurrency(item.salePrice) }}</span
                        >
                      </div>
                    </td>

                    <!-- Stock -->
                    <td class="py-2 px-2">
                      <div class="flex items-center gap-2">
                        <span
                          class="font-nunito text-sm leading-5 tracking-tight"
                          :class="
                            item.stockStatus === 'low' ||
                            item.stockStatus === 'out'
                              ? 'text-[#e7000b]'
                              : 'text-[#101828]'
                          "
                        >
                          {{ item.stock }}
                        </span>
                        <img
                          v-if="
                            item.stockStatus === 'low' ||
                            item.stockStatus === 'out'
                          "
                          :src="iconWarning"
                          alt=""
                          class="w-4 h-4"
                        />
                      </div>
                    </td>

                    <!-- Expiry Date -->
                    <td class="py-2 px-2">
                      <button
                        @click="handleChangeStatus(item)"
                        class="inline-block px-2 py-[3px] rounded-lg text-xs font-medium cursor-pointer hover:opacity-80 transition-opacity"
                        :class="item.expiryBadgeClass"
                        title="Kích để thay đổi trạng thái"
                      >
                        {{ item.expiryDate }}
                      </button>
                    </td>

                    <!-- Actions -->
                    <td class="py-2 px-2 text-right">
                      <button
                        class="bg-white border border-gray-200/60 rounded-lg h-8 px-[10px] py-[7px] flex items-center gap-2 hover:bg-gray-50 transition-colors ml-auto"
                        @click="handleOpenTheKho(item)"
                      >
                        <EyeIcon class="w-4 h-4" />
                        <span
                          class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                          >Thẻ kho</span
                        >
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- Pagination -->
            <div class="flex items-center justify-between h-10">
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Hiển thị 1 - 4 của 4 hàng hóa
              </p>
              <div class="flex items-center gap-1">
                <button
                  class="flex items-center gap-2 px-3 py-2 rounded-lg opacity-50"
                  disabled
                >
                  <ChevronLeftIcon />
                </button>
                <button
                  class="bg-white border !border-gray-300 rounded-lg w-9 h-9 flex items-center justify-center"
                >
                  <span
                    class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                    >1</span
                  >
                </button>
                <button
                  class="flex items-center gap-2 px-3 py-2 rounded-lg opacity-50"
                  disabled
                >
                  <ChevronRightIcon />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Nhập kho Tab -->
      <div
        v-if="activeTab === 'nhap-kho'"
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <div class="flex flex-col gap-[30px]">
          <!-- Header -->
          <div class="flex items-center justify-between h-9">
            <h3
              class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
            >
              Lịch sử Nhập kho
            </h3>
            <button
              class="bg-[#5a9690] rounded-lg h-9 px-3 py-2 flex items-center gap-2 hover:bg-[#5a9690]/80 transition-colors"
              @click="isTaoPhieuNhapModalOpen = true"
            >
              <AddIcon class="text-white" />
              <span class="font-medium text-sm text-white tracking-tight">
                Tạo phiếu nhập
              </span>
            </button>
          </div>

          <!-- Table -->
          <div class="overflow-hidden">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200/60">
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Mã phiếu nhập</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Nhà cung cấp</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Ngày nhập</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Người nhập</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Tổng tiền</span
                    >
                  </th>
                  <th class="text-right py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Thao tác</span
                    >
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="record in importRecords"
                  :key="record.id"
                  class="border-b border-gray-200/60"
                >
                  <td class="py-[15px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >{{ record.code }}</span
                    >
                  </td>
                  <td class="py-[15px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >{{ record.supplier }}</span
                    >
                  </td>
                  <td class="py-[15px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                      >{{ formatDate(record.date) }}</span
                    >
                  </td>
                  <td class="py-[15px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                      >{{ record.user }}</span
                    >
                  </td>
                  <td class="py-[15px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#009689] tracking-tight"
                      >{{ formatCurrency(record.total) }}</span
                    >
                  </td>
                  <td class="py-[15px] px-2 text-right">
                    <button
                      class="bg-white border border-gray-200/60 rounded-lg h-8 px-[10px] py-[7px] flex items-center gap-2 hover:bg-gray-50 transition-colors ml-auto"
                      @click="handleOpenChiTietPhieuNhap(record)"
                    >
                      <EyeIcon />
                      <span
                        class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                        >Chi tiết</span
                      >
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Kiểm kê Tab -->
      <div
        v-else-if="activeTab === 'kiem-ke'"
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <div class="flex flex-col gap-[30px]">
          <!-- Header -->
          <div class="flex items-center justify-between h-10">
            <div class="flex flex-col gap-1">
              <h3
                class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
              >
                Kiểm kê Kho
              </h3>
              <p
                class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
              >
                Đối chiếu số lượng thực tế với hệ thống
              </p>
            </div>
            <button
              @click="handleCanBangKho"
              class="bg-[#5a9690] rounded-lg h-9 px-4 py-2 hover:bg-[#5a9690]/80 transition-colors"
            >
              <span class="font-medium text-sm text-white tracking-tight">
                Cân bằng kho
              </span>
            </button>
          </div>

          <!-- Table -->
          <div class="overflow-hidden">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200/60">
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Mã hàng hóa</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Tên hàng hóa</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Tồn trên máy</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Thực tế kiểm</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Chênh lệch</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Lý do</span
                    >
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-if="inventoryItems.length === 0"
                  class="border-b border-gray-200/60"
                >
                  <td colspan="6" class="py-8 text-center">
                    <p class="font-nunito text-sm text-[#717182]">
                      Không có dữ liệu hàng hóa để kiểm kê
                    </p>
                  </td>
                </tr>
                <tr
                  v-for="item in inventoryItems"
                  :key="item.id"
                  class="border-b border-gray-200/60"
                >
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-xs leading-4 text-[#6a7282] tracking-tight"
                      >{{ item.code }}</span
                    >
                  </td>
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >{{ item.name }}</span
                    >
                  </td>
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                      >{{ item.systemQty }}</span
                    >
                  </td>
                  <td class="py-[17px] px-2">
                    <input
                      v-model.number="item.actualQty"
                      @input="handleActualQtyChange(item)"
                      type="number"
                      min="0"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 w-24 px-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none"
                    />
                  </td>
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 tracking-tight"
                      :class="{
                        'text-[#e7000b]': item.difference < 0,
                        'text-[#009689]': item.difference > 0,
                        'text-[#101828]': item.difference === 0,
                      }"
                    >
                      {{ item.difference > 0 ? "+" : "" }}{{ item.difference }}
                    </span>
                  </td>
                  <td class="py-[17px] px-2 relative reason-dropdown-container">
                    <button
                      @click.stop="toggleReasonDropdown(item)"
                      class="bg-[#f3f3f5] border-none rounded-lg h-9 w-40 px-[13px] py-0.5 flex items-center justify-between transition-all"
                      :class="{
                        'opacity-50 cursor-not-allowed': item.difference === 0,
                        'hover:bg-gray-200 cursor-pointer':
                          item.difference !== 0,
                        'ring-2 ring-[#009689] ring-opacity-50':
                          item.showReasonDropdown,
                      }"
                      :disabled="item.difference === 0"
                    >
                      <span
                        class="font-nunito text-sm leading-5 tracking-tight truncate"
                        :class="
                          item.selectedReason
                            ? 'text-neutral-950 font-medium'
                            : 'text-[#717182]'
                        "
                      >
                        {{ item.selectedReason || "Chọn lý do" }}
                      </span>
                      <ChevronDownIcon />
                    </button>

                    <!-- Dropdown lý do -->
                    <div
                      v-if="item.showReasonDropdown"
                      class="absolute z-50 mt-1 w-40 bg-white border !border-gray-200 rounded-lg shadow-lg overflow-hidden"
                    >
                      <button
                        v-for="reason in lyDoOptions"
                        :key="reason"
                        @click.stop="selectReason(item, reason)"
                        class="w-full text-left px-3 py-2 hover:bg-gray-100 font-nunito text-sm text-neutral-950 tracking-tight transition-colors flex items-center justify-between"
                        :class="{
                          'bg-[#e6f7f5] hover:bg-[#d4f2ed] text-[#009689] font-medium':
                            item.selectedReason === reason,
                        }"
                      >
                        <span>{{ reason }}</span>
                        <svg
                          v-if="item.selectedReason === reason"
                          class="w-4 h-4 text-[#009689]"
                          fill="none"
                          stroke="currentColor"
                          viewBox="0 0 24 24"
                        >
                          <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M5 13l4 4L19 7"
                          />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Nhà cung cấp Tab -->
      <div
        v-else-if="activeTab === 'nha-cung-cap'"
        class="bg-white border !border-gray-300 shadow-sm rounded-[14px] p-6"
      >
        <div class="flex flex-col gap-6">
          <!-- Header -->
          <div class="flex items-center justify-between h-9">
            <h3
              class="font-nunito text-base leading-4 text-neutral-950 tracking-tight"
            >
              Danh sách Nhà cung cấp
            </h3>
            <button
              class="bg-[#5a9690] rounded-lg h-9 px-3 py-2 flex items-center gap-2 hover:bg-[#5a9690]/80 transition-colors"
              @click="isThemDoiTacModalOpen = true"
            >
              <AddIcon class="text-white" />
              <span class="font-medium text-sm text-white tracking-tight">
                Thêm Nhà cung cấp
              </span>
            </button>
          </div>

          <!-- Search and Filter -->
          <div class="flex items-center gap-4 h-9">
            <div class="relative flex-1">
              <SearchIcon class="absolute left-3 top-[10px] w-4 h-4" />
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Tìm tên công ty, SĐT, người liên hệ..."
                class="bg-[#f3f3f5] border-none rounded-lg h-9 w-full pl-10 pr-3 py-1 font-nunito text-sm text-neutral-950 tracking-tight outline-none placeholder:text-[#717182]"
              />
            </div>
            <button
              class="bg-[#f3f3f5] border-none rounded-lg h-9 w-48 px-[13px] py-0.5 flex items-center justify-between hover:bg-gray-200 transition-colors"
            >
              <span
                class="font-nunito text-sm leading-5 text-neutral-950 tracking-tight"
                >Tất cả</span
              >
              <ChevronDownIcon />
            </button>
          </div>

          <!-- Table -->
          <div class="overflow-hidden">
            <table class="w-full">
              <thead>
                <tr class="border-b border-gray-200/60">
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Mã NCC</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Tên Nhà cung cấp</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Liên hệ</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Địa chỉ</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Công nợ hiện tại</span
                    >
                  </th>
                  <th class="text-left py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Trạng thái</span
                    >
                  </th>
                  <th class="text-right py-[10px] px-2">
                    <span
                      class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
                      >Thao tác</span
                    >
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-if="suppliers.length === 0"
                  class="border-b border-gray-200/60"
                >
                  <td colspan="7" class="py-8 text-center">
                    <p class="font-nunito text-sm text-[#717182]">
                      Chưa có nhà cung cấp nào. Nhấn "Thêm Nhà cung cấp" để thêm
                      mới.
                    </p>
                  </td>
                </tr>
                <tr
                  v-for="supplier in paginatedSuppliers"
                  :key="supplier.id"
                  class="border-b border-gray-200/60"
                >
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                      >{{ supplier.code }}</span
                    >
                  </td>
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-base leading-6 text-[#101828] tracking-tight"
                      >{{ supplier.name }}</span
                    >
                  </td>
                  <td class="py-[17px] px-2">
                    <div class="flex flex-col gap-0.5">
                      <span
                        class="font-nunito text-sm leading-5 text-[#101828] tracking-tight"
                        >{{ supplier.phone }}</span
                      >
                      <span class="font-nunito text-xs leading-4 text-[#6a7282]"
                        >({{ supplier.contact }})</span
                      >
                    </div>
                  </td>
                  <td class="py-[17px] px-2 overflow-hidden">
                    <span
                      class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
                      >{{ supplier.address }}</span
                    >
                  </td>
                  <td class="py-[17px] px-2">
                    <span
                      class="font-nunito text-sm leading-5 tracking-tight"
                      :class="
                        supplier.debt > 0 ? 'text-[#f54900]' : 'text-[#101828]'
                      "
                    >
                      {{ formatCurrency(supplier.debt) }}
                    </span>
                  </td>
                  <td class="py-[17px] px-2">
                    <button
                      @click="handleChangeSupplierStatus(supplier)"
                      class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-medium leading-4 cursor-pointer hover:opacity-80 transition-opacity"
                      :class="
                        supplier.status === 'active'
                          ? 'bg-green-100 text-[#008236]'
                          : 'bg-gray-100 text-[#364153]'
                      "
                      :title="`Kích để ${
                        supplier.status === 'active'
                          ? 'ngừng hoạt động'
                          : 'kích hoạt'
                      }`"
                    >
                      {{ supplier.status === "active" ? "Hợp tác" : "Ngừng" }}
                    </button>
                  </td>
                  <td class="py-[17px] px-2">
                    <div class="flex items-center gap-2 justify-end">
                      <button
                        class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                        @click="handleEditSupplier(supplier)"
                      >
                        <UpdateIcon class="w-4 h-4" />
                      </button>
                      <!-- <button
                        class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                      >
                        <img :src="iconEdit" alt="" class="w-4 h-4" />
                      </button> -->
                      <button
                        class="bg-white border border-gray-200/60 rounded-lg w-[38px] h-8 flex items-center justify-center hover:bg-gray-50 transition-colors"
                        @click="handleDeleteSupplier(supplier)"
                      >
                        <TrashIcon class="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="flex items-center justify-between h-10">
            <p
              class="font-nunito text-sm leading-5 text-[#4a5565] tracking-tight"
            >
              Hiển thị {{ displayStart }} - {{ displayEnd }} của
              {{ suppliers.length }} nhà cung cấp
            </p>
            <div class="flex items-center gap-1">
              <button
                @click="goToPreviousPage"
                :disabled="!canGoPrevious"
                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all"
                :class="
                  canGoPrevious
                    ? 'hover:bg-gray-100 cursor-pointer'
                    : 'opacity-50 cursor-not-allowed'
                "
              >
                <ChevronLeftIcon />
              </button>
              <button
                v-for="page in totalPages"
                :key="page"
                @click="goToPage(page)"
                class="rounded-lg w-9 h-9 flex items-center justify-center transition-all"
                :class="
                  page === currentPage
                    ? 'bg-white border !border-gray-300'
                    : 'hover:bg-gray-100'
                "
              >
                <span
                  class="font-nunito font-medium text-sm leading-5 tracking-tight"
                  :class="
                    page === currentPage ? 'text-black' : 'text-[#4a5565]'
                  "
                  >{{ page }}</span
                >
              </button>
              <button
                @click="goToNextPage"
                :disabled="!canGoNext"
                class="flex items-center gap-2 px-3 py-2 rounded-lg transition-all"
                :class="
                  canGoNext
                    ? 'hover:bg-gray-100 cursor-pointer'
                    : 'opacity-50 cursor-not-allowed'
                "
              >
                <ChevronRightIcon />
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <ThemThuocVatTu
      v-if="isAddModalOpen"
      @close="isAddModalOpen = false"
      @save="handleSaveInventory"
    />

    <TheKho
      v-if="isTheKhoModalOpen"
      :product="selectedItemForTheKho"
      :stock-data="{
        quantity: selectedItemForTheKho?.stock,
        unit: selectedItemForTheKho?.unit,
        lotNumber: 'LOT2024001',
      }"
      @close="isTheKhoModalOpen = false"
    />

    <TaoPhieuNhap
      v-if="isTaoPhieuNhapModalOpen"
      @close="isTaoPhieuNhapModalOpen = false"
      @save="handleSavePhieuNhap"
    />

    <ChiTietPhieuNhap
      v-if="isChiTietPhieuNhapModalOpen"
      :receipt="selectedReceiptForDetail"
      @close="isChiTietPhieuNhapModalOpen = false"
    />

    <ThemDoiTacCungCap
      v-if="isThemDoiTacModalOpen"
      @close="isThemDoiTacModalOpen = false"
      @save="handleSaveDoiTac"
    />

    <SuaNhaCungCap
      v-if="isSuaNhaCungCapModalOpen"
      :supplier="selectedSupplierForEdit"
      @close="isSuaNhaCungCapModalOpen = false"
      @save="handleSaveEditedSupplier"
    />

    <XoaNhaCungCap
      v-if="isXoaNhaCungCapModalOpen"
      :supplier-name="selectedSupplierForDelete?.name"
      :related-receipts="relatedReceiptsForDelete"
      @close="isXoaNhaCungCapModalOpen = false"
      @delete="handleConfirmDeleteSupplier"
      @deactivate="handleDeactivateSupplier"
    />

    <DanhMucHangHoa
      v-if="isManageCategoriesModalOpen"
      @close="isManageCategoriesModalOpen = false"
    />
  </div>
</template>

<script setup>
import {
  ref,
  reactive,
  onMounted,
  computed,
  onBeforeUnmount,
  toRaw,
} from "vue";
import ThemThuocVatTu from "./ThemThuocVatTu/index.vue";
import TheKho from "./TheKho/index.vue";
import TaoPhieuNhap from "./TaoPhieuNhap/index.vue";
import ChiTietPhieuNhap from "./ChiTietPhieuNhap/index.vue";
import ThemDoiTacCungCap from "./ThemDoiTacCungCap/index.vue";
import SuaNhaCungCap from "./SuaNhaCungCap/index.vue";
import XoaNhaCungCap from "./XoaNhaCungCap/index.vue";
import DanhMucHangHoa from "./DanhMucHangHoa/index.vue";
import { listHangHoa, changeHangHoaStatus } from "@/utils/hangHoa";
import {
  getNhaCungCaps,
  changeNhaCungCapStatus,
  deleteNhaCungCap,
} from "@/utils/nhaCungCap";
import { getPhieuNhapKhos } from "@/utils/phieuNhapKho";
import { getKiemKes, createKiemKe } from "@/utils/kiemKe";
import { getPhieuChis } from "@/utils/phieuChi";
import { showErrorToast, showSuccessToast } from "@/utils/toast";
//Icon SVG
import FolderIcon from "@/assets/svg/folder.svg";
import AddIcon from "@/assets/svg/add.svg";
import SearchIcon from "@/assets/svg/search.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
import ChevronLeftIcon from "@/assets/svg/chevron-left.svg";
import ChevronRightIcon from "@/assets/svg/chevron-right.svg";
import ArrowDownIcon from "@/assets/svg/arrow-down.svg";
import WarningIcon from "@/assets/svg/warning.svg";
import ClockIcon from "@/assets/svg/clock.svg";
import DollarIcon from "@/assets/svg/dollar.svg";
import EyeIcon from "@/assets/svg/eye.svg";
import UpdateIcon from "@/assets/svg/update.svg";
import TrashIcon from "@/assets/svg/trash.svg";
// State
const activeTab = ref("danh-sach");
const searchQuery = ref("");
const searchInventory = ref("");
const filterCategory = ref("Tất cả");
const filterUnit = ref("Tất cả");
const filterStatus = ref("Tất cả");
const isAddModalOpen = ref(false);
const isTheKhoModalOpen = ref(false);
const selectedItemForTheKho = ref(null);
const isTaoPhieuNhapModalOpen = ref(false);
const isChiTietPhieuNhapModalOpen = ref(false);
const selectedReceiptForDetail = ref(null);
const isThemDoiTacModalOpen = ref(false);
const isSuaNhaCungCapModalOpen = ref(false);
const selectedSupplierForEdit = ref(null);

// Pagination state for suppliers
const currentPage = ref(1);
const itemsPerPage = ref(5);
const isXoaNhaCungCapModalOpen = ref(false);
const selectedSupplierForDelete = ref(null);
const relatedReceiptsForDelete = ref([]);
const isManageCategoriesModalOpen = ref(false);

const tabs = [
  { id: "danh-sach", name: "Danh sách & Tồn kho" },
  { id: "nhap-kho", name: "Nhập kho" },
  { id: "kiem-ke", name: "Kiểm kê" },
  { id: "nha-cung-cap", name: "Nhà cung cấp" },
];

// Sample Data - Danh sách & Tồn kho
// Computed properties for inventory statistics
const inventoryStats = computed(() => {
  const outOfStock = inventoryList.filter(
    (item) => item.stockStatus === "out"
  ).length;
  const lowStock = inventoryList.filter(
    (item) => item.stockStatus === "low"
  ).length;
  const expiringSoon = inventoryList.filter(
    (item) => item.expiryStatus === "warning" || item.expiryStatus === "expired"
  ).length;
  const totalValue = inventoryList.reduce(
    (sum, item) => sum + item.stock * item.costPrice,
    0
  );

  return [
    {
      id: 1,
      label: "Hết hàng",
      count: `${outOfStock} mặt hàng`,
      bgColor: "bg-[#ffe2e2]",
      textColor: "text-[#e7000b]",
      icon: ArrowDownIcon,
      iconColor: "text-[#e7000b]",
    },
    {
      id: 2,
      label: "Sắp hết hàng",
      count: `${lowStock} mặt hàng`,
      bgColor: "bg-[#ffedd4]",
      textColor: "text-[#f54900]",
      icon: WarningIcon,
      iconColor: "text-[#f54900]",
    },
    {
      id: 3,
      label: "Sắp hết hạn",
      count: `${expiringSoon} mặt hàng`,
      bgColor: "bg-[#fef9c2]",
      textColor: "text-[#d08700]",
      icon: ClockIcon,
      iconColor: "text-[#d08700]",
    },
    {
      id: 4,
      label: "Tổng giá trị kho",
      count: formatCurrency(totalValue),
      bgColor: "bg-[#cbfbf1]",
      textColor: "text-[#009689]",
      icon: DollarIcon,
      iconColor: "text-[#009689]",
    },
  ];
});

const inventoryList = reactive([
  {
    id: 1,
    name: "Zoletil 50",
    code: "MED001",
    image: imgProduct1,
    category: "Thuốc",
    unit: "Lọ",
    costPrice: 50000,
    salePrice: 150000,
    stock: 5,
    stockStatus: "low",
    expiryDate: "Sắp hết hạn",
    expiryStatus: "warning",
    expiryBadgeClass: "bg-[#ffedd4] text-[#ca3500]",
  },
  {
    id: 2,
    name: "Vắc-xin 7 bệnh",
    code: "VAC001",
    image: imgProduct2,
    category: "Vắc-xin",
    unit: "Liều",
    costPrice: 80000,
    salePrice: 200000,
    stock: 120,
    stockStatus: "good",
    expiryDate: "Đã hết hạn",
    expiryStatus: "expired",
    expiryBadgeClass: "bg-[#ffe2e2] text-[#c10007]",
  },
  {
    id: 3,
    name: "Bông băng y tế",
    code: "SUP001",
    image: imgProduct3,
    category: "Vật tư tiêu hao",
    unit: "Cuộn",
    costPrice: 15000,
    salePrice: 35000,
    stock: 85,
    stockStatus: "good",
    expiryDate: "2026-06-20",
    expiryStatus: "good",
    expiryBadgeClass: "bg-green-100 text-[#008236]",
  },
  {
    id: 4,
    name: "Kháng sinh Amoxicillin",
    code: "MED002",
    image: null,
    category: "Thuốc",
    unit: "Viên",
    costPrice: 500,
    salePrice: 2000,
    stock: 0,
    stockStatus: "out",
    expiryDate: "Đã hết hạn",
    expiryStatus: "expired",
    expiryBadgeClass: "bg-[#ffe2e2] text-[#c10007]",
  },
]);

// Sample Data - Nhập kho
const importRecords = ref([]);

// Sample Data - Kiểm kê
const inventoryItems = reactive([]);
const kiemKeList = ref([]);
const lyDoOptions = [
  "Thất thoát",
  "Hư hỏng",
  "Hết hạn",
  "Sai sót nhập liệu",
  "Điều chỉnh khác",
];

// Sample Data - Nhà cung cấp
const suppliers = reactive([]);

// Icon URLs from Figma (expire in 7 days)
const iconStockOut =
  "https://www.figma.com/api/mcp/asset/dc66625c-ee38-4195-8991-e79261d28e11";
const iconLowStock =
  "https://www.figma.com/api/mcp/asset/9c43b4e1-d4e2-4e83-ab92-4873bcefc6b6";
const iconExpiring =
  "https://www.figma.com/api/mcp/asset/991c4435-2ad6-46da-9e16-2de8a915874c";
const iconTotalValue =
  "https://www.figma.com/api/mcp/asset/7f6357dd-4f2f-4cb9-b539-84a3a8148cbc";
const iconAddItem =
  "https://www.figma.com/api/mcp/asset/0a9a1906-a81f-4ccb-a798-7269ee7f26d5";
const iconFilterList =
  "https://www.figma.com/api/mcp/asset/b0533f6c-44cd-440a-9e72-b9c556dce9d4";
const iconSearchList =
  "https://www.figma.com/api/mcp/asset/4d6aa7a6-6c43-4944-aa02-a1f4c1cb77fa";
const iconWarning =
  "https://www.figma.com/api/mcp/asset/2305cbc5-ff43-49a4-b99a-aabc274a9bec";
const iconEyeList =
  "https://www.figma.com/api/mcp/asset/d4747055-e52c-44fd-a2c9-ee4471ff82b6";
const imgProduct1 =
  "https://www.figma.com/api/mcp/asset/96b8daf7-a913-40e8-ba03-325034721605";
const imgProduct2 =
  "https://www.figma.com/api/mcp/asset/908d1703-82d8-4cc4-bdfe-80e9dee83841";
const imgProduct3 =
  "https://www.figma.com/api/mcp/asset/0e69a3f9-b9f9-478b-900a-2810a6630076";
const iconPlus =
  "https://www.figma.com/api/mcp/asset/f6842dcf-ccf9-45ea-99b7-e6c4e4affc61";
const iconEye =
  "https://www.figma.com/api/mcp/asset/0d3fb76d-9c10-43ec-8cd6-535123639beb";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/2dc96151-300a-4d43-97fa-1b1f8dc13efb";
const iconFilter =
  "https://www.figma.com/api/mcp/asset/226a7a77-aed1-47aa-9821-d83e78112106";
const iconChevronDown2 =
  "https://www.figma.com/api/mcp/asset/10307674-3571-4970-a22f-49fd3ec79376";
const iconSearch =
  "https://www.figma.com/api/mcp/asset/ffbbe0e1-efcb-46ca-b836-b930da441b68";
const iconView =
  "https://www.figma.com/api/mcp/asset/a852fd94-3f29-4a09-ac91-a9a675900216";
const iconEdit =
  "https://www.figma.com/api/mcp/asset/573f95f2-4c6a-475b-b38c-0cf28529aca8";
const iconDelete =
  "https://www.figma.com/api/mcp/asset/db259098-cbc5-4ede-9aac-244844d1ba0a";
const iconChevronLeft =
  "https://www.figma.com/api/mcp/asset/6d46ecce-c14a-4ba9-afc1-b477dd7c7a66";
const iconChevronRight =
  "https://www.figma.com/api/mcp/asset/dec0f180-4a84-46dc-8ecb-737a257369ae";
const iconFolder =
  "https://www.figma.com/api/mcp/asset/d6fa9f58-18e9-46fb-a4c7-d6d5ac16c30b";

// Computed properties for pagination
const totalPages = computed(() => {
  return Math.ceil(suppliers.length / itemsPerPage.value);
});

const paginatedSuppliers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return suppliers.slice(start, end);
});

const displayStart = computed(() => {
  return suppliers.length > 0
    ? (currentPage.value - 1) * itemsPerPage.value + 1
    : 0;
});

const displayEnd = computed(() => {
  const end = currentPage.value * itemsPerPage.value;
  return end > suppliers.length ? suppliers.length : end;
});

const canGoPrevious = computed(() => {
  return currentPage.value > 1;
});

const canGoNext = computed(() => {
  return currentPage.value < totalPages.value;
});

// Methods
const formatCurrency = (amount) => {
  if (amount === null || amount === undefined || isNaN(amount)) {
    return "0 ₫";
  }
  return `${parseInt(amount).toLocaleString("vi-VN")} ₫`;
};

// Hàm format ngày sang định dạng dd/mm/yyyy
const formatDate = (dateString) => {
  if (!dateString) return "N/A";

  const date = new Date(dateString);
  if (isNaN(date.getTime())) return dateString; // Nếu không parse được, trả về string gốc

  const day = String(date.getDate()).padStart(2, "0");
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const year = date.getFullYear();

  return `${day}/${month}/${year}`;
};

// Hàm tính toán trạng thái hạn sử dụng
const getExpiryStatus = (tinhTrang) => {
  // Backend chỉ hỗ trợ 2 trạng thái
  if (tinhTrang) {
    if (tinhTrang === "hoat_dong") {
      return {
        date: "Hoạt động",
        badgeClass: "bg-green-100 text-[#008236]",
      };
    } else if (tinhTrang === "ngung_kinh_doanh") {
      return {
        date: "Ngừng kinh doanh",
        badgeClass: "bg-gray-100 text-[#4b5563]",
      };
    }
  }

  // Default
  return {
    date: "-",
    badgeClass: "bg-green-100 text-[#008236]",
  };
};

// Hàm tính tổng số lượng tồn kho từ phiếu nhập
const calculateStockQuantity = (hangHoaId) => {
  let totalStock = 0;

  // Duyệt qua tất cả phiếu nhập kho
  importRecords.value.forEach((record) => {
    // Lấy chi tiết phiếu nhập từ dữ liệu gốc
    const chiTiet = record._original?.chi_tiet_phieu_nhap_kho || [];

    // Tìm hàng hóa trong chi tiết phiếu nhập
    chiTiet.forEach((item) => {
      if (item.hang_hoa_id === hangHoaId) {
        totalStock += parseInt(item.so_luong) || 0;
      }
    });
  });

  return totalStock;
};

// Hàm xác định trạng thái tồn kho
const getStockStatus = (quantity) => {
  if (quantity === 0) return "out";
  if (quantity <= 10) return "low";
  return "good";
};

// Load hàng hóa từ API
const loadHangHoa = async () => {
  try {
    const data = await listHangHoa();
    if (data && Array.isArray(data)) {
      // Clear sample data - xóa toàn bộ
      inventoryList.splice(0, inventoryList.length);

      // Map dữ liệu từ API
      data.forEach((item) => {
        // Lấy tên danh mục từ Backend
        const categoryName =
          item.ten_danh_muc_hang_hoa ||
          item.danhMuc?.ten_danh_muc_hang_hoa ||
          "Chưa phân loại";

        // Lấy trạng thái từ tinh_trang Backend
        const expiryStatus = getExpiryStatus(item.tinh_trang);

        // Lấy số lượng tồn kho từ backend (tong_so_luong_nhap)
        const stockQuantity = item.tong_so_luong_nhap || 0;
        const stockStatus = getStockStatus(stockQuantity);

        inventoryList.push({
          id: item.id,
          name: item.ten_mat_hang,
          code: item.ma_hang_hoa,
          image: item.anh_san_pham,
          category: categoryName,
          unit: item.don_vi_tinh,
          costPrice: item.gia_von,
          salePrice: item.gia_ban,
          stock: stockQuantity, // Lấy từ backend (tong_so_luong_nhap)
          stockStatus: stockStatus, // 'out', 'low', hoặc 'good'
          tinh_trang: item.tinh_trang, // Lưu giữ giá trị tinh_trang từ Backend
          expiryDate: expiryStatus.date,
          expiryStatus:
            item.tinh_trang === "ngung_kinh_doanh" ? "expired" : "good", // Thêm expiryStatus cho thống kê
          expiryBadgeClass: expiryStatus.badgeClass,
        });
      });
    }
  } catch (error) {
    console.error("Lỗi tải dữ liệu hàng hóa:", error);
    showErrorToast("Lỗi", "Không thể tải danh sách hàng hóa");
  }
};

// Load nhà cung cấp từ API
const loadNhaCungCaps = async () => {
  try {
    const response = await getNhaCungCaps();
    if (response && response.status && Array.isArray(response.data)) {
      // Clear array
      suppliers.splice(0, suppliers.length);

      // Load phiếu chi để tính công nợ
      let phieuChiData = [];
      try {
        const phieuChiResponse = await getPhieuChis({ per_page: 9999 });
        console.log("📦 Response phiếu chi:", phieuChiResponse);
        if (phieuChiResponse && phieuChiResponse.status) {
          // Xử lý cả trường hợp có pagination và không có pagination
          if (Array.isArray(phieuChiResponse.data)) {
            phieuChiData = phieuChiResponse.data;
          } else if (
            phieuChiResponse.data &&
            Array.isArray(phieuChiResponse.data.data)
          ) {
            phieuChiData = phieuChiResponse.data.data;
          }
          console.log("✅ Phiếu chi data:", phieuChiData);
          console.log("📊 Số lượng phiếu chi:", phieuChiData.length);
        }
      } catch (error) {
        console.error("❌ Lỗi tải phiếu chi:", error);
      }

      // Map dữ liệu từ API
      response.data.forEach((item) => {
        // Tính tổng công nợ từ các phiếu chi của nhà cung cấp này
        const phieuChiFiltered = phieuChiData.filter(
          (phieu) =>
            phieu.nha_cung_cap_id === item.id &&
            phieu.loai_phieu_chi === "chi_nhap_hang"
        );

        console.log(
          `💰 NCC [${item.ten_nha_cung_cap}] (ID: ${item.id}) có ${phieuChiFiltered.length} phiếu chi:`,
          phieuChiFiltered
        );

        const totalDebt = phieuChiFiltered.reduce((sum, phieu) => {
          const debt = parseFloat(phieu.so_tien_con_no) || 0;
          console.log(
            `  - Phiếu ${phieu.ma_phieu_chi}: ${phieu.so_tien_con_no} => ${debt}đ (nha_cung_cap_id: ${phieu.nha_cung_cap_id})`
          );
          return sum + debt;
        }, 0);

        console.log(
          `  ➡️ Tổng công nợ: ${totalDebt}đ (làm tròn: ${Math.round(
            totalDebt
          )}đ)`
        );

        suppliers.push({
          id: item.id,
          code: item.ma_nha_cung_cap || "N/A",
          name: item.ten_nha_cung_cap,
          phone: item.so_dien_thoai,
          contact: item.ten_nguoi_lien_he || "N/A",
          address: item.dia_chi || "N/A",
          debt: Math.round(totalDebt), // Tổng công nợ từ phiếu chi (làm tròn thành số nguyên)
          status: item.trang_thai === "hoat_dong" ? "active" : "inactive",
          // Lưu giữ dữ liệu gốc để sử dụng khi cần
          _original: item,
        });
      });

      console.log(
        "✅ Đã tải nhà cung cấp với công nợ từ phiếu chi:",
        toRaw(suppliers)
      );

      // Reset về trang 1 sau khi load dữ liệu mới
      currentPage.value = 1;
    }
  } catch (error) {
    console.error("Lỗi tải dữ liệu nhà cung cấp:", error);
    showErrorToast("Không thể tải danh sách nhà cung cấp");
  }
};

// Load phiếu nhập kho từ API
const loadPhieuNhapKhos = async () => {
  try {
    const response = await getPhieuNhapKhos();
    if (response && response.status && Array.isArray(response.data)) {
      importRecords.value = response.data.map((item) => {
        // Lấy tên người nhập - ưu tiên nhanVien, nếu không có thì lấy admin
        let userName = "N/A";
        if (item.nhan_vien && item.nhan_vien.full_name) {
          userName = item.nhan_vien.full_name;
        } else if (item.admin && item.admin.ho_ten) {
          userName = item.admin.ho_ten;
        }

        return {
          id: item.id,
          code: item.ma_phieu_nhap,
          supplier: item.nha_cung_cap?.ten_nha_cung_cap || "N/A",
          date: item.ngay_nhap,
          user: userName,
          total: item.tong_tien || 0,
          status: item.trang_thai,
          // Lưu dữ liệu gốc để sử dụng khi xem chi tiết
          _original: item,
        };
      });
    }
  } catch (error) {
    console.error("Lỗi tải dữ liệu phiếu nhập kho:", error);
    // Không hiển thị toast error nếu là lỗi network (có thể backend chưa chạy)
    if (error.code !== "ERR_NETWORK") {
      showErrorToast("Không thể tải danh sách phiếu nhập kho");
    }
    // Khởi tạo mảng rỗng nếu lỗi
    importRecords.value = [];
  }
};

// Load dữ liệu khi component mount
onMounted(() => {
  // Load tất cả dữ liệu song song
  Promise.all([
    loadPhieuNhapKhos(),
    loadHangHoa(),
    loadInventoryForKiemKe(),
    loadNhaCungCaps(),
  ]);

  // Thêm event listener để đóng dropdown khi click bên ngoài
  document.addEventListener("click", handleClickOutside);
});

// Cleanup event listener khi component unmount
onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

// Hàm xử lý click bên ngoài để đóng dropdown
const handleClickOutside = (event) => {
  // Kiểm tra xem click có phải vào button hoặc dropdown không
  const isClickInsideDropdown = event.target.closest(
    ".reason-dropdown-container"
  );

  if (!isClickInsideDropdown) {
    // Đóng tất cả dropdown
    inventoryItems.forEach((item) => {
      item.showReasonDropdown = false;
    });
  }
};

const handleSaveInventory = (data) => {
  console.log("New inventory item:", data);

  // Nếu backend trả về item mới, ta có thể add vào list
  if (data && data.id) {
    // Lấy tên danh mục từ Backend
    const categoryName =
      data.ten_danh_muc_hang_hoa ||
      data.danhMuc?.ten_danh_muc_hang_hoa ||
      "Chưa phân loại";

    // Lấy trạng thái từ tinh_trang
    const expiryStatus = getExpiryStatus(data.tinh_trang);

    const newItem = {
      id: data.id,
      code: data.ma_hang_hoa,
      name: data.ten_mat_hang,
      category: categoryName,
      unit: data.don_vi_tinh,
      costPrice: data.gia_von,
      salePrice: data.gia_ban,
      stock: 0, // Hàng mới chưa có tồn kho
      stockStatus: "out", // Hàng mới = hết hàng
      tinh_trang: data.tinh_trang, // Lưu giữ giá trị tinh_trang từ Backend
      expiryDate: expiryStatus.date,
      expiryStatus: data.tinh_trang === "ngung_kinh_doanh" ? "expired" : "good",
      expiryBadgeClass: expiryStatus.badgeClass,
      image: data.anh_san_pham,
    };
    inventoryList.unshift(newItem);
    showSuccessToast("Thành công", "Thêm hàng hóa thành công");
  }

  isAddModalOpen.value = false;
};

const handleChangeStatus = async (item) => {
  try {
    // Backend chỉ hỗ trợ 2 trạng thái: hoat_dong hoặc ngung_kinh_doanh
    // Chỉ toggle giữa 2 giá trị này
    const nextStatus =
      item.tinh_trang === "hoat_dong" ? "ngung_kinh_doanh" : "hoat_dong";

    // Gọi API để cập nhật trạng thái
    const result = await changeHangHoaStatus(item.id, nextStatus);

    if (result) {
      // Cập nhật item trong list
      const expiryStatus = getExpiryStatus(result.tinh_trang || nextStatus);
      item.tinh_trang = result.tinh_trang || nextStatus; // Lưu giá trị tinh_trang mới
      item.expiryDate = expiryStatus.date;
      item.expiryStatus =
        (result.tinh_trang || nextStatus) === "ngung_kinh_doanh"
          ? "expired"
          : "good";
      item.expiryBadgeClass = expiryStatus.badgeClass;

      showSuccessToast("Thành công", "Đổi trạng thái sản phẩm thành công");
    }
  } catch (error) {
    showErrorToast("Lỗi", error.message || "Không thể đổi trạng thái sản phẩm");
  }
};

const handleOpenTheKho = (item) => {
  selectedItemForTheKho.value = item;
  isTheKhoModalOpen.value = true;
};

const handleSavePhieuNhap = async (data) => {
  console.log("New import record:", data);
  // Đóng modal
  isTaoPhieuNhapModalOpen.value = false;
  // Reload danh sách phiếu nhập và hàng hóa để cập nhật số lượng tồn kho
  await Promise.all([
    loadPhieuNhapKhos(),
    loadHangHoa(),
    loadInventoryForKiemKe(),
  ]);
};

const handleOpenChiTietPhieuNhap = (record) => {
  selectedReceiptForDetail.value = record;
  isChiTietPhieuNhapModalOpen.value = true;
};

const handleSaveDoiTac = (data) => {
  console.log("New supplier:", data);
  // Reload danh sách nhà cung cấp sau khi thêm thành công
  loadNhaCungCaps();
  isThemDoiTacModalOpen.value = false;
};

const handleEditSupplier = (supplier) => {
  selectedSupplierForEdit.value = supplier;
  isSuaNhaCungCapModalOpen.value = true;
};

const handleSaveEditedSupplier = async (data) => {
  console.log("Updated supplier:", data);
  // Đóng modal
  isSuaNhaCungCapModalOpen.value = false;
  // Reload danh sách để lấy dữ liệu mới nhất từ backend
  await loadNhaCungCaps();
};

const handleDeleteSupplier = (supplier) => {
  selectedSupplierForDelete.value = supplier;

  // Mock logic to check for related receipts
  // For demonstration, let's say supplier with ID 2 has related receipts
  if (supplier.id === 2) {
    relatedReceiptsForDelete.value = [
      { id: "PN002", code: "PN002", date: "2024-11-05", amount: 9600000 },
    ];
  } else {
    relatedReceiptsForDelete.value = [];
  }

  isXoaNhaCungCapModalOpen.value = true;
};

const handleConfirmDeleteSupplier = async () => {
  try {
    const response = await deleteNhaCungCap(selectedSupplierForDelete.value.id);

    if (response.status) {
      showSuccessToast(response.message || "Xóa nhà cung cấp thành công");
      isXoaNhaCungCapModalOpen.value = false;
      // Reload danh sách
      await loadNhaCungCaps();
    } else {
      showErrorToast(response.message || "Có lỗi xảy ra khi xóa nhà cung cấp");
    }
  } catch (error) {
    console.error("Error deleting supplier:", error);

    // Xử lý lỗi 400 khi nhà cung cấp có phiếu nhập liên quan
    if (error.response?.status === 400) {
      showErrorToast(
        error.response.data.message ||
          "Không thể xóa nhà cung cấp do có phiếu nhập liên quan"
      );
    } else {
      showErrorToast(
        error.response?.data?.message || "Có lỗi xảy ra khi xóa nhà cung cấp"
      );
    }
  }
};

const handleDeactivateSupplier = () => {
  console.log("Deactivated supplier:", selectedSupplierForDelete.value);
  // Logic to deactivate supplier goes here
  isXoaNhaCungCapModalOpen.value = false;
};

const handleManageCategories = () => {
  isManageCategoriesModalOpen.value = true;
};

const handleAddInventory = () => {
  isAddModalOpen.value = true;
};

// Đổi trạng thái nhà cung cấp
const handleChangeSupplierStatus = async (supplier) => {
  try {
    const response = await changeNhaCungCapStatus(supplier.id);

    if (response && response.status) {
      // Cập nhật trạng thái trong danh sách
      const newStatus =
        response.data.trang_thai === "hoat_dong" ? "active" : "inactive";
      supplier.status = newStatus;
      supplier._original = response.data;

      showSuccessToast(
        response.message || "Đổi trạng thái nhà cung cấp thành công"
      );
    } else {
      showErrorToast(response.message || "Có lỗi xảy ra khi đổi trạng thái");
    }
  } catch (error) {
    console.error("Lỗi đổi trạng thái nhà cung cấp:", error);
    showErrorToast(
      error.response?.data?.message || "Không thể đổi trạng thái nhà cung cấp"
    );
  }
};

// Pagination methods
const goToPreviousPage = () => {
  if (canGoPrevious.value) {
    currentPage.value--;
  }
};

const goToNextPage = () => {
  if (canGoNext.value) {
    currentPage.value++;
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Load dữ liệu hàng hóa cho kiểm kê
const loadInventoryForKiemKe = async () => {
  try {
    const data = await listHangHoa();
    if (data && Array.isArray(data)) {
      // Clear array
      inventoryItems.splice(0, inventoryItems.length);

      // Map dữ liệu từ API
      data.forEach((item) => {
        // Lấy số lượng tồn kho từ backend (tong_so_luong_nhap)
        const stockQuantity = item.tong_so_luong_nhap || 0;

        inventoryItems.push({
          id: item.id,
          hang_hoa_id: item.id,
          name: item.ten_mat_hang,
          code: item.ma_hang_hoa,
          systemQty: stockQuantity, // Lấy từ backend
          actualQty: stockQuantity, // Mặc định bằng số lượng hệ thống
          difference: 0,
          ly_do: null,
          selectedReason: null,
          showReasonDropdown: false,
        });
      });
    }
  } catch (error) {
    console.error("Lỗi tải dữ liệu hàng hóa cho kiểm kê:", error);
    showErrorToast("Không thể tải danh sách hàng hóa cho kiểm kê");
  }
};

// Hàm xử lý khi thay đổi số lượng thực tế
const handleActualQtyChange = (item) => {
  const difference = (item.actualQty || 0) - (item.systemQty || 0);
  item.difference = difference;
};

// Hàm chọn lý do
const selectReason = (item, reason) => {
  console.log("Select reason:", reason, "for item:", item.name);
  item.selectedReason = reason;
  item.ly_do = reason;
  item.showReasonDropdown = false;
  console.log("Reason selected successfully. Item ly_do:", item.ly_do);
};

// Hàm toggle dropdown lý do
const toggleReasonDropdown = (item) => {
  console.log(
    "Toggle dropdown for item:",
    item.name,
    "difference:",
    item.difference
  );

  if (item.difference !== 0) {
    // Đóng tất cả dropdown khác
    inventoryItems.forEach((i) => {
      if (i.id !== item.id) {
        i.showReasonDropdown = false;
      }
    });
    // Toggle dropdown hiện tại
    item.showReasonDropdown = !item.showReasonDropdown;
  } else {
    console.log("Cannot open dropdown: difference is 0");
  }
};

// Hàm cân bằng kho - lưu tất cả các bản ghi kiểm kê có chênh lệch
const handleCanBangKho = async () => {
  try {
    // Lọc các item có chênh lệch
    const itemsToSave = inventoryItems.filter((item) => item.difference !== 0);

    if (itemsToSave.length === 0) {
      showErrorToast("Không có mục nào có chênh lệch để cân bằng");
      return;
    }

    // Kiểm tra xem các item có chênh lệch đã chọn lý do chưa
    const itemsWithoutReason = itemsToSave.filter((item) => !item.ly_do);
    if (itemsWithoutReason.length > 0) {
      showErrorToast("Vui lòng chọn lý do cho tất cả các mục có chênh lệch");
      return;
    }

    // Tạo các bản ghi kiểm kê
    const promises = itemsToSave.map((item) => {
      return createKiemKe({
        hang_hoa_id: item.hang_hoa_id,
        chi_tiet_phieu_nhap_kho_id: null, // Có thể cập nhật nếu cần
        so_luong_he_thong: item.systemQty,
        so_luong_thuc_te: item.actualQty,
        ly_do: item.ly_do,
        ngay_kiem_ke: new Date().toISOString().split("T")[0],
        ghi_chu: null,
      });
    });

    await Promise.all(promises);

    showSuccessToast("Cân bằng kho thành công");

    // Reload dữ liệu
    await loadInventoryForKiemKe();
    await loadHangHoa();
  } catch (error) {
    console.error("Lỗi cân bằng kho:", error);
    showErrorToast(
      error.response?.data?.message || "Có lỗi xảy ra khi cân bằng kho"
    );
  }
};

// Load danh sách kiểm kê đã lưu
const loadKiemKeList = async () => {
  try {
    const response = await getKiemKes();
    if (response && response.status && response.data) {
      kiemKeList.value = response.data;
    }
  } catch (error) {
    console.error("Lỗi tải danh sách kiểm kê:", error);
    // Không hiển thị toast error nếu là lỗi network
    if (error.code !== "ERR_NETWORK") {
      showErrorToast("Không thể tải danh sách kiểm kê");
    }
  }
};
</script>

<style scoped>
/* Remove number input arrows */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}

input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

/* Button Styles */
</style>
