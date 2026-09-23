import { useCallback, useEffect, useRef, useState } from 'react'

import type { Message } from '@/chat/chat.types'
import { clearMessages, loadMessages, purgeExpired, saveMessages } from '@/chat/chat.storage'

type UseChatMessagesOptions = {
  conversationId: string
  sessionLoaded: boolean
}

type UseChatMessagesReturn = {
  messages: Message[]
  setMessages: React.Dispatch<React.SetStateAction<Message[]>>
  persist: (msgs: Message[]) => void
  clear: () => void
}

export function useChatMessages({ conversationId, sessionLoaded }: UseChatMessagesOptions): UseChatMessagesReturn {
  const [messages, setMessages] = useState<Message[]>([])
  const conversationIdRef = useRef(conversationId)

  useEffect(() => {
    conversationIdRef.current = conversationId
  }, [conversationId])

  useEffect(() => {
    purgeExpired()
  }, [])

  useEffect(() => {
    if (!sessionLoaded) return
    setMessages(loadMessages(conversationId))
  }, [conversationId, sessionLoaded])

  const persist = useCallback((msgs: Message[]): void => {
    saveMessages(conversationIdRef.current, msgs)
  }, [])

  const clear = useCallback((): void => {
    clearMessages(conversationIdRef.current)
    setMessages([])
  }, [])

  return { messages, setMessages, persist, clear }
}
