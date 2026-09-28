import { useRef, useState } from 'react'

type Condition = string | null | boolean | undefined

export function useScrollTrigger(...conditions: Condition[]): number {
  const [trigger, setTrigger] = useState(0)
  const prevRef = useRef<Condition[]>([])

  const becameTruthy = conditions.some((c, i) => Boolean(c) && !Boolean(prevRef.current[i]))
  prevRef.current = [...conditions]

  if (becameTruthy) setTrigger((n) => n + 1)

  return trigger
}
