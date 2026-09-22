import { useCallback, useRef, useState } from 'react'

import type { Message } from '@/chat/chat.types'
import { deleteConversation, streamChat } from '@/chat/chat.service'

type ApiChunk = {
  success: boolean
  message?: string
  data?: {
    token?: string
    done?: boolean
    conversation_id?: string
  }
}

export function useChat() {
  const [messages, setMessages] = useState<Message[]>([])
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const conversationId = useRef<string>(crypto.randomUUID())
  const abortRef = useRef<AbortController | null>(null)
  const loadingRef = useRef(false)

  const send = useCallback(async (text: string) => {
    if (loadingRef.current) return

    setError(null)
    loadingRef.current = true
    setLoading(true)

    const userMsg: Message = { id: crypto.randomUUID(), role: 'user', content: text }
    const assistantId = crypto.randomUUID()
    const assistantMsg: Message = { id: assistantId, role: 'assistant', content: '', streaming: true }

    setMessages((prev) => [...prev, userMsg, assistantMsg])

    abortRef.current = new AbortController()

    try {
      await streamChat(
        text,
        conversationId.current,
        (raw) => {
          let chunk: ApiChunk
          try {
            chunk = JSON.parse(raw) as ApiChunk
          } catch {
            return
          }

          if (!chunk.success) {
            setError(chunk.message ?? 'Unknown error')
            setMessages((prev) =>
              prev.map((m) => (m.id === assistantId ? { ...m, streaming: false } : m)),
            )
            return
          }

          if (chunk.data?.token) {
            setMessages((prev) =>
              prev.map((m) =>
                m.id === assistantId
                  ? { ...m, content: m.content + chunk.data!.token! }
                  : m,
              ),
            )
          }

          if (chunk.data?.done) {
            setMessages((prev) =>
              prev.map((m) => (m.id === assistantId ? { ...m, streaming: false } : m)),
            )
          }
        },
        abortRef.current.signal,
      )
    } catch (err) {
      if ((err as Error).name !== 'AbortError') {
        setError('Connection failed. Please try again.')
        setMessages((prev) =>
          prev.map((m) => (m.id === assistantId ? { ...m, streaming: false } : m)),
        )
      }
    } finally {
      loadingRef.current = false
      setLoading(false)
    }
  }, [])

  const reset = useCallback(async () => {
    abortRef.current?.abort()
    await deleteConversation(conversationId.current)
    conversationId.current = crypto.randomUUID()
    setMessages([])
    setError(null)
    setLoading(false)
  }, [])

  return { messages, loading, error, send, reset }
}
