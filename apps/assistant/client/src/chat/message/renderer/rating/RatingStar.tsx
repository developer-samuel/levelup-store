import { type ReactNode } from 'react'

import s from '@/chat/message/renderer/rating/RatingStar.module.css'

type StarType = 'full' | 'half' | 'empty'

const RATING_RE = /^(\d+\.?\d*)★\s*\((\d+\s+(?:reviews?|ratings?)(?:,\s*\d+\s+(?:reviews?|ratings?))?)\)$/
const STAR_PATH = 'M 12 2 L 15.09 8.26 L 22 9.27 L 17 14.14 L 18.18 21.02 L 12 17.77 L 5.82 21.02 L 7 14.14 L 2 9.27 L 8.91 8.26 Z'
const STAR_FULL = '#f59e0b'
const STAR_DIM  = 'rgba(245,158,11,0.28)'

function StarSvg({ type }: { type: StarType }) {
  if (type === 'full') {
    return <svg width="14" height="14" viewBox="0 0 24 24"><path d={STAR_PATH} fill={STAR_FULL} /></svg>
  }

  if (type === 'half') {
    return (
      <svg width="14" height="14" viewBox="0 0 24 24">
        <defs><linearGradient id="sg" x1="0" x2="1" y1="0" y2="0"><stop offset="50%" stopColor={STAR_FULL} /><stop offset="50%" stopColor={STAR_DIM} /></linearGradient></defs>
        <path d={STAR_PATH} fill="url(#sg)" />
      </svg>
    )
  }

  return <svg width="14" height="14" viewBox="0 0 24 24"><path d={STAR_PATH} fill={STAR_DIM} /></svg>
}

export function renderRatingLine(line: string): ReactNode | null {
  const m = line.trim().match(RATING_RE)
  if (!m) return null

  const rating = parseFloat(m[1]!)
  const label = m[2]!

  const stars = Array.from({ length: 5 }, (_, i): StarType => {
    const filled = rating - i
    if (filled >= 0.75) return 'full'
    if (filled >= 0.25) return 'half'
    return 'empty'
  })

  return (
    <span className={s.ratingLine}>
      {stars.map((type, i) => <StarSvg key={i} type={type} />)}
      <span className={s.label}>({label})</span>
    </span>
  )
}
