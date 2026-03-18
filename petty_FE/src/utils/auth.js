import axios from "axios";

const TOKEN_KEY = "auth_token";
const USER_KEY = "auth_user";

export function setAuth(token, user = null, remember = true) {
  try {
    if (remember) {
      localStorage.setItem(TOKEN_KEY, token);
      if (user) localStorage.setItem(USER_KEY, JSON.stringify(user));
      sessionStorage.removeItem(TOKEN_KEY);
      sessionStorage.removeItem(USER_KEY);
    } else {
      sessionStorage.setItem(TOKEN_KEY, token);
      if (user) sessionStorage.setItem(USER_KEY, JSON.stringify(user));
      localStorage.removeItem(TOKEN_KEY);
      localStorage.removeItem(USER_KEY);
    }
  } catch (e) {
    // ignore storage errors
    console.warn("auth: storage error", e);
  }

  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

export function clearAuth() {
  try {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(USER_KEY);
    sessionStorage.removeItem(TOKEN_KEY);
    sessionStorage.removeItem(USER_KEY);
  } catch (e) {
    console.warn("auth: clear storage error", e);
  }

  if (
    axios.defaults &&
    axios.defaults.headers &&
    axios.defaults.headers.common
  ) {
    delete axios.defaults.headers.common["Authorization"];
  }
}

export function getToken() {
  try {
    return (
      localStorage.getItem(TOKEN_KEY) ||
      sessionStorage.getItem(TOKEN_KEY) ||
      null
    );
  } catch (e) {
    return null;
  }
}

export function getUser() {
  try {
    const raw =
      localStorage.getItem(USER_KEY) ||
      sessionStorage.getItem(USER_KEY) ||
      null;
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

export function logout(router) {
  clearAuth();
  // If router provided, navigate to login page; otherwise do a hard navigation
  try {
    // Decide which login page to redirect to based on current path.
    // If the current location is under /admin, send to admin login; otherwise customer login.
    const currentPath =
      typeof window !== "undefined" &&
      window.location &&
      window.location.pathname
        ? window.location.pathname
        : "";
    const target = currentPath.startsWith("/admin")
      ? "/admin/dang-nhap"
      : "/khach-hang/dang-nhap";

    if (router && typeof router.replace === "function") {
      // replace so the current history entry is replaced (reduces chance of going back)
      router.replace(target);
    } else {
      // use location.replace to avoid adding a new history entry
      window.location.replace(target);
    }
  } catch (e) {
    // fallback
    try {
      window.location.replace(
        typeof window !== "undefined" &&
          window.location &&
          window.location.pathname &&
          window.location.pathname.startsWith("/admin")
          ? "/admin/dang-nhap"
          : "/khach-hang/dang-nhap"
      );
    } catch (ee) {
      // give up
    }
  }
}

export default {
  setAuth,
  clearAuth,
  getToken,
  getUser,
  logout,
};
