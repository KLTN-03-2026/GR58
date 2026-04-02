import axios from "axios";

export const ROLE_KEYS = {
  customer: { token: "auth_token_customer", user: "auth_user_customer" },
  staff: { token: "auth_token_staff", user: "auth_user_staff" },
  admin: { token: "auth_token_admin", user: "auth_user_admin" },
};

export function resolveRole(role) {
  if (role) return role;
  if (typeof window === "undefined" || !window.location) return "customer";

  const path = window.location.pathname;
  if (path.startsWith("/admin")) return "admin";
  if (path.startsWith("/nhan-vien") || path.startsWith("/doctor") || path.startsWith("/nurse") || path.startsWith("/receptionist") || path.startsWith("/assistant")) return "staff";
  return "customer";
}

function getKeys(role) {
  const r = resolveRole(role);
  return ROLE_KEYS[r] || ROLE_KEYS.customer;
}

export function setAuth(token, user = null, remember = true, role = "customer") {
  const keys = getKeys(role);
  try {
    if (remember) {
      localStorage.setItem(keys.token, token);
      if (user) localStorage.setItem(keys.user, JSON.stringify(user));
      sessionStorage.removeItem(keys.token);
      sessionStorage.removeItem(keys.user);
    } else {
      sessionStorage.setItem(keys.token, token);
      if (user) sessionStorage.setItem(keys.user, JSON.stringify(user));
      localStorage.removeItem(keys.token);
      localStorage.removeItem(keys.user);
    }
  } catch (e) {
    console.warn("auth: storage error", e);
  }

  if (token) axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

export function clearAuth(role) {
  const keys = getKeys(role);
  try {
    localStorage.removeItem(keys.token);
    localStorage.removeItem(keys.user);
    sessionStorage.removeItem(keys.token);
    sessionStorage.removeItem(keys.user);
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

export function getToken(role) {
  const keys = getKeys(role);
  try {
    return (
      localStorage.getItem(keys.token) ||
      sessionStorage.getItem(keys.token) ||
      null
    );
  } catch (e) {
    return null;
  }
}

export function getUser(role) {
  const keys = getKeys(role);
  try {
    const raw =
      localStorage.getItem(keys.user) ||
      sessionStorage.getItem(keys.user) ||
      null;
    return raw ? JSON.parse(raw) : null;
  } catch (e) {
    return null;
  }
}

export function logout(router, role) {
  const resolvedRole = resolveRole(role);
  clearAuth(resolvedRole);
  
  let target = "/customer/login";
  if (resolvedRole === "admin") target = "/admin/login";
  else if (resolvedRole === "staff") target = "/staff/login";

  try {
    if (router && typeof router.replace === "function") {
      router.replace(target);
    } else {
      window.location.replace(target);
    }
  } catch (e) {
    try {
      window.location.replace(target);
    } catch (ee) {
      // give up
    }
  }
}

export default {
  ROLE_KEYS,
  resolveRole,
  setAuth,
  clearAuth,
  getToken,
  getUser,
  logout,
};
