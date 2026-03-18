import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import svgLoader from "vite-svg-loader";
import { fileURLToPath, URL } from "node:url";

export default defineConfig({
  plugins: [
    vue(),
    svgLoader(), // Cho phép import SVG như component
  ],
  // Dev server proxy to forward API calls to backend (Laravel)
  // Adjust target to your backend address (e.g. http://127.0.0.1:8000)
  server: {
    proxy: {
      // Forward /khoa requests to Laravel backend
      "/khoa": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false,
      },
      // Optional: forward other api routes prefixed with /api
      "/api": {
        target: "http://127.0.0.1:8000",
        changeOrigin: true,
        secure: false,
        rewrite: (path) => path.replace(/^\/api/, "/api"),
      },
    },
  },
  resolve: {
    alias: {
      "@": fileURLToPath(new URL("./src", import.meta.url)),
    },
  },
});
