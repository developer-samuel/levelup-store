import { useCallback, useRef } from 'react'

import { deleteConversation } from '@/chat/chat.service'
import { useChatMessages } from '@/chat/_hooks/useChatMessages'
import { useChatStream } from '@/chat/_hooks/useChatStream'

type UseChatOptions = {
  conversationId: string
  isAuthenticated: boolean
  sessionLoaded: boolean
  onConversationReset?: (newId: string) => void
}

export function useChat({
  conversationId: initialConversationId,
  isAuthenticated,
  sessionLoaded,
  onConversationReset,
}: UseChatOptions) {
  const conversationId = useRef<string>(initialConversationId)
  const { messages, setMessages, persist, clearAll } = useChatMessages({
    conversationId: initialConversationId,
    sessionLoaded,
  })
  const { loading, queued, queuePosition, error, setError, failedMessage, send, stop } = useChatStream({
    conversationId,
    messages,
    setMessages,
    persist,
    sessionLoaded,
  })

  const reset = useCallback(async () => {
    await stop({ silent: true })
    await deleteConversation(conversationId.current)

    if (!isAuthenticated && onConversationReset) {
      const newId = crypto.randomUUID()
      onConversationReset(newId)
      conversationId.current = newId
    }

    clearAll()
    setError(null)
  }, [isAuthenticated, onConversationReset, clearAll, stop, setError])

  return { messages, loading, queued, queuePosition, error, failedMessage, send, reset, stop }
}
