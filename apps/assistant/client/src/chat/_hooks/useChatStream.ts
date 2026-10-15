import { useCallback, useEffect, useRef, useState } from 'react'
import { flushSync } from 'react-dom'

import { API_BASE_URL, API_KEY } from '@/app/api.config'
import type { ServerChunk, Message } from '@/chat/chat.types'
import { useStreamRestore } from '@/chat/_hooks/useStreamRestore'
import { cancelChatRequest, RateLimitError, streamChat } from '@/chat/chat.service'

type UseChatStreamOptions = {
  conversationId: React.MutableRefObject<string>
  messages: Message[]
  setMessages: React.Dispatch<React.SetStateAction<Message[]>>
  persist: (msgs: Message[]) => void
  sessionLoaded: boolean
  onModelChange?: (model: string, fallback: boolean) => void
}

export function useChatStream({ conversationId, messages, setMessages, persist, sessionLoaded, onModelChange }: UseChatStreamOptions) {
  const [loading, setLoading] = useState(false)
  const [queued, setQueued] = useState(false)
  const [queuePosition, setQueuePosition] = useState<number | null>(null)
  const [error, setError] = useState<string | null>(null)
  const [rateLimitRetryAfter, setRateLimitRetryAfter] = useState<number | null>(null)
  const [failedMessage, setFailedMessage] = useState<string | null>(null)

  const abortRef = useRef<AbortController | null>(null)
  const requestIdRef = useRef<string | null>(null)
  const thinkingStartedRef = useRef(false)
  const loadingRef = useRef(false)
  const activeStreamRef = useRef(false)
  const abortedRef = useRef(false)
  const timerRef = useRef<ReturnType<typeof setInterval> | null>(null)
  const queuedRef = useRef(false)
  const doneReceivedRef = useRef(false)

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
    (msgId: string, chunk: ServerChunk) => {
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

  const restoredRef = useStreamRestore({
    conversationId,
    messages,
    setMessages,
    persist,
    sessionLoaded,
    setLoading,
    setError,
    loadingRef,
    activeStreamRef,
    requestIdRef,
    abortRef,
    timerRef,
    clearTimer,
    handleToken,
    handleDone,
  })

  const send = useCallback(
    async (text: string) => {
      if (loadingRef.current) return

      let errorHandled = false

      restoredRef.current = true
      setError(null)
      setRateLimitRetryAfter(null)
      setFailedMessage(null)
      doneReceivedRef.current = false
      loadingRef.current = true
      activeStreamRef.current = true
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
            let chunk: ServerChunk
            try {
              chunk = JSON.parse(raw) as ServerChunk
            } catch {
              return
            }

            if (!chunk.success) {
              clearTimer()
              doneReceivedRef.current = true
              setError('Something went wrong. Please try again.')
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

            if (chunk.data?.model) {
              onModelChange?.(chunk.data.model, chunk.data.fallback ?? false)
              return
            }

            if (chunk.data?.token) handleToken(assistantId, chunk)
              
            if (chunk.data?.done) {
              doneReceivedRef.current = true
              handleDone(assistantId, chunk.data.cancelled)
            }
          },
          abortRef.current.signal,
        )
      } catch (err) {
        if ((err as Error).name === 'AbortError') {
          abortedRef.current = true
        } else {
          clearTimer()
          errorHandled = true

          if (err instanceof RateLimitError) {
            setRateLimitRetryAfter(err.retryAfter)
          } else {
            setError('Something went wrong. Please try again.')
          }

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
        if (!abortedRef.current) {
          const disconnected = !doneReceivedRef.current

          setMessages((prev) => {
            const updated = prev.map((m) =>
              (m.streaming || m.thinking) && !m.content
                ? { ...m, streaming: false, thinking: false }
                : { ...m, streaming: false },
            )
            persist(updated)
            return updated
          })

          if (disconnected && !errorHandled) {
            setError('Connection lost. Please try again.')
            setFailedMessage(text)
          }
        }

        abortedRef.current = false
        loadingRef.current = false
        activeStreamRef.current = false
        setLoading(false)
      }
    },
    [conversationId, persist, setMessages, handleToken, handleDone, onModelChange, restoredRef],
  )

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

  return { loading, queued, queuePosition, error, setError, rateLimitRetryAfter, failedMessage, send, stop }
}
