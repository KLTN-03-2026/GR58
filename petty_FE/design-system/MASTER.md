# TARGET: PETTY SaaS - MASTER DESIGN SYSTEM

## PATTERN: Admin Dashboard / B2B Management System
*   **Type:** Clinical / Pet Clinic / Booking SaaS
*   **Architecture:** Sidebar Navigation (metisMenu) + Topbar + Content View

## STYLE: Clean B2B / Soft UI
*   **Keywords:** Chuyên nghiệp, gọn gàng, hệ thống, y tế/thú y, đáng tin cậy.
*   **Frameworks:** Vue 3 + Tailwind CSS (kèm core layout từ Bootstrap theme).

## COLORS (tailwind.config.js & Vue components)
*   **Primary (Brand):** `#5A9690` (Màu chính) | Hover: `#4A7F79`
*   **Teal (Action Primary):** `#009689` (Teal 900) | Hover: `#008177`
*   **Teal Alternates:** `#2dd4bf` (Teal 400), `#0d9488` (Teal 600), `#2f5755`
*   **Text Hierarchy:**
    *   Text Dark (H1, Titles): `#432323`
    *   Text Medium (Body): `#393E46`
    *   Text Light: `#222831`
*   **Backgrounds & Surfaces:**
    *   App Background: `#F8FAFC` (Gray 50)
    *   Card/Box Surface: `#FFFFFF` (White)
    *   Borders/Lines: `#E0D9D9` (Gray 300) / `#EEEEEE` (Gray 200)

## TYPOGRAPHY
*   **Font chính (Sans):** "Nunito Sans", sans-serif (Mặc định cho Body)
*   **Font phụ:** "Nunito", sans-serif
*   **Font nhấn (Logo/H1):** "Montserrat Alternates", sans-serif

## KEY EFFECTS & SHAPES
*   **Corners (Bo góc):** `rounded-lg` (8px) cho cho Card, Modal, Button
*   **Elevation (Bóng):** `shadow` (Shadow viền nhẹ, soft shadow cho background trắng)
*   **Transitions:** `transition-colors duration-200` (Hover button, action bar)
*   **Flex/Gap:** Thường xuyên dùng `flex items-center gap-2` cho nhóm các icon + chữ.

## COMPONENTS (Quy ước bắt buộc)
*   **Action Bar:** Background trắng (`bg-white`), bo góc (`rounded-lg`), bóng nhỏ (`shadow`), padding `p-4`, margin bottom `mb-6`.
*   **Nút bấm (Primary Button):** `bg-[#009689] text-white px-4 py-2 rounded-lg hover:bg-[#008177] transition-colors`
*   **Alerts/Status Messages:**
    *   Success: `bg-green-50 text-green-700 p-4 rounded-lg`
    *   Error: `bg-red-50 text-red-700 p-4 rounded-lg`

## AVOID (Anti-patterns cho dev sau này)
*   [ ] Không sử dụng shadow quá gắt hoặc màu viền quá đậm, làm mất chất Soft UI
*   [ ] Không nhúng CSS in-line (Style tag hạn chế), ưu tiên 100% Tailwind class
*   [ ] Tránh conflict giữa class Bootstrap cũ (trong layout) và Tailwind CSS. Layout nên dùng grid/flex của Tailwind thay thế dần row/col của Bootstrap.