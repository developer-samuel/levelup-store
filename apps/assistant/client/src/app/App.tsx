import { useState } from 'react'
import { MessageSquare } from 'lucide-react'

import { cn } from '@/utils/classes.utils'

import s from '@/app/App.module.css'

import { useBodyScrollLock } from '@/app/_hooks/useBodyScrollLock'
import { useConversationSession } from '@/chat/_hooks/useConversationSession'
import { useHealthStatus } from '@/chat/_hooks/useHealthStatus'
import { useTriggerEntry } from '@/app/_hooks/useTriggerEntry'
import { ChatPanel } from '@/chat/ChatPanel'

export default function App() {
  const [open, setOpen] = useState(false)
  const visible = useTriggerEntry()
  const { conversationId, isAuthenticated, sessionLoaded, onConversationReset } = useConversationSession()
  const healthStatus = useHealthStatus()

  useBodyScrollLock(open)

  return (
    <>
      <button
        className={cn(s.trigger, visible ? s.triggerVisible : s.triggerHidden)}
        onClick={() => setOpen(true)}
        aria-label="Open chat"
      >
        <MessageSquare className={s.triggerIcon} />
        <span className={s.statusDot} data-status={healthStatus} />
      </button>

      <ChatPanel
        healthStatus={healthStatus}
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
