<template>
  <div
    class="bg-white border !border-gray-200/60 rounded-[10px] shadow-lg p-6 w-full max-w-[510px]"
  >
    <!-- Confirm Modal: Delete Service -->
    <div
      v-if="true"
      class="grid grid-cols-1 grid-rows-[56px_80px_minmax(0,1fr)] gap-4"
    >
      <!-- Header -->
      <div class="flex gap-3 items-center">
        <div
          class="bg-[#ffedd4] rounded-full w-12 h-12 flex items-center justify-center"
        >
          <WarningIcon class="w-6 h-6 text-amber-700" />
        </div>
        <h2
          class="font-nunito font-semibold text-lg leading-7 text-neutral-950 tracking-tight"
        >
          Xóa dịch vụ?
        </h2>
      </div>

      <!-- Content -->
      <div class="flex flex-col">
        <p class="font-nunito text-sm leading-5 text-[#717182] tracking-tight">
          Bạn có chắc chắn muốn xóa dịch vụ
          <span class="font-semibold text-[#101828]">{{ service.name }}</span>
          không?
        </p>
        <p
          class="font-nunito text-sm leading-5 text-[#ca3500] tracking-tight mt-3"
        >
          ⚠️ Lưu ý: Hành động này sẽ ẩn dịch vụ khỏi danh sách đặt lịch mới. Các
          hóa đơn và lịch sử cũ liên quan đến dịch vụ này vẫn được giữ nguyên.
        </p>
      </div>

      <!-- Footer Buttons -->
      <div class="flex gap-2 justify-end items-start">
        <button
          class="bg-white border !border-gray-300 rounded-lg px-[17px] py-[9px] hover:bg-gray-50 transition-colors"
          @click="handleClose"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-neutral-950 tracking-tight"
          >
            Hủy
          </span>
        </button>
        <button
          class="bg-[#e7000b] rounded-lg px-4 py-2 hover:bg-[#c00009] transition-colors disabled:opacity-60"
          @click="handleConfirmDelete"
          :disabled="deleting"
        >
          <span
            class="font-nunito font-medium text-sm leading-5 text-white tracking-tight"
          >
            <span v-if="!deleting">Xác nhận xóa</span>
            <span v-else>Đang xóa...</span>
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import api, { attachToken } from "@/utils/api";
import { showSuccessToast, showErrorToast } from "@/utils/toast";
// Icon SVG
import WarningIcon from "@/assets/svg/warning.svg";

const props = defineProps({
  modalType: {
    type: String,
    required: true,
    validator: (value) => ["error", "confirm"].includes(value),
  },
  service: {
    type: Object,
    required: true,
    default: () => ({
      id: "",
      name: "Khám tổng quát",
    }),
  },
  appointments: {
    type: Array,
    default: () => [
      {
        time: "09:00",
        date: "25-11",
        petName: "Milo",
        ownerName: "Nguyễn Văn A",
      },
      {
        time: "10:30",
        date: "25-11",
        petName: "Lu",
        ownerName: "Trần Thị B",
      },
      {
        time: "14:00",
        date: "26-11",
        petName: "Max",
        ownerName: "Lê Văn C",
      },
    ],
  },
});

const emit = defineEmits(["close", "deleted", "confirmDelete"]);

const deleting = ref(false);

// Methods
const handleClose = () => {
  emit("close");
};

const handleConfirmDelete = async () => {
  if (!props.service || !props.service.id) {
    showErrorToast("Lỗi", "Dịch vụ không hợp lệ");
    return;
  }

  deleting.value = true;
  try {
    try {
      attachToken();
    } catch (e) {
      // ignore attach token failure; api helper may handle auth
    }

    const res = await api.delete(`/dich-vu/${props.service.id}`);

    if (res && res.data && res.data.status) {
      showSuccessToast(
        "Thành công",
        res.data.message || "Xóa dịch vụ thành công."
      );
      // emit deleted so parent can refresh list
      emit("deleted", {
        serviceId: props.service.id,
        serviceName: props.service.name,
      });
      // backward-compatible event name used previously
      emit("confirmDelete", {
        serviceId: props.service.id,
        serviceName: props.service.name,
      });
      emit("close");
    } else {
      const msg =
        (res && res.data && res.data.message) || "Có lỗi khi xóa dịch vụ.";
      showErrorToast("Lỗi", msg);
    }
  } catch (e) {
    // Backend returns 400 when linked appointments exist; prefer showing the modal with appointment list
    const msg =
      e && e.response && e.response.data && e.response.data.message
        ? e.response.data.message
        : "Có lỗi khi xóa dịch vụ.";

    // Treat backend errors as generic errors and show a toast.
    showErrorToast("Lỗi", msg);
  } finally {
    deleting.value = false;
  }
};
</script>

<style scoped>
/* Add any additional custom styles if needed */
</style>
