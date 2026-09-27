type Role = 'user' | 'assistant'

export type Message = {
  id: string
  role: Role
  content: string
  streaming?: boolean
  thinking?: boolean
  thinkingSeconds?: number
  thinkingStartedAt?: number
  thinkingConfirmed?: boolean
  requestId?: string
  queued?: boolean
  queuePosition?: number
  createdAt?: number
}
