import { type ReactNode } from 'react'

import s from '@/chat/message/renderer/status/OrderStatus.module.css'

type Status = 'pending' | 'confirmed' | 'processed' | 'shipped' | 'delivered' | 'completed' | 'refunded'
type StepVariant = 'past' | 'current' | 'future'

const FLOW: Status[] = ['pending', 'confirmed', 'processed', 'shipped', 'delivered', 'completed']

const STATUS_RE = /^Status:\s*(pending|confirmed|processed|shipped|delivered|completed|refunded)/i
const BADGE_RE = /Status:\s*(pending|confirmed|processed|shipped|delivered|completed|refunded)/i

function parseStatus(text: string, re: RegExp): Status | null {
  const m = re.exec(text)

  return m ? (m[1]!.toLowerCase() as Status) : null
}

function _stepClass(status: Status, variant: StepVariant): string {
  const base = `${s.step} ${s[status]}`

  if (variant === 'current') return `${base} ${s.stepActive}`
  if (variant === 'past')    return `${base} ${s.stepPast}`

  return `${base} ${s.stepFuture}`
}

function _timeline(current: Status): ReactNode {
  const steps: ReactNode[] = []

  if (current === 'refunded') {
    const deliveredIdx = FLOW.indexOf('delivered')

    FLOW.slice(0, deliveredIdx + 1).forEach((st, i) => {
      if (i > 0) steps.push(<span key={`a${i}`} className={s.arrow}>↓</span>)

      steps.push(<span key={st} className={_stepClass(st, 'past')}>{st}</span>)
    })

    steps.push(<span key="ar" className={s.arrow}>↓</span>)
    steps.push(<span key="refunded" className={`${s.step} ${s.refunded} ${s.stepActive}`}>refunded</span>)

    return steps
  }

  const currentIdx = FLOW.indexOf(current)

  FLOW.forEach((st, i) => {
    if (i > 0) steps.push(<span key={`a${i}`} className={s.arrow}>↓</span>)
    const variant = i < currentIdx ? 'past' : i === currentIdx ? 'current' : 'future'
    
    steps.push(<span key={st} className={_stepClass(st, variant)}>{st}</span>)
  })

  return steps
}

export function renderOrderStatusLine(line: string): ReactNode | null {
  const status = parseStatus(line, STATUS_RE)
  if (!status) return null

  return <div className={s.timeline}>{_timeline(status)}</div>
}

export function renderOrderStatusBadge(text: string): ReactNode | null {
  const status = parseStatus(text, BADGE_RE)
  if (!status) return null

  return <span className={`${s.step} ${s[status]}`}>{status}</span>
}
