import { Clock } from 'lucide-react'

import { useElapsedTimer } from '@/chat/status/useElapsedTimer'
import s from '@/chat/status/QueueStatus.module.css'

type Props = {
  position: number | null
}

export function QueueStatus({ position }: Props) {
  const elapsed = useElapsedTimer()

  return (
    <div className={s.wrapper}>
      <Clock className={s.icon} />
      <div className={s.text}>
        <p className={s.title}>
          Waiting in queue
          <span className={s.timer}>{elapsed}</span>
        </p>
        <p className={s.subtitle}>
          {position !== null
            ? `Position ${position} - please wait, the assistant will be with you shortly.`
            : 'Please wait a moment.'}
        </p>
      </div>
    </div>
  )
}
