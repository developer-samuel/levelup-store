import { useLayoutEffect, useRef } from 'react'

export function usePersistSelection(active: boolean) {
  const savedRange = useRef<Range | null>(null)

  useLayoutEffect(() => {
    if (!active) return

    const sel = window.getSelection()

    if (sel && sel.rangeCount > 0 && !sel.isCollapsed) {
      savedRange.current = sel.getRangeAt(0).cloneRange()
    } else {
      savedRange.current = null
    }
  })

  useLayoutEffect(() => {
    const range = savedRange.current

    if (!active || !range) return
    if (!document.contains(range.startContainer) || !document.contains(range.endContainer)) return

    const sel = window.getSelection()

    sel?.removeAllRanges()
    sel?.addRange(range)
  })
}
