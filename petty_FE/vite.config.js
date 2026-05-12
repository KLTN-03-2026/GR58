import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import svgLoader from "vite-svg-loader";
import { fileURLToPath, URL } from "node:url";

export default defineConfig(({ mode }) => {
  // Load env file based on `mode` in the current working directory.
  const env = loadEnv(mode, process.cwd(), '');

  // Get API base URL from env, default to localhost:8001
  const apiBase = env.VITE_API_BASE || 'http://localhost:8001/api';
  const apiTarget = apiBase.replace('/api', '');

  return {
    plugins: [
      vue(),
      svgLoader(), // Cho phép import SVG như component
    ],
    // Dev server proxy to forward API calls to backend (Laravel)
    server: {
      proxy: {
        // Forward /khoa requests to Laravel backend
        "/khoa": {
          target: apiTarget,
          changeOrigin: true,
          secure: false,
        },
        // Optional: forward other api routes prefixed with /api
        "/api": {
          target: apiTarget,
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
  };
});
