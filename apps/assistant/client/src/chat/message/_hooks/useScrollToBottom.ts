import { useLayoutEffect, useRef } from 'react'
import type { Message } from '@/chat/chat.types'

export function useScrollToBottom(messages: Message[], scrollTrigger: number) {
  const bottomRef = useRef<HTMLDivElement>(null)
  const prevIdsRef = useRef<string>('')
  const prevStreamingRef = useRef(false)
  const prevTriggerRef = useRef(scrollTrigger)

  useLayoutEffect(() => {
    const ids = messages.map((m) => m.id).join(',')
    const isStreaming = messages.some((m) => m.streaming)
    const idsChanged = ids !== prevIdsRef.current
    const streamingFinished = prevStreamingRef.current && !isStreaming
    const triggered = scrollTrigger !== prevTriggerRef.current

    prevIdsRef.current = ids
    prevStreamingRef.current = isStreaming
    prevTriggerRef.current = scrollTrigger

    if (idsChanged || streamingFinished || triggered) {
      bottomRef.current?.scrollIntoView({ behavior: 'instant', block: 'end' })
    }
  }, [messages, scrollTrigger])

  return bottomRef
}
