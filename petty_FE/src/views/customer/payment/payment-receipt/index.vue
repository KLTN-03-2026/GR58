<template>
  <div
    v-if="isOpen"
    class="bg-white border border-black/15 rounded-[10px] p-6 flex flex-col gap-4 max-w-[512px] shadow-lg"
  >
    <!-- Header -->
    <div class="flex flex-col gap-2">
      <div class="h-7 relative">
        <div class="flex items-center justify-between">
          <h2 class="font-bold text-lg leading-7 text-black">
            Biên lai thanh toán
          </h2>
          <button @click="closePopup" class="w-7 h-7">
            <img :src="iconClose" alt="Close" class="w-full h-full" />
          </button>
        </div>
      </div>
      <p class="font-medium text-sm leading-5 text-gray-500">
        Hóa đơn: {{ receiptData.invoiceCode }}
      </p>
    </div>

    <!-- Receipt Content -->
    <div class="bg-white border border-black/15 rounded-[10px] p-6 flex flex-col gap-6">
      <!-- Clinic Info -->
      <div class="border-b border-black/15 pb-1 flex flex-col items-center">
        <p class="font-medium text-sm leading-5 text-teal-500 text-center">
          PHÒNG KHÁM PETTY
        </p>
        <p class="font-medium text-sm leading-5 text-gray-600 text-center">
          123 Đường ABC, Quận 1, TP.HCM
        </p>
        <p class="font-medium text-sm leading-5 text-gray-600 text-center">
          Điện thoại: 0123 456 789
        </p>
      </div>

      <!-- Invoice Details Grid -->
      <div class="grid grid-cols-2 gap-4">
        <div class="flex flex-col">
          <p class="font-medium text-sm leading-6 text-gray-600">
            Mã hóa đơn:
          </p>
          <p class="font-medium text-sm leading-6 text-black">
            {{ receiptData.invoiceCode }}
          </p>
        </div>
        <div class="flex flex-col">
          <p class="font-medium text-sm leading-6 text-gray-600">
            Ngày khám:
          </p>
          <p class="font-medium text-sm leading-6 text-black">
            {{ receiptData.visitDate }}
          </p>
        </div>
        <div class="flex flex-col">
          <p class="font-medium text-sm leading-6 text-gray-600">
            Thú cưng:
          </p>
          <p class="font-medium text-sm leading-6 text-black">
            {{ receiptData.petName }}
          </p>
        </div>
        <div class="flex flex-col">
          <p class="font-medium text-sm leading-6 text-gray-600">
            Bác sĩ:
          </p>
          <p class="font-medium text-sm leading-6 text-black">
            {{ receiptData.doctor }}
          </p>
        </div>
      </div>

      <!-- Divider -->
      <div class="bg-black/15 h-px w-full"></div>

      <!-- Service Table -->
      <div class="flex flex-col gap-3">
        <p class="font-medium text-sm leading-6 text-black">
          Chi tiết dịch vụ
        </p>
        <div class="flex flex-col">
          <!-- Table Header -->
          <div class="border-b border-black/15 h-10 flex items-center">
            <div class="flex-[3] px-2">
              <p class="font-semibold text-base leading-6 text-black">
                Dịch vụ
              </p>
            </div>
            <div class="flex-[2] px-2">
              <p class="font-semibold text-base leading-6 text-black text-right">
                Đơn giá
              </p>
            </div>
          </div>
          <!-- Table Body -->
          <div
            v-for="service in receiptData.services"
            :key="service.id"
            class="border-b border-black/15 h-[37px] flex items-center"
          >
            <div class="flex-[3] px-2">
              <p class="font-medium text-sm leading-5 text-black">
                {{ service.name }}
              </p>
            </div>
            <div class="flex-[2] px-2">
              <p class="font-medium text-sm leading-5 text-black text-right">
                {{ formatCurrency(service.price) }}
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="bg-black/15 h-px w-full"></div>

      <!-- Summary -->
      <div class="flex flex-col gap-2">
        <div class="flex justify-between">
          <p class="font-medium text-sm leading-6 text-gray-700">
            Tổng cộng:
          </p>
          <p class="font-medium text-sm leading-6 text-gray-700">
            {{ formatCurrency(receiptData.totalAmount) }}
          </p>
        </div>
        <div class="flex justify-between">
          <p class="font-medium text-sm leading-6 text-gray-700">
            Đã thanh toán:
          </p>
          <p class="font-medium text-sm leading-6 text-gray-700">
            {{ formatCurrency(receiptData.paidAmount) }}
          </p>
        </div>
        <div class="bg-black/15 h-px w-full"></div>
        <div class="flex justify-between items-center">
          <p class="font-medium text-sm leading-6 text-teal-600">
            Trạng thái:
          </p>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4">
              <img :src="iconCheck" alt="" class="w-full h-full" />
            </div>
            <p class="font-medium text-sm leading-5 text-teal-600">
              Đã thanh toán trước {{ formatCurrency(receiptData.prepaidAmount) }}
            </p>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="border-t border-black/15 pt-4 flex flex-col items-center">
        <p class="font-medium text-sm leading-5 text-gray-600 text-center">
          Cảm ơn quý khách đã sử dụng dịch vụ!
        </p>
        <p class="font-medium text-sm leading-5 text-gray-600 text-center">
          Biên lai này được tạo tự động bởi hệ thống
        </p>
      </div>
    </div>

    <!-- Download Button -->
    <div class="flex justify-end">
      <button
        @click="downloadPDF"
        class="bg-[#5a9690] rounded-lg px-4 py-1.5 flex items-center gap-2"
      >
        <div class="w-4 h-4">
          <img :src="iconDownload" alt="" class="w-full h-full" />
        </div>
        <span class="font-semibold text-base leading-6 text-white">
          Tải xuống PDF
        </span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  receiptData: {
    type: Object,
    default: () => ({
      invoiceCode: 'HD001228',
      visitDate: '25/10/2025',
      petName: 'Luna',
      doctor: 'BS. Trần Văn D',
      services: [
        { id: 1, name: 'Tiêm phòng 6 bệnh', price: 150000 },
        { id: 2, name: 'Khám sức khỏe', price: 100000 },
        { id: 3, name: 'Thuốc tăng cường', price: 100000 }
      ],
      totalAmount: 350000,
      paidAmount: 350000,
      prepaidAmount: 200000
    })
  }
});

const emit = defineEmits(['close']);

// Icon URLs
const iconClose = "https://www.figma.com/api/mcp/asset/af922fda-7ca4-4c91-bf67-593cd1f91b9a";
const iconCheck = "https://www.figma.com/api/mcp/asset/7be948c9-3d50-4bea-b240-c59e7179252f";
const iconDownload = "https://www.figma.com/api/mcp/asset/ed2e3411-afd3-429e-bff4-04f7803117c5";

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount) + ' ₫';
};

const closePopup = () => {
  emit('close');
};

const downloadPDF = () => {
  console.log('Download PDF');
  // TODO: Implement PDF download
  alert('Chức năng tải PDF đang được phát triển');
};
</script>
