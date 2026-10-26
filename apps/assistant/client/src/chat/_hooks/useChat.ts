import { useCallback, useRef } from 'react'

import { useChatMessages } from '@/chat/_hooks/useChatMessages'
import { useChatStream } from '@/chat/_hooks/useChatStream'
import { deleteConversation } from '@/chat/chat.service'

type UseChatOptions = {
  conversationId: string
  isAuthenticated: boolean
  sessionLoaded: boolean
  onConversationReset?: (newId: string) => void
  onModelChange?: (model: string, fallback: boolean) => void
}

export function useChat({
  conversationId: initialConversationId,
  isAuthenticated,
  sessionLoaded,
  onConversationReset,
  onModelChange,
}: UseChatOptions) {
  const conversationId = useRef<string>(initialConversationId)
  const { messages, setMessages, persist, clearAll } = useChatMessages({
    conversationId: initialConversationId,
    sessionLoaded,
  })
  const { loading, queued, queuePosition, error, setError, rateLimitRetryAfter, failedMessage, send, stop } =
    useChatStream({
      conversationId,
      messages,
      setMessages,
      persist,
      sessionLoaded,
      onModelChange,
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

  return { messages, loading, queued, queuePosition, error, rateLimitRetryAfter, failedMessage, send, reset, stop }
}
