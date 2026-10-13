import { useEffect, useRef } from 'react'

import { reattachStream } from '@/chat/chat.service'
import type { Message, ServerChunk } from '@/chat/chat.types'

type UseStreamRestoreOptions = {
  conversationId: React.MutableRefObject<string>
  messages: Message[]
  setMessages: React.Dispatch<React.SetStateAction<Message[]>>
  persist: (msgs: Message[]) => void
  sessionLoaded: boolean
  setLoading: (v: boolean) => void
  setError: (v: string | null) => void
  loadingRef: React.MutableRefObject<boolean>
  activeStreamRef: React.MutableRefObject<boolean>
  requestIdRef: React.MutableRefObject<string | null>
  abortRef: React.MutableRefObject<AbortController | null>
  timerRef: React.MutableRefObject<ReturnType<typeof setInterval> | null>
  clearTimer: () => void
  handleToken: (msgId: string, chunk: ServerChunk) => void
  handleDone: (msgId: string, cancelled?: boolean) => void
}

export function useStreamRestore({
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
}: UseStreamRestoreOptions) {
  const restoredRef = useRef(false)

  useEffect(() => {
    if (restoredRef.current || !sessionLoaded || messages.length === 0 || activeStreamRef.current) return
    restoredRef.current = true

    const pendingMsg = messages.find((m) => m.streaming)
    if (!pendingMsg) return

    if (!pendingMsg.requestId) {
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
            let chunk: ServerChunk

            try {
              chunk = JSON.parse(raw) as ServerChunk
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
          const updated = prev.map((m) =>
            m.streaming || m.thinking ? { ...m, streaming: false, thinking: false } : m,
          )
          persist(updated)

          return updated
        })
        
        loadingRef.current = false
        setLoading(false)
        requestIdRef.current = null
      }
    })()
  }, [sessionLoaded, messages, setMessages, conversationId, persist, setLoading, setError, loadingRef, activeStreamRef, requestIdRef, abortRef, timerRef, clearTimer, handleToken, handleDone])

  return restoredRef
}
