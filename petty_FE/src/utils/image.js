const BASE_URL = (import.meta.env.VITE_API_BASE || 'http://localhost:8000/api').replace(/\/api\/?$/, '');

export function resolveImageUrl(path, fallback = null) {
  if (!path) return fallback;
  if (path.startsWith('http://') || path.startsWith('https://') || path.startsWith('data:')) {
    // Full URL — re-base to current backend if it's a localhost URL with different port
    const currentHost = new URL(BASE_URL).origin;
    try {
      const parsed = new URL(path);
      if (parsed.hostname === 'localhost' || parsed.hostname === '127.0.0.1') {
        return `${currentHost}${parsed.pathname}`;
      }
    } catch {
      // Not a valid URL, fall through
    }
    return path;
  }
  if (path.startsWith('storage/') || path.startsWith('images/')) {
    return `${BASE_URL}/${path}`;
  }
  return `${BASE_URL}/storage/${path.replace(/^\//, '')}`;
}

export default { resolveImageUrl };
