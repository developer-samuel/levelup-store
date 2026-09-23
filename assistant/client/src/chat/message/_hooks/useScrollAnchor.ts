import { useCallback, useEffect, useRef } from 'react'

export function useScrollAnchor(
  containerRef: React.RefObject<HTMLDivElement | null>,
  visibleCount: number,
  hasMore: boolean,
  loadMore: () => void,
) {
  const sentinelRef = useRef<HTMLDivElement>(null)
  const prevScrollHeightRef = useRef(0)
  const isPrependingRef = useRef(false)

  const handleLoadMore = useCallback(() => {
    if (!containerRef.current) return
    prevScrollHeightRef.current = containerRef.current.scrollHeight
    isPrependingRef.current = true
    loadMore()
  }, [containerRef, loadMore])

  useEffect(() => {
    if (!isPrependingRef.current || !containerRef.current) return
    isPrependingRef.current = false
    containerRef.current.scrollTop += containerRef.current.scrollHeight - prevScrollHeightRef.current
  }, [visibleCount, containerRef])

  useEffect(() => {
    const sentinel = sentinelRef.current
    const container = containerRef.current
    if (!sentinel || !container || !hasMore) return

    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0]?.isIntersecting) handleLoadMore()
      },
      { root: container, threshold: 0 },
    )

    observer.observe(sentinel)
    return () => observer.disconnect()
  }, [hasMore, handleLoadMore, containerRef])

  return sentinelRef
}
