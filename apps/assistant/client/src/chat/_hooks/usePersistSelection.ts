import { useLayoutEffect, useRef } from 'react'

// Saves the current selection range (if any) before a streaming DOM update and
// restores it after - prevents the browser from clearing text the user selected.
export function usePersistSelection(active: boolean) {
  const savedRange = useRef<Range | null>(null)

  // Runs synchronously before paint - save selection BEFORE React mutates the DOM
  useLayoutEffect(() => {
    if (!active) return
    const sel = window.getSelection()
    if (sel && sel.rangeCount > 0 && !sel.isCollapsed) {
      savedRange.current = sel.getRangeAt(0).cloneRange()
    } else {
      savedRange.current = null
    }
  })

  // Runs synchronously after paint - restore selection AFTER DOM update
  useLayoutEffect(() => {
    const range = savedRange.current

    if (!active || !range) return
    if (!document.contains(range.startContainer) || !document.contains(range.endContainer)) return

    const sel = window.getSelection()

    sel?.removeAllRanges()
    sel?.addRange(range)
  })
}
