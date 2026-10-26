import { createPortal } from 'react-dom'
import { X } from 'lucide-react'
import { useEscapeKey } from '@/chat/_hooks/useEscapeKey'
import s from '@/chat/message/lightbox/ImageLightbox.module.css'

type Props = {
  src: string
  onClose: () => void
}

export function ImageLightbox({ src, onClose }: Props) {
  useEscapeKey(true, onClose)

  return createPortal(
    <div className={s.overlay} onClick={onClose}>
      <button className={s.close} onClick={onClose} aria-label="Close">
        <X size={20} />
      </button>
      <img className={s.image} src={src} alt="" onClick={(e) => e.stopPropagation()} />
    </div>,
    document.body,
  )
}
