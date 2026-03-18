<template>
  <div class="flex flex-col gap-6 h-full p-6">
    <!-- Loading State -->
    <div
      v-if="loading && !form.name"
      class="flex items-center justify-center py-12"
    >
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
        <div class="flex items-center gap-4">
          <button
            @click="handleBack"
            class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <ArrowLeftIcon />
            <span class="text-sm font-medium text-neutral-950">Quay lại</span>
          </button>
          <div class="flex flex-col">
            <h1 class="text-2xl font-medium text-[#101828]">
              Chỉnh sửa khuyến mãi
            </h1>
            <p class="text-base text-[#4a5565]">
              Thiết lập các điều kiện và mức giảm giá
            </p>
          </div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-2 gap-6">
        <!-- Card 1: Thông tin cơ bản -->
        <div class="bg-white rounded-[14px] border !border-gray-300 p-6">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-base text-neutral-950">1. Thông tin cơ bản</h2>
          </div>

          <div class="flex flex-col gap-4">
            <!-- Tên chương trình -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Tên chương trình
                <span class="text-[#fb2c36]">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                placeholder="VD: Tri ân khách hàng tháng 11"
                class="w-full px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                :class="{ 'border-red-500': errors.name }"
              />
              <p v-if="errors.name" class="text-xs text-red-500">
                {{ errors.name }}
              </p>
            </div>

            <!-- Mô tả ngắn -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Mô tả ngắn (Optional)
              </label>
              <textarea
                v-model="form.description"
                rows="3"
                placeholder="Giải thích về chương trình khuyến mãi này..."
                class="w-full px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm resize-none focus:outline-none focus:ring-2 focus:ring-[#009689]"
              ></textarea>
            </div>

            <!-- Loại khuyến mãi -->
            <div class="flex flex-col gap-3">
              <label class="text-sm font-medium text-neutral-950">
                Loại khuyến mãi
                <span class="text-[#fb2c36]">*</span>
              </label>

              <!-- Option 1: Voucher -->
              <div
                @click="form.type = 'voucher'"
                :class="[
                  'flex flex-col gap-1 p-4 rounded-[10px] border-2 cursor-pointer transition-all',
                  form.type === 'voucher'
                    ? 'bg-teal-50 border-[#009689]'
                    : 'bg-white !border-gray-200 hover:border-gray-300',
                ]"
              >
                <div class="flex items-start gap-3">
                  <div class="flex items-center justify-center w-4 h-4 mt-1">
                    <div
                      :class="[
                        'w-4 h-4 rounded-full border-2 flex items-center justify-center',
                        form.type === 'voucher'
                          ? 'border-[#009689]'
                          : 'border-[#d1d5dc]',
                      ]"
                    >
                      <div
                        v-if="form.type === 'voucher'"
                        class="w-2 h-2 rounded-full bg-[#009689]"
                      ></div>
                    </div>
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <span class="text-base text-[#101828]"
                        >Mã giảm giá (Voucher)</span
                      >
                    </div>
                    <p class="text-sm text-[#4a5565]">
                      Khách hàng phải nhập mã code để áp dụng giảm giá
                    </p>
                  </div>
                </div>
              </div>

              <!-- Option 2: Auto -->
              <div
                @click="form.type = 'auto'"
                :class="[
                  'flex flex-col gap-1 p-4 rounded-[10px] border-2 cursor-pointer transition-all',
                  form.type === 'auto'
                    ? 'bg-teal-50 border-[#009689]'
                    : 'bg-white border-gray-200 hover:border-gray-300',
                ]"
              >
                <div class="flex items-start gap-3">
                  <div class="flex items-center justify-center w-4 h-4 mt-1">
                    <div
                      :class="[
                        'w-4 h-4 rounded-full border-2 flex items-center justify-center',
                        form.type === 'auto'
                          ? 'border-[#009689]'
                          : 'border-[#d1d5dc]',
                      ]"
                    >
                      <div
                        v-if="form.type === 'auto'"
                        class="w-2 h-2 rounded-full bg-[#009689]"
                      ></div>
                    </div>
                  </div>
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                      <span class="text-base text-[#101828]"
                        >Chương trình tự động</span
                      >
                    </div>
                    <p class="text-sm text-[#4a5565]">
                      Hệ thống tự động trừ tiền khi đủ điều kiện
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Mã Code -->
            <div
              v-if="form.type === 'voucher'"
              class="flex flex-col gap-2 p-4 bg-purple-50 border !border-[#e9d4ff] rounded-[10px]"
            >
              <label class="text-sm font-medium text-neutral-950">
                Mã Code
                <span class="text-[#fb2c36]">*</span>
              </label>
              <input
                v-model="form.code"
                type="text"
                placeholder="VD: SALE11, TET2025"
                class="w-full px-3 py-2 bg-white border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                :class="{ 'border-red-500': errors.code }"
              />
              <p v-if="errors.code" class="text-xs text-red-500">
                {{ errors.code }}
              </p>
              <p v-else class="text-xs text-[#4a5565]">
                Gợi ý: Dùng chữ in hoa, dễ nhớ (6-12 ký tự)
              </p>
            </div>
          </div>
        </div>

        <!-- Card 2: Mức giảm giá -->
        <div class="bg-white rounded-[14px] border !border-gray-300 p-6">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-base text-neutral-950">2. Mức giảm giá</h2>
          </div>

          <div class="flex flex-col gap-4">
            <!-- Hình thức giảm -->
            <div class="flex flex-col gap-2 relative">
              <label class="text-sm font-medium text-neutral-950">
                Hình thức giảm
                <span class="text-[#fb2c36]">*</span>
              </label>
              <div
                @click="showDiscountTypeDropdown = !showDiscountTypeDropdown"
                class="flex items-center justify-between px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg cursor-pointer hover:bg-gray-100"
              >
                <div class="flex items-center gap-2">
                  <span class="text-sm text-neutral-950">
                    {{
                      form.discountType === "percent"
                        ? "Giảm theo % (Phần trăm)"
                        : "Giảm theo số tiền cố định"
                    }}
                  </span>
                </div>
                <ChevronDownIcon />
              </div>

              <!-- Dropdown Menu -->
              <div
                v-if="showDiscountTypeDropdown"
                class="absolute top-full left-0 right-0 mt-1 bg-white border !border-gray-300 rounded-lg shadow-lg z-10"
              >
                <button
                  @click="selectDiscountType('percent')"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 rounded-t-lg transition-colors"
                  :class="
                    form.discountType === 'percent'
                      ? 'bg-teal-50 text-[#009689]'
                      : 'text-neutral-950'
                  "
                >
                  Giảm theo % (Phần trăm)
                </button>
                <button
                  @click="selectDiscountType('fixed')"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 rounded-b-lg transition-colors"
                  :class="
                    form.discountType === 'fixed'
                      ? 'bg-teal-50 text-[#009689]'
                      : 'text-neutral-950'
                  "
                >
                  Giảm theo số tiền cố định
                </button>
              </div>
            </div>

            <!-- Giá trị giảm -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Giá trị giảm
                <span class="text-[#fb2c36]">*</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.discountValue"
                  type="number"
                  placeholder="VD: 20"
                  class="w-full px-3 py-2 pr-12 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                  :class="{ 'border-red-500': errors.discountValue }"
                />
                <span
                  class="absolute right-10 top-1/2 -translate-y-1/2 text-[#6a7282]"
                >
                  {{ form.discountType === "percent" ? "%" : "đ" }}
                </span>
              </div>
              <p v-if="errors.discountValue" class="text-xs text-red-500">
                {{ errors.discountValue }}
              </p>
            </div>

            <!-- Giảm tối đa -->
            <div
              v-if="form.discountType === 'percent'"
              class="flex flex-col gap-3 p-4 bg-amber-50 border !border-[#fee685] rounded-[10px]"
            >
              <div class="flex items-start gap-2">
                <div class="flex flex-col gap-1">
                  <label class="text-sm font-medium text-neutral-950">
                    Giảm tối đa (Max Cap)
                  </label>
                  <p class="text-xs text-[#4a5565]">
                    Rất quan trọng để tránh lỗ vốn với đơn hàng lớn!
                  </p>
                </div>
              </div>
              <div class="relative">
                <input
                  v-model="form.maxDiscount"
                  type="number"
                  placeholder="VD: 50000"
                  class="w-full px-3 py-2 pr-12 bg-white border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
                <span
                  class="absolute right-10 top-1/2 -translate-y-1/2 text-[#6a7282]"
                  >đ</span
                >
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: Điều kiện áp dụng -->
        <div class="bg-white rounded-[14px] border !border-gray-300 p-6">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-base text-neutral-950">3. Điều kiện áp dụng</h2>
          </div>

          <div class="flex flex-col gap-4">
            <!-- Áp dụng cho -->
            <div class="flex flex-col gap-2 relative">
              <label class="text-sm font-medium text-neutral-950"
                >Áp dụng cho</label
              >
              <div
                @click="showApplyToDropdown = !showApplyToDropdown"
                class="flex items-center justify-between px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg cursor-pointer hover:bg-gray-100"
              >
                <span class="text-sm text-neutral-950">
                  {{
                    form.applyTo === "all"
                      ? "Toàn bộ đơn hàng"
                      : "Nhóm dịch vụ cụ thể"
                  }}
                </span>
                <ChevronDownIcon />
              </div>

              <!-- Dropdown Menu -->
              <div
                v-if="showApplyToDropdown"
                class="absolute top-full left-0 right-0 mt-1 bg-white border !border-gray-300 rounded-lg shadow-lg z-10"
              >
                <button
                  @click="selectApplyTo('all')"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 rounded-t-lg transition-colors"
                  :class="
                    form.applyTo === 'all'
                      ? 'bg-teal-50 text-[#009689]'
                      : 'text-neutral-950'
                  "
                >
                  Toàn bộ đơn hàng
                </button>
                <button
                  @click="selectApplyTo('specific')"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 rounded-b-lg transition-colors"
                  :class="
                    form.applyTo === 'specific'
                      ? 'bg-teal-50 text-[#009689]'
                      : 'text-neutral-950'
                  "
                >
                  Nhóm dịch vụ cụ thể
                </button>
              </div>
            </div>

            <!-- Chọn dịch vụ áp dụng -->
            <div
              v-if="form.applyTo === 'specific'"
              class="flex flex-col gap-3 p-4 bg-blue-50 border !border-[#bedbff] rounded-[10px]"
            >
              <label class="text-sm font-medium text-neutral-950">
                Chọn dịch vụ áp dụng
              </label>

              <div
                v-if="loadingServices"
                class="flex items-center gap-2 text-sm text-gray-500 py-4"
              >
                <svg
                  class="animate-spin h-5 w-5 text-[#009689]"
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
                <span>Đang tải danh sách dịch vụ...</span>
              </div>

              <div
                v-else-if="services.length === 0"
                class="text-sm text-gray-500 py-4"
              >
                Không có dịch vụ nào. Sử dụng dữ liệu mẫu.
              </div>

              <div
                v-else
                class="flex flex-col gap-2 max-h-[320px] overflow-y-auto"
              >
                <label
                  v-for="service in services"
                  :key="service.id"
                  class="flex items-center gap-3 px-3 py-3 bg-white border border-gray-200 rounded-[10px] cursor-pointer hover:bg-gray-50 transition-colors"
                >
                  <input
                    type="checkbox"
                    v-model="form.selectedServices"
                    :value="service.id"
                    class="w-4 h-4 rounded border-2 border-[#d1d5dc] text-[#009689] focus:ring-[#009689]"
                  />
                  <div class="flex flex-col">
                    <span class="text-sm text-[#101828]">{{
                      service.name
                    }}</span>
                    <span
                      v-if="service.ma_dich_vu"
                      class="text-xs text-gray-500"
                      >{{ service.ma_dich_vu }}</span
                    >
                  </div>
                </label>
              </div>
              <p class="text-xs text-[#4a5565]">
                VD: Chỉ giảm cho Spa, không giảm cho Thuốc
              </p>
            </div>

            <!-- Giá trị đơn tối thiểu -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Giá trị đơn tối thiểu (Optional)
              </label>
              <div class="relative">
                <input
                  v-model="form.minOrderValue"
                  type="number"
                  placeholder="VD: 300000"
                  class="w-full px-3 py-2 pr-12 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                />
                <span
                  class="absolute right-10 top-1/2 -translate-y-1/2 text-[#6a7282]"
                  >đ</span
                >
              </div>
            </div>

            <!-- Đối tượng khách hàng -->
            <div class="flex flex-col gap-2 relative">
              <label class="text-sm font-medium text-neutral-950"
                >Đối tượng khách hàng</label
              >
              <div
                @click="showCustomerTypeDropdown = !showCustomerTypeDropdown"
                class="flex items-center justify-between px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg cursor-pointer hover:bg-gray-100"
              >
                <div class="flex items-center gap-2">
                  <span class="text-sm text-neutral-950">
                    {{
                      form.customerType === "all"
                        ? "Tất cả khách hàng"
                        : "Khách hàng VIP"
                    }}
                  </span>
                </div>
                <ChevronDownIcon />
              </div>

              <!-- Dropdown Menu -->
              <div
                v-if="showCustomerTypeDropdown"
                class="absolute top-full left-0 right-0 mt-1 bg-white border !border-gray-300 rounded-lg shadow-lg z-10"
              >
                <button
                  @click="selectCustomerType('all')"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 rounded-t-lg transition-colors"
                  :class="
                    form.customerType === 'all'
                      ? 'bg-teal-50 text-[#009689]'
                      : 'text-neutral-950'
                  "
                >
                  Tất cả khách hàng
                </button>
                <button
                  @click="selectCustomerType('vip')"
                  class="w-full px-3 py-2 text-left text-sm hover:bg-gray-50 rounded-b-lg transition-colors"
                  :class="
                    form.customerType === 'vip'
                      ? 'bg-teal-50 text-[#009689]'
                      : 'text-neutral-950'
                  "
                >
                  Khách hàng VIP
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 4: Giới hạn & Thời gian -->
        <div class="bg-white rounded-[14px] border !border-gray-300 p-6">
          <div class="flex items-center gap-2 mb-6">
            <h2 class="text-base text-neutral-950">4. Giới hạn & Thời gian</h2>
          </div>

          <div class="flex flex-col gap-4">
            <!-- Thời gian hiệu lực -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Thời gian hiệu lực
                <span class="text-[#fb2c36]">*</span>
              </label>
              <div class="grid grid-cols-2 gap-3">
                <div class="flex flex-col gap-1">
                  <label class="text-xs font-medium text-[#4a5565]"
                    >Từ ngày</label
                  >
                  <input
                    v-model="form.startDate"
                    type="date"
                    class="px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    :class="{ 'border-red-500': errors.startDate }"
                  />
                  <p v-if="errors.startDate" class="text-xs text-red-500">
                    {{ errors.startDate }}
                  </p>
                </div>
                <div class="flex flex-col gap-1">
                  <label class="text-xs font-medium text-[#4a5565]"
                    >Đến ngày</label
                  >
                  <input
                    v-model="form.endDate"
                    type="date"
                    class="px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                    :class="{ 'border-red-500': errors.endDate }"
                  />
                  <p v-if="errors.endDate" class="text-xs text-red-500">
                    {{ errors.endDate }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Tổng số lượng mã -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Tổng số lượng mã
                <span class="text-[#fb2c36]">*</span>
              </label>
              <input
                v-model="form.totalQuantity"
                type="number"
                placeholder="VD: 100"
                class="w-full px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                :class="{ 'border-red-500': errors.totalQuantity }"
              />
              <p v-if="errors.totalQuantity" class="text-xs text-red-500">
                {{ errors.totalQuantity }}
              </p>
              <p v-else class="text-xs text-[#4a5565]">
                Số lượng tối đa khách hàng có thể sử dụng chương trình này
              </p>
            </div>

            <!-- Giới hạn mỗi khách -->
            <div class="flex flex-col gap-2">
              <label class="text-sm font-medium text-neutral-950">
                Giới hạn mỗi khách
                <span class="text-[#fb2c36]">*</span>
              </label>
              <input
                v-model="form.perCustomerLimit"
                type="number"
                placeholder="1"
                class="w-full px-3 py-2 bg-[#f3f3f5] border !border-transparent rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#009689]"
                :class="{ 'border-red-500': errors.perCustomerLimit }"
              />
              <p v-if="errors.perCustomerLimit" class="text-xs text-red-500">
                {{ errors.perCustomerLimit }}
              </p>
              <p v-else class="text-xs text-[#4a5565]">
                Mỗi khách hàng có thể sử dụng bao nhiêu lần
              </p>
            </div>

            <!-- Tóm tắt -->
            <div
              class="flex flex-col gap-2 p-4 bg-teal-50 border !border-[#96f7e4] rounded-[10px]"
            >
              <p class="text-sm font-medium text-[#101828]">
                Tóm tắt cấu hình:
              </p>
              <ul class="flex flex-col gap-1 text-sm text-[#364153]">
                <li>
                  • Loại:
                  <strong>{{
                    form.type === "voucher" ? "Voucher" : "Tự động"
                  }}</strong>
                </li>
                <li>
                  • Đối tượng:
                  <strong>{{
                    form.customerType === "all" ? "Tất cả" : "VIP"
                  }}</strong>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Actions -->
      <div class="flex items-center justify-end gap-3">
        <button
          @click="handleCancel"
          class="px-4 py-2 bg-white border !border-gray-300 rounded-lg text-sm font-medium text-neutral-950 hover:bg-gray-50 transition-colors"
        >
          Hủy
        </button>
        <button
          @click="handleUpdate"
          :disabled="loading"
          class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] text-white rounded-lg text-sm font-medium hover:bg-[#5a9690]/80 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          <span>{{
            loading ? "Đang cập nhật..." : "Cập nhật chương trình"
          }}</span>
        </button>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import { khuyenMaiAPI, dichVuAPI } from "@/utils/khuyenMai";

// Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";

const router = useRouter();
const route = useRoute();

const services = ref([]);
const loading = ref(false);
const loadingServices = ref(false);
const errors = ref({});
const promotionId = ref(null);

// Dropdown states
const showDiscountTypeDropdown = ref(false);
const showApplyToDropdown = ref(false);
const showCustomerTypeDropdown = ref(false);

const form = reactive({
  name: "",
  description: "",
  type: "voucher",
  code: "",
  discountType: "percent",
  discountValue: "",
  maxDiscount: "",
  applyTo: "all",
  selectedServices: [],
  minOrderValue: "",
  customerType: "all",
  startDate: "",
  endDate: "",
  totalQuantity: "",
  perCustomerLimit: "1",
});

const loadServices = async () => {
  loadingServices.value = true;
  try {
    const response = await dichVuAPI.getAll({ per_page: 1000 });

    let servicesData = [];
    if (response.data.success && response.data.data) {
      servicesData = response.data.data.data || response.data.data;
    } else if (response.data.data) {
      servicesData = Array.isArray(response.data.data)
        ? response.data.data
        : [response.data.data];
    } else if (Array.isArray(response.data)) {
      servicesData = response.data;
    }

    if (servicesData && servicesData.length > 0) {
      services.value = servicesData.map((dv) => ({
        id: dv.id,
        name: dv.ten || dv.ten_dich_vu || dv.name,
        ma_dich_vu: dv.ma_dich_vu || dv.code,
      }));
    }
  } catch (error) {
    console.error("Error loading services:", error);
  } finally {
    loadingServices.value = false;
  }
};

