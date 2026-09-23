import { useEffect, useRef } from 'react'

export function useScrollToBottom(messageCount: number) {
  const bottomRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    bottomRef.current?.scrollIntoView({ behavior: 'instant' })
  }, [messageCount])

  return bottomRef
}
