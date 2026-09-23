import tailwindcss from '@tailwindcss/vite'
import react from '@vitejs/plugin-react'
import { defineConfig } from 'vite'
import { resolve } from 'node:path'

export default defineConfig({
  plugins: [tailwindcss(), react()],
  resolve: {
    alias: { '@': resolve(import.meta.dirname, 'src') },
  },
  base: '/build/assistant/',
  build: {
    outDir: '../../public/build/assistant',
    emptyOutDir: true,
    rollupOptions: {
      input: resolve(import.meta.dirname, 'src/main.tsx'),
      output: {
        entryFileNames: 'chat.js',
        chunkFileNames: 'chat-[name].js',
        assetFileNames: 'chat[extname]',
      },
    },
  },
  server: {
    port: 5174,
    host: true,
    proxy: {
      '/api': {
        target: 'http://localhost:8001',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, ''),
      },
    },
  },
})
