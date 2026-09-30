import { useCallback, useState } from 'react'

import type { Message } from '@/chat/chat.types'

const LOAD_MORE_SIZE = 20
const INITIAL_SIZE = Math.max(LOAD_MORE_SIZE, Math.ceil((window.innerHeight * 2) / 80))

type Result = {
  visibleMessages: Message[]
  hasMore: boolean
  loadMore: () => void
}

export function useMessagePagination(messages: Message[]): Result {
  const [displayCount, setDisplayCount] = useState(INITIAL_SIZE)

  const visibleMessages = messages.slice(-Math.min(displayCount, messages.length))
  const hasMore = messages.length > displayCount

  const loadMore = useCallback(() => {
    setDisplayCount((prev) => prev + LOAD_MORE_SIZE)
  }, [])

  return { visibleMessages, hasMore, loadMore }
}
