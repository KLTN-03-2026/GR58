# Frontend — Danh Mục Hàng Hoá (integration notes)

This small doc explains how the frontend component for `DanhMucHangHoa` (product categories) connects to the backend API and how to test it locally.

Files changed/added

- `src/utils/danhMucHangHoa.js` — API helper (list, show, create, update, delete)
- `src/components/Admin/.../DanhMucHangHoa/index.vue` — component now uses the API instead of static data

Environment

- The frontend uses the env var `VITE_API_BASE` to target the backend API. Default is `http://127.0.0.1:8000/api` when not set.

How to configure

1. Create (or update) `.env` or `.env.local` at the project root with:

```
VITE_API_BASE=http://your-backend-host:8000/api
```

2. Authentication: The project stores an auth token under `auth_token` in localStorage/sessionStorage. The existing `src/utils/auth.js` exposes `setAuth`/`getToken`. If you log in via the app, the token will be stored and the axios client will attach it automatically.

Testing the Danh Mục Hàng Hoá UI

1. Start the frontend dev server (e.g. `npm run dev`).
2. Ensure your backend is running and the API routes in the BE match the paths described in the controller and routes.
3. Open the Danh Muc dialog in the app. The component will call GET `/danh-muc-hang-hoa` to load categories.
4. Create: filling the form and tapping "Thêm danh mục" will POST to `/danh-muc-hang-hoa`.
5. Delete: clicking the delete icon will DELETE `/danh-muc-hang-hoa/{id}` (you'll be asked to confirm).

Notes & next steps

- Update/add editing of a category (PUT/PATCH) if needed.
- Add pagination/filtering parameters to `listDanhMucHangHoa` if the backend returns paginated results (the helper currently expects an array in `response.data.data`).
- If using Laravel Sanctum cookie-based auth instead of Bearer tokens, adjust `src/utils/api.js` to send cookies and CSRF token (set `withCredentials: true`).
