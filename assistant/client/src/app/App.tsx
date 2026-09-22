import { useState } from 'react'
import { MessageSquare } from 'lucide-react'

import s from '@/app/App.module.css'

import { ChatPanel } from '@/chat/ChatPanel'

export default function App() {
  const [open, setOpen] = useState(false)

  return (
    <>
      <button
        className={s.trigger}
        onClick={() => setOpen(true)}
        aria-label="Open chat"
      >
        <MessageSquare className={s.triggerIcon} />
      </button>

      <ChatPanel open={open} onClose={() => setOpen(false)} />
    </>
  )
}
