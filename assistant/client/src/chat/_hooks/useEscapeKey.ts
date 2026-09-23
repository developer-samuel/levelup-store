import { useEffect } from 'react'

export const useEscapeKey = (enabled: boolean, onEscape: () => void) => {
  useEffect(() => {
    const onKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && enabled) onEscape()
    }
    document.addEventListener('keydown', onKeyDown)
    return () => document.removeEventListener('keydown', onKeyDown)
  }, [enabled, onEscape])
}