const loadPromotion = async (id) => {
  loading.value = true;
  try {
    const response = await khuyenMaiAPI.getById(id);

    if (response.data.success) {
      const data = response.data.data;

      form.name = data.ten_khuyen_mai;
      form.description = data.mo_ta || "";
      form.type = data.loai_khuyen_mai === "ma_giam_gia" ? "voucher" : "auto";
      form.code = data.ma_code || "";
      form.discountType =
        data.hinh_thuc_giam === "phan_tram" ? "percent" : "fixed";
      form.discountValue = data.gia_tri_giam;
      form.maxDiscount = data.giam_toi_da || "";
      form.applyTo =
        data.dich_vus && data.dich_vus.length > 0 ? "specific" : "all";
      form.selectedServices = data.dich_vus
        ? data.dich_vus.map((dv) => dv.id)
        : [];
      form.minOrderValue = data.gia_tri_don_toi_thieu || "";
      form.customerType = data.loai_khach_hang === "tat_ca" ? "all" : "vip";
      form.startDate = data.tu_ngay;
      form.endDate = data.den_ngay;
      form.totalQuantity = data.tong_so_luong;
      form.perCustomerLimit = data.gioi_han_moi_khach;
    }
  } catch (error) {
    console.error("Error loading promotion:", error);
    alert("Có lỗi xảy ra khi tải thông tin khuyến mãi");
    router.back();
  } finally {
    loading.value = false;
  }
};

