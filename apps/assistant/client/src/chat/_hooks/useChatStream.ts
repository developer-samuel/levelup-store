import { useCallback, useEffect, useRef, useState } from 'react'
import { flushSync } from 'react-dom'

import { API_BASE_URL, API_KEY } from '@/app/api.config'
import type { Message } from '@/chat/chat.types'
import { cancelChatRequest, reattachStream, streamChat } from '@/chat/chat.service'

type ApiChunk = {
  success: boolean
  message?: string
  data?: {
    token?: string
    done?: boolean
    cancelled?: boolean
    thinking?: boolean
    queued?: boolean
    queue_position?: number
    conversation_id?: string
  }
}

type UseChatStreamOptions = {
  conversationId: React.MutableRefObject<string>
  messages: Message[]
  setMessages: React.Dispatch<React.SetStateAction<Message[]>>
  persist: (msgs: Message[]) => void
  sessionLoaded: boolean
}

export function useChatStream({ conversationId, messages, setMessages, persist, sessionLoaded }: UseChatStreamOptions) {
  const [loading, setLoading] = useState(false)
  const [queued, setQueued] = useState(false)
  const [queuePosition, setQueuePosition] = useState<number | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [failedMessage, setFailedMessage] = useState<string | null>(null)

  const abortRef = useRef<AbortController | null>(null)
  const requestIdRef = useRef<string | null>(null)
  const thinkingStartedRef = useRef(false)
  const loadingRef = useRef(false)
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null)
  const queuedRef = useRef(false)
  const restoredRef = useRef(false)

  useEffect(() => {
    queuedRef.current = queued
  }, [queued])

  const clearTimer = () => {
    if (timerRef.current) {
      clearInterval(timerRef.current)
      timerRef.current = null
    }
  }

  const handleToken = useCallback(
    (msgId: string, chunk: ApiChunk) => {
      clearTimer()

      let persisted: Message[] | null = null

      flushSync(() => {
        setMessages((prev) => {
          const updated = prev.map((m) =>
            m.id === msgId ? { ...m, thinking: false, content: m.content + chunk.data!.token! } : m,
          )

          persisted = updated

          return updated
        })
      })

      if (persisted) persist(persisted)
    },
    [persist, setMessages],
  )

  const handleDone = useCallback(
    (msgId: string, cancelled = false) => {
      setMessages((prev) => {
        const updated = cancelled
          ? prev.filter((m) => m.id !== msgId)
          : prev.map((m) => (m.id === msgId ? { ...m, streaming: false, createdAt: Date.now() } : m))
        persist(updated)

        return updated
      })
    },
    [persist, setMessages],
  )

  useEffect(() => () => clearTimer(), [])

  useEffect(() => {
    const handleUnload = () => {
      if (!queuedRef.current || !requestIdRef.current) return
      const url = `${API_BASE_URL}/chat/requests/${requestIdRef.current}/cancel`
      const data = API_KEY ? JSON.stringify({ _key: API_KEY }) : null
      if (data) {
        navigator.sendBeacon(url, new Blob([data], { type: 'application/json' }))
      } else {
        navigator.sendBeacon(url)
      }
    }
    window.addEventListener('beforeunload', handleUnload)
    return () => window.removeEventListener('beforeunload', handleUnload)
  }, [])

  useEffect(() => {
    if (restoredRef.current || !sessionLoaded || messages.length === 0) return
    restoredRef.current = true

    const pendingMsg = messages.find((m) => m.streaming)
    if (!pendingMsg) return

    if (!pendingMsg.requestId || (!pendingMsg.content && !pendingMsg.thinkingConfirmed)) {
      setMessages((prev) => {
        const updated = prev.filter((m) => m.id !== pendingMsg.id)
        persist(updated)
        return updated
      })
      return
    }

    requestIdRef.current = pendingMsg.requestId
    loadingRef.current = true
    setLoading(true)

    const msgId = pendingMsg.id
    const reqId = pendingMsg.requestId

    if (!pendingMsg.content) {
      const startTime = pendingMsg.thinkingStartedAt ?? Date.now()
      setMessages((prev) =>
        prev.map((m) =>
          m.id === msgId ? { ...m, thinking: true, thinkingSeconds: Math.floor((Date.now() - startTime) / 1000) } : m,
        ),
      )
      timerRef.current = setInterval(() => {
        setMessages((prev) =>
          prev.map((m) =>
            m.id === msgId ? { ...m, thinkingSeconds: Math.floor((Date.now() - startTime) / 1000) } : m,
          ),
        )
      }, 1000)
    }

    const reattachAbort = new AbortController()
    abortRef.current = reattachAbort

    void (async () => {
      try {
        await reattachStream(
          conversationId.current,
          reqId,
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
              setMessages((prev) => prev.map((m) => (m.id === msgId ? { ...m, streaming: false, thinking: false } : m)))
              return
            }

            if (chunk.data?.thinking) {
              setMessages((prev) => {
                const updated = prev.map((m) => (m.id === msgId ? { ...m, thinking: true, content: '' } : m))
                persist(updated)
                return updated
              })
              return
            }

            if (chunk.data?.token) handleToken(msgId, chunk)
            if (chunk.data?.done) handleDone(msgId, chunk.data.cancelled)
          },
          reattachAbort.signal,
        )
      } catch (err) {
        if ((err as Error).name !== 'AbortError') {
          setMessages((prev) => {
            const updated = prev.map((m) => (m.id === msgId ? { ...m, streaming: false, thinking: false } : m))
            persist(updated)
            return updated
          })
        }
      } finally {
        clearTimer()
        setMessages((prev) => {
          const updated = prev.map((m) => (m.streaming || m.thinking ? { ...m, streaming: false, thinking: false } : m))
          persist(updated)
          return updated
        })
        loadingRef.current = false
        setLoading(false)
        requestIdRef.current = null
      }
    })()
  }, [sessionLoaded, messages, setMessages, conversationId, persist, setError, handleToken, handleDone])

  // Send a new message and stream the assistant response
  const send = useCallback(
    async (text: string) => {
      if (loadingRef.current) return

      restoredRef.current = true
      setError(null)
      setFailedMessage(null)
      loadingRef.current = true
      setLoading(true)

      const sendStartTime = Date.now()
      const requestId = crypto.randomUUID()
      requestIdRef.current = requestId
      const userMsg: Message = { id: crypto.randomUUID(), role: 'user', content: text, createdAt: sendStartTime }
      const assistantId = crypto.randomUUID()
      const assistantMsg: Message = {
        id: assistantId,
        role: 'assistant',
        content: '',
        streaming: true,
        thinking: true,
        thinkingStartedAt: sendStartTime,
        requestId,
      }

      setMessages((prev) => {
        const cleaned = prev.filter((m) => !m.streaming)
        const updated = [...cleaned, userMsg, assistantMsg]
        persist(updated)
        return updated
      })

      timerRef.current = setInterval(() => {
        setMessages((prev) =>
          prev.map((m) =>
            m.id === assistantId ? { ...m, thinkingSeconds: Math.floor((Date.now() - sendStartTime) / 1000) } : m,
          ),
        )
      }, 1000)
      thinkingStartedRef.current = false
      abortRef.current = new AbortController()

      try {
        await streamChat(
          text,
          conversationId.current,
          requestId,
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
              setFailedMessage(text)
              setMessages((prev) =>
                prev.map((m) => (m.id === assistantId ? { ...m, streaming: false, thinking: false } : m)),
              )
              return
            }

            if (chunk.data?.queued) {
              clearTimer()
              setQueued(true)
              setQueuePosition(chunk.data.queue_position ?? null)
              setMessages((prev) =>
                prev.map((m) =>
                  m.id === assistantId
                    ? { ...m, thinking: false, thinkingSeconds: undefined, thinkingStartedAt: undefined }
                    : m,
                ),
              )
              return
            }

            if (chunk.data?.thinking) {
              setQueued(false)
              setQueuePosition(null)
              if (thinkingStartedRef.current) return
              thinkingStartedRef.current = true
              clearTimer()
              const startTime = Date.now()
              setMessages((prev) => {
                const updated = prev.map((m) =>
                  m.id === assistantId
                    ? {
                        ...m,
                        thinking: true,
                        thinkingConfirmed: true,
                        thinkingSeconds: 0,
                        thinkingStartedAt: startTime,
                      }
                    : m,
                )
                persist(updated)
                return updated
              })
              timerRef.current = setInterval(() => {
                setMessages((prev) =>
                  prev.map((m) =>
                    m.id === assistantId ? { ...m, thinkingSeconds: Math.floor((Date.now() - startTime) / 1000) } : m,
                  ),
                )
              }, 1000)
              return
            }

            if (chunk.data?.token) handleToken(assistantId, chunk)
            if (chunk.data?.done) handleDone(assistantId, chunk.data.cancelled)
          },
          abortRef.current.signal,
        )
      } catch (err) {
        if ((err as Error).name !== 'AbortError') {
          clearTimer()
          setError('Connection failed. Please try again.')
          setFailedMessage(text)
          setMessages((prev) =>
            prev.map((m) => (m.id === assistantId ? { ...m, streaming: false, thinking: false } : m)),
          )
        }
      } finally {
        clearTimer()
        setQueued(false)
        setQueuePosition(null)
        requestIdRef.current = null
        thinkingStartedRef.current = false
        setMessages((prev) => {
          const updated = prev.map((m) =>
            (m.streaming || m.thinking) && !m.content
              ? { ...m, streaming: false, thinking: false }
              : { ...m, streaming: false },
          )
          persist(updated)
          return updated
        })
        loadingRef.current = false
        setLoading(false)
      }
    },
    [conversationId, persist, setMessages, handleToken, handleDone],
  )

  // Stop the current stream and cancel the server-side request
  const stop = useCallback(
    async ({ silent = false }: { silent?: boolean } = {}) => {
      const requestId = requestIdRef.current
      abortRef.current?.abort()
      clearTimer()
      requestIdRef.current = null
      thinkingStartedRef.current = false
      loadingRef.current = false
      setLoading(false)
      setQueued(false)
      setQueuePosition(null)
      setMessages((prev) => {
        const updated = prev.map((m) =>
          m.streaming ? { ...m, streaming: false, thinking: false, requestId: undefined } : m,
        )
        persist(updated)
        return updated
      })
      if (requestId) {
        try {
          await cancelChatRequest(requestId)
        } catch {
          if (!silent) setError('Could not cancel the request on the assistant.')
        }
      }
    },
    [persist, setMessages],
  )

  return { loading, queued, queuePosition, error, setError, failedMessage, send, stop }
}
