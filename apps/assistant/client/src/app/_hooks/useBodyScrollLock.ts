import { useEffect } from 'react'

const SM = 640

export function useBodyScrollLock(locked: boolean): void {
  useEffect(() => {
    const apply = () => {
      document.body.style.overflow = locked && window.innerWidth < SM ? 'hidden' : ''
    }

    apply()
    window.addEventListener('resize', apply)

    return () => {
      window.removeEventListener('resize', apply)
      document.body.style.overflow = ''
    }
  }, [locked])
}
