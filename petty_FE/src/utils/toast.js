import { useToast } from "vue-toastification";
import MyToast from "@/components/ToastMessage/mytoast.vue";

const DEFAULT_DURATION = 4000;

const DEFAULT_TITLES = {
  success: "Thành công",
  error: "Lỗi",
  info: "Thông báo",
  warning: "Cảnh báo",
  default: "Thông báo",
};

const MESSAGE_KEY_MAP = {
  "messages.login_success": "Đăng nhập thành công",
  "messages.login_failed": "Đăng nhập thất bại",
  "messages.validation_failed": "Vui lòng kiểm tra lại thông tin đã nhập",
  "messages.server_error": "Đã có lỗi hệ thống xảy ra",
  "messages.unauthorized": "Bạn không có quyền thực hiện thao tác này",
  "messages.unauthorized_admin": "Bạn không có quyền truy cập khu vực quản trị",
  "messages.update_success": "Cập nhật thành công",
  "messages.forbidden": "Bạn không có quyền thực hiện thao tác này",
  "messages.not_found": "Không tìm thấy dữ liệu",
  "messages.no_changes": "Không có thay đổi nào để cập nhật",
  social_login_failed: "Đăng nhập mạng xã hội thất bại",
  google_login_failed: "Đăng nhập Google thất bại",
  facebook_login_failed: "Đăng nhập Facebook thất bại",
};

const normalizeToastText = (value) => {
  if (value == null) return "";

  const raw = String(value).trim();
  if (!raw) return "";

  if (MESSAGE_KEY_MAP[raw]) {
    return MESSAGE_KEY_MAP[raw];
  }

  if (raw.startsWith("messages.")) {
    const fallbackKey = raw.replace(/^messages\./, "");
    const fallback = fallbackKey.replace(/_/g, " ").trim();
    return fallback.charAt(0).toUpperCase() + fallback.slice(1);
  }

  return raw;
};

const resolveToastCopy = (type, titleOrMessage, message) => {
  const normalizedType = type || "default";
  const normalizedTitle = normalizeToastText(titleOrMessage);
  const normalizedMessage = normalizeToastText(message);

  if (message == null || message === "") {
    return {
      title: DEFAULT_TITLES[normalizedType] || DEFAULT_TITLES.default,
      message: normalizedTitle,
    };
  }

  return {
    title:
      normalizedTitle || DEFAULT_TITLES[normalizedType] || DEFAULT_TITLES.default,
    message: normalizedMessage,
  };
};

export const createPettyToastContent = (
  type,
  titleOrMessage,
  message,
  duration = DEFAULT_DURATION
) => {
  const { title, message: normalizedMessage } = resolveToastCopy(
    type,
    titleOrMessage,
    message
  );

  return {
    component: MyToast,
    props: {
      title,
      message: normalizedMessage,
      type,
      duration,
      __petty_toast: true,
    },
  };
};

const getToastOptions = (duration = DEFAULT_DURATION) => ({
  icon: false,
  closeButton: false,
  timeout: duration,
});

const showToast = (type, titleOrMessage, message, duration = DEFAULT_DURATION) => {
  const toast = useToast();
  const content = createPettyToastContent(type, titleOrMessage, message, duration);
  toast[type]?.(content, getToastOptions(duration));
};

export const showSuccessToast = (
  titleOrMessage,
  message,
  duration = DEFAULT_DURATION
) => showToast("success", titleOrMessage, message, duration);

export const showErrorToast = (
  titleOrMessage,
  message,
  duration = DEFAULT_DURATION
) => showToast("error", titleOrMessage, message, duration);

export const showInfoToast = (
  titleOrMessage,
  message,
  duration = DEFAULT_DURATION
) => showToast("info", titleOrMessage, message, duration);

export const showWarningToast = (
  titleOrMessage,
  message,
  duration = DEFAULT_DURATION
) => showToast("warning", titleOrMessage, message, duration);

const isPettyToastContent = (content) =>
  content &&
  typeof content === "object" &&
  content.props &&
  content.props.__petty_toast;

export const pettyToastFilterBeforeCreate = (toast, toasts) => {
  void toasts;

  const type = toast.type || "default";
  const duration =
    typeof toast.timeout === "number" ? toast.timeout : DEFAULT_DURATION;

  if (typeof toast.content === "string") {
    return {
      ...toast,
      content: createPettyToastContent(
        type,
        DEFAULT_TITLES[type] || DEFAULT_TITLES.default,
        toast.content,
        duration
      ),
      ...getToastOptions(duration),
    };
  }

  if (isPettyToastContent(toast.content)) {
    return {
      ...toast,
      content: createPettyToastContent(
        type,
        toast.content.props.title,
        toast.content.props.message,
        duration
      ),
      ...getToastOptions(duration),
    };
  }

  return toast;
};

export { normalizeToastText, DEFAULT_TITLES };
