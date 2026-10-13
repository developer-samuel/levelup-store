import { useEffect, useState } from 'react'

export function useCountdown(value: number | null): number | null {
  const [countdown, setCountdown] = useState<number | null>(null)

  useEffect(() => {
    if (value === null) {
      setCountdown(null)
      return
    }

    setCountdown(value)

    const id = setInterval(() => {
      setCountdown((prev) => {
        if (prev === null || prev <= 1) {
          clearInterval(id)
          return null
        }
        return prev - 1
      })
    }, 1000)

    return () => clearInterval(id)
  }, [value])

  return countdown
}
