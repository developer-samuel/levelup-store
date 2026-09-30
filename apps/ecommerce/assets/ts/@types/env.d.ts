/// <reference types="vite/client" />

declare global {
  type ImportMetaEnv = {
    readonly APP_ENV: 'dev' | 'prod'
    readonly API_URL: string
  }

  type ImportMeta = {
    readonly env: ImportMetaEnv
  }
}
