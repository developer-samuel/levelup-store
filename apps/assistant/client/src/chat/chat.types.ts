type Role = 'user' | 'assistant'

export type ServerChunk = {
  success: boolean
  message?: string
  data?: {
    token?: string
    done?: boolean
    cancelled?: boolean
    thinking?: boolean
    queued?: boolean
    queue_position?: number
    conversation_id?: string
    model?: string
    fallback?: boolean
  }
}

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
