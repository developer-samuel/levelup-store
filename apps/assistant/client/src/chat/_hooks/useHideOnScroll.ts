import { useEffect } from 'react'

export function useHideOnScroll(onHide: () => void) {
  useEffect(() => {
    window.addEventListener('scroll', onHide, true)
    
    return () => window.removeEventListener('scroll', onHide, true)
  }, [onHide])
}
