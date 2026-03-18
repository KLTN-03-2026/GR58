import axios from "axios";
import { getToken, clearAuth, logout } from "./auth";
import { showErrorToast } from "./toast";

const API_BASE = import.meta.env.VITE_API_BASE || "http://127.0.0.1:8000/api";

const client = axios.create({
  baseURL: API_BASE,
  // You can set other defaults here (timeout, headers etc.)
});

// Attach token if present
export function attachToken() {
  const token = getToken();
  if (token)
    client.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  else delete client.defaults.headers.common["Authorization"];
}

// Allow manual set/clear of token
export function setAuthToken(token) {
  if (token)
    client.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  else delete client.defaults.headers.common["Authorization"];
}

attachToken();

// Ensure requests always include the latest token from storage. This avoids cases
// where the token was stored after this module initialized (e.g. user just logged in)
// and the `client` instance would otherwise miss the header.
client.interceptors.request.use(
  (config) => {
    try {
      const token = getToken();
      if (token) {
        if (!config.headers) config.headers = {};
        config.headers["Authorization"] = `Bearer ${token}`;
      } else if (config.headers && config.headers["Authorization"]) {
        delete config.headers["Authorization"];
      }
    } catch (e) {
      // ignore
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Global response handler: if 401, clear local auth (so frontend can redirect)
client.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error && error.response && error.response.status === 401) {
      try {
        // Clear local auth so subsequent guarded routes will redirect
        clearAuth();
        // Inform the user
        try {
          showErrorToast(
            "Không xác thực",
            "Bạn chưa đăng nhập hoặc phiên đã hết hạn. Vui lòng đăng nhập lại."
          );
        } catch (t) {
          // ignore toast errors
        }
        // Redirect to login page
        try {
          // use logout helper which navigates to login
          logout();
        } catch (r) {
          try {
            window.location.replace("/khach-hang/dang-nhap");
          } catch (e) {}
        }
      } catch (e) {
        // ignore
      }
    }
    return Promise.reject(error);
  }
);

export default client;
