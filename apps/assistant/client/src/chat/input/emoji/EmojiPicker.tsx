import { lazy, Suspense } from 'react'
import { createPortal } from 'react-dom'
import data from '@emoji-mart/data'

import { useEmojiPickerPosition } from '@/chat/input/emoji/useEmojiPickerPosition'
import s from '@/chat/input/emoji/EmojiPicker.module.css'

const Picker = lazy(() => import('@emoji-mart/react'))

type Props = {
  onSelect: (emoji: string) => void
  anchorRef: React.RefObject<HTMLDivElement | null>
}

export function EmojiPicker({ onSelect, anchorRef }: Props) {
  const { pos, pickerRef } = useEmojiPickerPosition(anchorRef)

  return createPortal(
    <div
      ref={pickerRef}
      data-emoji-picker
      className={s.wrapper}
      style={pos ? { bottom: pos.bottom, right: pos.right } : undefined}
    >
      <Suspense fallback={null}>
        <Picker
          data={data}
          onEmojiSelect={(e: { native: string }) => onSelect(e.native)}
          categories={['people', 'nature', 'foods', 'activity', 'places', 'objects', 'symbols', 'flags']}
          previewPosition="none"
          skinTonePosition="none"
          theme="light"
        />
      </Suspense>
    </div>,
    document.body,
  )
}
