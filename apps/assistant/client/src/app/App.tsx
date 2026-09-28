import { useState } from 'react'
import { MessageSquare } from 'lucide-react'

import s from '@/app/App.module.css'

import { useConversationSession } from '@/chat/_hooks/useConversationSession'
import { ChatPanel } from '@/chat/ChatPanel'

export default function App() {
  const [open, setOpen] = useState(false)
  const { conversationId, isAuthenticated, sessionLoaded, onConversationReset } = useConversationSession()

  return (
    <>
      <button
        className={s.trigger}
        onClick={() => setOpen(true)}
        aria-label="Open chat"
      >
        <MessageSquare className={s.triggerIcon} />
      </button>

      <ChatPanel
        open={open}
        onClose={() => setOpen(false)}
        conversationId={conversationId}
        isAuthenticated={isAuthenticated}
        sessionLoaded={sessionLoaded}
        onConversationReset={onConversationReset}
      />
    </>
  )
}
