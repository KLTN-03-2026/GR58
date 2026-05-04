import axios from "axios";

export const ROLE_KEYS = {
  customer:    { token: "auth_token_customer",    user: "auth_user_customer" },
  admin:       { token: "auth_token_admin",       user: "auth_user_admin" },
  y_ta:        { token: "auth_token_y_ta",        user: "auth_user_y_ta" },
  bac_si:      { token: "auth_token_bac_si",      user: "auth_user_bac_si" },
  le_tan:      { token: "auth_token_le_tan",      user: "auth_user_le_tan" },
  tro_ly:      { token: "auth_token_tro_ly",      user: "auth_user_tro_ly" },
};

/**
 * Resolve which auth role key to use.
 * - If an explicit role string is passed, use it directly.
 * - Otherwise, derive from the current URL path.
 */
export function resolveRole(role) {
  if (role) return role;
  if (typeof window === "undefined" || !window.location) return "customer";

  const path = window.location.pathname;
  if (path.startsWith("/admin"))        return "admin";
  if (path.startsWith("/nurse"))        return "y_ta";
  if (path.startsWith("/doctor"))       return "bac_si";
  if (path.startsWith("/receptionist")) return "le_tan";
  if (path.startsWith("/assistant"))    return "tro_ly";
  // /staff/login page or /nhan-vien/* — generic slot before role is known
  if (path.startsWith("/staff") || path.startsWith("/nhan-vien")) return "staff";
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

  if (axios.defaults?.headers?.common) {
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

  const loginPages = {
    admin:  "/admin/login",
    y_ta:   "/staff/login",
    bac_si: "/staff/login",
    le_tan: "/staff/login",
    tro_ly: "/staff/login",
    staff:  "/staff/login",
  };
  const target = loginPages[resolvedRole] || "/customer/login";

  try {
    if (router && typeof router.replace === "function") {
      router.replace(target);
    } else {
      window.location.replace(target);
    }
  } catch (e) {
    try { window.location.replace(target); } catch (ee) { /* give up */ }
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
