/// <reference types="vite/client" />

export {}

declare global {
  interface Window {
    openZoomModal: (src: string) => void
  }

  type ImportMetaEnv = {
    readonly APP_ENV: 'dev' | 'prod'
    readonly API_URL: string
  }

  type ImportMeta = {
    readonly env: ImportMetaEnv
  }
}