const validateForm = () => {
  errors.value = {};

  if (!form.name.trim()) {
    errors.value.name = "Tên chương trình là bắt buộc";
  }

  if (form.type === "voucher" && !form.code.trim()) {
    errors.value.code = "Mã code là bắt buộc khi chọn loại Voucher";
  }

  if (!form.discountValue || form.discountValue <= 0) {
    errors.value.discountValue = "Giá trị giảm phải lớn hơn 0";
  }

  if (form.discountType === "percent" && form.discountValue > 100) {
    errors.value.discountValue = "Giá trị giảm % không được vượt quá 100";
  }

  if (!form.startDate) {
    errors.value.startDate = "Ngày bắt đầu là bắt buộc";
  }

  if (!form.endDate) {
    errors.value.endDate = "Ngày kết thúc là bắt buộc";
  }

  if (form.startDate && form.endDate && form.startDate > form.endDate) {
    errors.value.endDate = "Ngày kết thúc phải sau ngày bắt đầu";
  }

  if (!form.totalQuantity || form.totalQuantity <= 0) {
    errors.value.totalQuantity = "Tổng số lượng phải lớn hơn 0";
  }

  if (!form.perCustomerLimit || form.perCustomerLimit <= 0) {
    errors.value.perCustomerLimit = "Giới hạn mỗi khách phải lớn hơn 0";
  }

  return Object.keys(errors.value).length === 0;
};

