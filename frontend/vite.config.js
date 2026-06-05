import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  server: {
    proxy: {
      '/api': {
        target: 'https://playconnect-backend.ddev.site',
        changeOrigin: true,
        secure: false, // Accept self-signed DDEV certs
        rewrite: (path) => path
      },
      '/storage': {
        target: 'https://playconnect-backend.ddev.site',
        changeOrigin: true,
        secure: false
      }
    }
  }
})

