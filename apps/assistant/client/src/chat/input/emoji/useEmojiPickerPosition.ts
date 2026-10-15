import { useLayoutEffect, useRef, useState } from 'react'

type Pos = { bottom: number; right: number }

export function useEmojiPickerPosition(anchorRef: React.RefObject<HTMLDivElement | null>) {
  const [pos, setPos] = useState<Pos | null>(null)
  const pickerRef = useRef<HTMLDivElement>(null)

  useLayoutEffect(() => {
    if (!anchorRef.current) return

    const rect = anchorRef.current.getBoundingClientRect()

    setPos({
      bottom: window.innerHeight - rect.top + 8,
      right: window.innerWidth - rect.right,
    })
  }, [anchorRef])

  return { pos, pickerRef }
}
