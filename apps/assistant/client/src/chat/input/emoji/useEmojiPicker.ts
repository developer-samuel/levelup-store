import { useEffect, useRef } from 'react'

export function useEmojiPicker<T extends HTMLElement>(onClose: () => void) {
  const wrapperRef = useRef<T>(null)

  useEffect(() => {
    const handler = (e: MouseEvent) => {
      const target = e.target as HTMLElement

      if (target.closest('[data-emoji-picker]')) return
      if (target.closest('[data-emoji-toggle]')) return

      onClose()
    }

    document.addEventListener('mousedown', handler)

    return () => document.removeEventListener('mousedown', handler)
  }, [onClose])

  return wrapperRef
}
