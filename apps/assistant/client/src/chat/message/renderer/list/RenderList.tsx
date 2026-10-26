import { type ReactNode } from 'react'

import { inlineParse } from '@/chat/message/renderer/InlineParse'

import s from '@/chat/message/renderer/list/RenderList.module.css'

type OnImageClick = (url: string) => void

const EMOJI_RE = /^\p{Emoji_Presentation}/u

function _liClass(item: string): string {
  return EMOJI_RE.test(item.trimStart()) ? (s.emojiItem ?? '') : ''
}

export function renderList(lines: string[], i: number, onImageClick?: OnImageClick): [ReactNode, number] {
  const items: string[] = []

  while (i < lines.length) {
    const cur = lines[i] ?? ''
    const next = lines[i + 1] ?? ''

    if (/^[-*] /.test(cur)) {
      items.push(cur.replace(/^[-*] /, ''))
      i++
    } else if (cur.trim() === '' && /^[-*] /.test(next)) {
      i++
    } else {
      break
    }
  }

  return [
    <ul className={s.ul}>
      {items.map((item, j) => (
        <li key={j} className={_liClass(item)}>
          {inlineParse(item, onImageClick)}
        </li>
      ))}
    </ul>,
    i,
  ]
}

export function renderOrderedList(lines: string[], i: number, onImageClick?: OnImageClick): [ReactNode, number] {
  const items: string[] = []

  while (i < lines.length) {
    const cur = lines[i] ?? ''
    const next = lines[i + 1] ?? ''

    if (/^\d+\. /.test(cur)) {
      items.push(cur.replace(/^\d+\. /, ''))
      i++
    } else if (cur.trim() === '' && /^\d+\. /.test(next)) {
      i++
    } else {
      break
    }
  }

  return [
    <ol className={s.ol}>
      {items.map((item, j) => (
        <li key={j}>{inlineParse(item, onImageClick)}</li>
      ))}
    </ol>,
    i,
  ]
}
