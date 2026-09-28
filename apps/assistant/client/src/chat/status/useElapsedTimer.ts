import { useEffect, useRef, useState } from 'react'

export function useElapsedTimer(): string {
  const [seconds, setSeconds] = useState(0)
  const intervalRef = useRef<ReturnType<typeof setInterval> | null>(null)

  useEffect(() => {
    setSeconds(0)
    intervalRef.current = setInterval(() => setSeconds((n) => n + 1), 1000)

    return () => {
      if (intervalRef.current) clearInterval(intervalRef.current)
    }
  }, [])

  if (seconds < 60) return `${seconds}s`

  const m = Math.floor(seconds / 60)
  const s = seconds % 60

  return s > 0 ? `${m}m ${s}s` : `${m}m`
}
