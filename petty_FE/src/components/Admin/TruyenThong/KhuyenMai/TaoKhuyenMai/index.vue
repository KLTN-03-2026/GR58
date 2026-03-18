<template>
  <div class="flex flex-col gap-6 h-full p-6">
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
            Tạo khuyến mãi mới
          </h1>
          <p class="text-base text-[#4a5565]">
            Thiết lập các điều kiện và mức giảm giá
          </p>
        </div>
      </div>
      <!-- <button
        @click="handleSave"
        :disabled="loading"
        class="flex items-center gap-2 px-4 py-2 bg-[#00a63e] text-white rounded-lg hover:bg-[#008c35] transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
      >
        <img :src="iconSave" alt="Save" class="w-4 h-4" />
        <span class="text-sm font-medium">{{
          loading ? "Đang lưu..." : "Lưu chương trình"
        }}</span>
      </button> -->
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-2 gap-6">
      <!-- Card 1: Thông tin cơ bản -->
      <div class="bg-white rounded-[14px] border !border-gray-300 p-6">
        <div class="flex items-center gap-2 mb-6">
          <!-- <img :src="iconInfo" alt="Info" class="w-5 h-5" /> -->
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
                    <!-- <img :src="iconVoucher" alt="Voucher" class="w-4 h-4" /> -->
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
                    <!-- <img :src="iconAuto" alt="Auto" class="w-4 h-4" /> -->
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

          <!-- Mã Code (chỉ hiện khi chọn voucher) -->
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
          <!-- <img :src="iconDiscount" alt="Discount" class="w-5 h-5" /> -->
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

          <!-- Giảm tối đa (Max Cap) -->
          <div
            v-if="form.discountType === 'percent'"
            class="flex flex-col gap-3 p-4 bg-amber-50 border !border-[#fee685] rounded-[10px]"
          >
            <div class="flex items-start gap-2">
              <!-- <img :src="iconWarning" alt="Warning" class="w-5 h-5 mt-0.5" /> -->
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
          <!-- <img :src="iconCondition" alt="Condition" class="w-5 h-5" /> -->
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

          <!-- Chọn dịch vụ áp dụng (chỉ hiện khi chọn specific) -->
          <div
            v-if="form.applyTo === 'specific'"
            class="flex flex-col gap-3 p-4 bg-blue-50 border !border-[#bedbff] rounded-[10px]"
          >
            <label class="text-sm font-medium text-neutral-950">
              Chọn dịch vụ áp dụng
            </label>

            <!-- Loading state -->
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

            <!-- No services state -->
            <div
              v-else-if="services.length === 0"
              class="text-sm text-gray-500 py-4"
            >
              Không có dịch vụ nào. Sử dụng dữ liệu mẫu.
            </div>

            <!-- Services list -->
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
                  <span class="text-sm text-[#101828]">{{ service.name }}</span>
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
          <!-- <img :src="iconCalendar" alt="Calendar" class="w-5 h-5" /> -->
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

          <!-- Tóm tắt cấu hình -->
          <div
            class="flex flex-col gap-2 p-4 bg-teal-50 border !border-[#96f7e4] rounded-[10px]"
          >
            <p class="text-sm font-medium text-[#101828]">Tóm tắt cấu hình:</p>
            <ul class="flex flex-col gap-1 text-sm text-[#364153]">
              <li>
                • Loại:
                <strong class="font-bold">{{
                  form.type === "voucher" ? "Voucher" : "Tự động"
                }}</strong>
              </li>
              <li>
                • Đối tượng:
                <strong class="font-bold">{{
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
        @click="handleCreate"
        :disabled="loading"
        class="flex items-center gap-2 px-4 py-2 bg-[#5a9690] text-white rounded-lg text-sm font-medium hover:bg-[#5a9690]/80 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
      >
        <!-- <img :src="iconSave" alt="Save" class="w-4 h-4" /> -->
        <span>{{ loading ? "Đang tạo..." : "Tạo chương trình" }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { khuyenMaiAPI, dichVuAPI } from "@/utils/khuyenMai";
//Icon SVG
import ArrowLeftIcon from "@/assets/svg/arrow-left.svg";
import ChevronDownIcon from "@/assets/svg/chevron-down.svg";
const router = useRouter();

// Icons - Replace with your actual icon paths
const iconBack =
  "https://www.figma.com/api/mcp/asset/2833a072-3c1e-473f-a7d0-f72972a54afc";
const iconSave =
  "https://www.figma.com/api/mcp/asset/ce07e089-dce1-41d2-8b67-2f7b95cf5f37";
const iconInfo =
  "https://www.figma.com/api/mcp/asset/60e4ee2a-bb38-4b7e-a342-76c4d4b743d0";
const iconVoucher =
  "https://www.figma.com/api/mcp/asset/04c81bd0-4298-4ef5-95b9-86be400f1674";
const iconAuto =
  "https://www.figma.com/api/mcp/asset/cd8c94c8-61d8-4a57-98a7-97a2f2cf252d";
const iconDiscount =
  "https://www.figma.com/api/mcp/asset/f3120024-852b-4274-b327-b93e347d11f6";
const iconPercent =
  "https://www.figma.com/api/mcp/asset/e0de8752-5c0c-49fe-ac0a-113be9b9329d";
const iconChevronDown =
  "https://www.figma.com/api/mcp/asset/0ba16cdf-9a22-4228-88c1-36c8b7fe134d";
const iconWarning =
  "https://www.figma.com/api/mcp/asset/325694c3-5085-44a0-b711-2619e613aa31";
const iconCondition =
  "https://www.figma.com/api/mcp/asset/10c0e793-cad4-4588-83be-8f9c526e34ee";
const iconUsers =
  "https://www.figma.com/api/mcp/asset/fd23a3d6-0a39-4038-a357-75f8d609d118";
const iconCalendar =
  "https://www.figma.com/api/mcp/asset/d33dc2e7-262c-45b7-8d08-ae09b640c221";

// Services list
const services = ref([]);
const loading = ref(false);
const loadingServices = ref(false);
const errors = ref({});

// Dropdown states
const showDiscountTypeDropdown = ref(false);
const showApplyToDropdown = ref(false);
const showCustomerTypeDropdown = ref(false);

// Form data
const form = reactive({
  name: "",
  description: "",
  type: "voucher", // 'voucher' or 'auto' -> map to 'ma_giam_gia' or 'tu_dong'
  code: "",
  discountType: "percent", // 'percent' or 'fixed' -> map to 'phan_tram' or 'tien_mat'
  discountValue: "",
  maxDiscount: "",
  applyTo: "all", // 'all' or 'specific'
  selectedServices: [], // Array of selected service IDs
  minOrderValue: "",
  customerType: "all", // 'all' or 'vip' -> map to 'tat_ca' or 'vip'
  startDate: "",
  endDate: "",
  totalQuantity: "",
  perCustomerLimit: "1",
});

// Load dịch vụ từ API
const loadServices = async () => {
  loadingServices.value = true;
  try {
    const response = await dichVuAPI.getAll({ per_page: 1000 });
    console.log("API Response:", response.data);

    // Xử lý nhiều cấu trúc response khác nhau
    let servicesData = [];

    if (response.data.success && response.data.data) {
      // Format: { success: true, data: { data: [...] } }
      servicesData = response.data.data.data || response.data.data;
    } else if (response.data.data) {
      // Format: { data: [...] }
      servicesData = Array.isArray(response.data.data)
        ? response.data.data
        : [response.data.data];
    } else if (Array.isArray(response.data)) {
      // Format: [...]
      servicesData = response.data;
    }

    if (servicesData && servicesData.length > 0) {
      services.value = servicesData.map((dv) => ({
        id: dv.id,
        name: dv.ten || dv.ten_dich_vu || dv.name,
        ma_dich_vu: dv.ma_dich_vu || dv.code,
      }));
      console.log("Loaded services:", services.value);
    } else {
      throw new Error("No services data found");
    }
  } catch (error) {
    console.error("Error loading services:", error);
    console.error("Error details:", error.response?.data);

    // Fallback to default services
    services.value = [
      { id: 1, name: "Dịch vụ Spa" },
      { id: 2, name: "Cắt tỉa lông" },
      { id: 3, name: "Khám tổng quát" },
      { id: 4, name: "Tiêm phòng" },
      { id: 5, name: "Phẫu thuật" },
      { id: 6, name: "Thuốc & Vật tư" },
    ];
    console.log("Using fallback services");
  } finally {
    loadingServices.value = false;
  }
};

// Validate form
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

// Map form data to API format
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
    trang_thai: "dang_chay", // Mặc định là đang chạy
  };
};

// Methods
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

// Keep old toggle methods for compatibility
const toggleDiscountType = () => {
  form.discountType = form.discountType === "percent" ? "fixed" : "percent";
};

const toggleApplyTo = () => {
  form.applyTo = form.applyTo === "all" ? "specific" : "all";
};

const toggleCustomerType = () => {
  form.customerType = form.customerType === "all" ? "vip" : "all";
};

const handleCancel = () => {
  if (confirm("Bạn có chắc chắn muốn hủy? Các thay đổi sẽ không được lưu.")) {
    router.back();
  }
};

const handleSave = async () => {
  await handleCreate();
};

const handleCreate = async () => {
  // Validate form
  if (!validateForm()) {
    alert("Vui lòng kiểm tra lại thông tin!");
    console.error("Validation errors:", errors.value);
    return;
  }

  loading.value = true;

  try {
    const apiData = mapFormToAPI();
    console.log("Creating promotion with data:", apiData);

    const response = await khuyenMaiAPI.create(apiData);

    if (response.data.success) {
      alert("Tạo khuyến mãi thành công!");
      router.push("/admin/khuyen-mai");
    } else {
      alert("Có lỗi xảy ra: " + (response.data.message || "Unknown error"));
    }
  } catch (error) {
    console.error("Error creating promotion:", error);

    if (error.response?.data?.errors) {
      // Validation errors from backend
      const backendErrors = error.response.data.errors;
      let errorMessage = "Lỗi:\n";
      for (const [field, messages] of Object.entries(backendErrors)) {
        errorMessage += `- ${messages.join(", ")}\n`;
      }
      alert(errorMessage);
    } else if (error.response?.data?.message) {
      alert("Có lỗi xảy ra: " + error.response.data.message);
    } else {
      alert("Có lỗi xảy ra khi tạo khuyến mãi. Vui lòng thử lại!");
    }
  } finally {
    loading.value = false;
  }
};

// Load data on mount
onMounted(() => {
  loadServices();
});
</script>

<style scoped>
/* Custom scrollbar for the form */
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
