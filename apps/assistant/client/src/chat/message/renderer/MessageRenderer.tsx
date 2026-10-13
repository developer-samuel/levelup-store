import s from '@/chat/message/renderer/MessageRenderer.module.css'
import { renderMarkdown } from '@/chat/message/renderer/RenderMarkdown'

type Props = {
  content: string
  streaming?: boolean
  thinking?: boolean
  onImageClick: (url: string) => void
}

export function MessageRenderer({ content, streaming, thinking, onImageClick }: Props) {
  if (thinking && !content) {
    return (
      <span className={s.dots}>
        <span className={s.dot} />
        <span className={s.dot2} />
        <span className={s.dot3} />
      </span>
    )
  }

  return (
    <>
      {renderMarkdown(content, onImageClick)}
      {streaming && content && <span className={s.cursor} />}
    </>
  )
}
