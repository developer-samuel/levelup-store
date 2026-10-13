/// <reference types="vite/client" />

interface Window {
  openZoomModal?: (src: string) => void
}

interface ImportMetaEnv {
  readonly VITE_API_URL: string | undefined
}

interface ImportMeta {
  readonly env: ImportMetaEnv
}
