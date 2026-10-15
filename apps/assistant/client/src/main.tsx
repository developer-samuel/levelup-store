import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'

import '@/app/theme/index.css'

import App from '@/app/App'

const root = document.getElementById('levelup-store-assistant')
if (root) {
  createRoot(root).render(
    <StrictMode>
      <App />
    </StrictMode>,
  )
}