const mapFormToAPI = () => {
  return {
    ten_khuyen_mai: form.name,
    mo_ta: form.description || null,
    loai_khuyen_mai: form.type === "voucher" ? "ma_giam_gia" : "tu_dong",
    ma_code: form.type === "voucher" ? form.code : null,
    gia_tri_don_toi_thieu: form.minOrderValue || null,
    loai_khach_hang: form.customerType === "all" ? "tat_ca" : "vip",
    hinh_thuc_giam: form.discountType === "percent" ? "phan_tram" : "tien_mat",
    giam_toi_da: form.discountType === "percent" ? form.maxDiscount : null,
    gia_tri_giam: parseFloat(form.discountValue),
    tu_ngay: form.startDate,
    den_ngay: form.endDate,
    tong_so_luong: parseInt(form.totalQuantity),
    gioi_han_moi_khach: parseInt(form.perCustomerLimit),
    dich_vu_ids: form.applyTo === "specific" ? form.selectedServices : [],
  };
};

const handleBack = () => {
  router.back();
};

// Dropdown select methods
const selectDiscountType = (type) => {
  form.discountType = type;
  showDiscountTypeDropdown.value = false;
};

const selectApplyTo = (value) => {
  form.applyTo = value;
  showApplyToDropdown.value = false;
};

