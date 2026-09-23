import { useCallback, useRef, useState } from 'react'
import { flushSync } from 'react-dom'

import type { Message } from '@/chat/chat.types'
import { deleteConversation, streamChat } from '@/chat/chat.service'

type ApiChunk = {
  success: boolean
  message?: string
  data?: {
    token?: string
    done?: boolean
    thinking?: boolean
    conversation_id?: string
  }
}

const MESSAGES_KEY = 'levelup_chat_messages'

function loadMessages(): Message[] {
  try {
    const saved = localStorage.getItem(MESSAGES_KEY)
    return saved ? (JSON.parse(saved) as Message[]) : []
  } catch {
    return []
  }
}

function saveMessages(msgs: Message[]): void {
  localStorage.setItem(MESSAGES_KEY, JSON.stringify(msgs.filter((m) => !m.streaming)))
}

export function useChat() {
  const [messages, setMessages] = useState<Message[]>(loadMessages)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const conversationId = useRef<string>(
    localStorage.getItem('levelup_chat_id') ?? (() => {
      const id = crypto.randomUUID()
      localStorage.setItem('levelup_chat_id', id)
      return id
    })(),
  )
  const abortRef = useRef<AbortController | null>(null)
  const loadingRef = useRef(false)
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null)

  const clearTimer = () => {
    if (timerRef.current) {
      clearInterval(timerRef.current)
      timerRef.current = null
    }
  }

  const send = useCallback(async (text: string) => {
    if (loadingRef.current) return

    setError(null)
    loadingRef.current = true
    setLoading(true)

    const userMsg: Message = { id: crypto.randomUUID(), role: 'user', content: text }
    const assistantId = crypto.randomUUID()
    const assistantMsg: Message = {
      id: assistantId,
      role: 'assistant',
      content: '',
      streaming: true,
      thinking: true,
      thinkingSeconds: 0,
    }

    setMessages((prev) => {
      const updated = [...prev, userMsg, assistantMsg]
      saveMessages(updated)
      return updated
    })

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
            clearTimer()
            setError(chunk.message ?? 'Unknown error')
            setMessages((prev) =>
              prev.map((m) =>
                m.id === assistantId ? { ...m, streaming: false, thinking: false } : m,
              ),
            )
            return
          }

          if (chunk.data?.thinking) {
            const startTime = Date.now()
            timerRef.current = setInterval(() => {
              setMessages((prev) =>
                prev.map((m) =>
                  m.id === assistantId
                    ? { ...m, thinkingSeconds: Math.floor((Date.now() - startTime) / 1000) }
                    : m,
                ),
              )
            }, 1000)
            return
          }

          if (chunk.data?.token) {
            clearTimer()
            flushSync(() => {
              setMessages((prev) =>
                prev.map((m) =>
                  m.id === assistantId
                    ? { ...m, thinking: false, content: m.content + chunk.data!.token! }
                    : m,
                ),
              )
            })
          }

          if (chunk.data?.done) {
            setMessages((prev) => {
              const updated = prev.map((m) => (m.id === assistantId ? { ...m, streaming: false } : m))
              saveMessages(updated)
              return updated
            })
          }
        },
        abortRef.current.signal,
      )
    } catch (err) {
      if ((err as Error).name !== 'AbortError') {
        clearTimer()
        setError('Connection failed. Please try again.')
        setMessages((prev) =>
          prev.map((m) =>
            m.id === assistantId ? { ...m, streaming: false, thinking: false } : m,
          ),
        )
      }
    } finally {
      clearTimer()
      setMessages((prev) => {
        const updated = prev.map((m) =>
          m.streaming ? { ...m, streaming: false, thinking: false } : m,
        )
        saveMessages(updated)
        return updated
      })
      loadingRef.current = false
      setLoading(false)
    }
  }, [])

  const reset = useCallback(async () => {
    clearTimer()
    abortRef.current?.abort()
    await deleteConversation(conversationId.current)
    const newId = crypto.randomUUID()
    localStorage.setItem('levelup_chat_id', newId)
    localStorage.removeItem(MESSAGES_KEY)
    conversationId.current = newId
    setMessages([])
    setError(null)
    setLoading(false)
  }, [])

  const stop = useCallback(() => {
    abortRef.current?.abort()
  }, [])

  return { messages, loading, error, send, reset, stop }
}
