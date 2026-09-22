import { StrictMode } from 'react'
import { createRoot } from 'react-dom/client'

import './app/theme/index.css'

import App from '@/app/App'

createRoot(document.getElementById('levelup-chat')!).render(
  <StrictMode>
    <App />
  </StrictMode>,
)