const selectCustomerType = (type) => {
  form.customerType = type;
  showCustomerTypeDropdown.value = false;
};

const handleCancel = () => {
  if (confirm("Bạn có chắc chắn muốn hủy? Các thay đổi sẽ không được lưu.")) {
    router.back();
  }
};

const handleUpdate = async () => {
  if (!validateForm()) {
    alert("Vui lòng kiểm tra lại thông tin!");
    return;
  }

  loading.value = true;

  try {
    const apiData = mapFormToAPI();
    const response = await khuyenMaiAPI.update(promotionId.value, apiData);

    if (response.data.success) {
      alert("Cập nhật khuyến mãi thành công!");
      router.push("/admin/khuyen-mai");
    } else {
      alert("Có lỗi xảy ra: " + (response.data.message || "Unknown error"));
    }
  } catch (error) {
    console.error("Error updating promotion:", error);

    if (error.response?.data?.errors) {
      const backendErrors = error.response.data.errors;
      let errorMessage = "Lỗi:\n";
      for (const [field, messages] of Object.entries(backendErrors)) {
        errorMessage += `- ${messages.join(", ")}\n`;
      }
      alert(errorMessage);
    } else if (error.response?.data?.message) {
      alert("Có lỗi xảy ra: " + error.response.data.message);
    } else {
      alert("Có lỗi xảy ra khi cập nhật khuyến mãi!");
    }
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  promotionId.value = route.params.id;
  if (promotionId.value) {
    await loadServices();
    await loadPromotion(promotionId.value);
  } else {
    alert("Không tìm thấy ID khuyến mãi");
    router.back();
  }
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
</style>
