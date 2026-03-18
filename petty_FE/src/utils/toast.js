import { useToast } from "vue-toastification";
import MyToast from "@/components/ToastMessage/mytoast.vue";

const makePayload = (type, title, message, duration) => ({
  component: MyToast,
  props: {
    title,
    message,
    type,
    duration,
  },
});

export const showSuccessToast = (title, message, duration = 4000) => {
  const toast = useToast();
  toast.success(makePayload("success", title, message, duration), {
    icon: false,
    closeButton: false,
    timeout: duration,
  });
};

export const showErrorToast = (title, message, duration = 4000) => {
  const toast = useToast();
  toast.error(makePayload("error", title, message, duration), {
    icon: false,
    closeButton: false,
    timeout: duration,
  });
};

export const showInfoToast = (title, message, duration = 4000) => {
  const toast = useToast();
  toast.info(makePayload("info", title, message, duration), {
    icon: false,
    closeButton: false,
    timeout: duration,
  });
};
