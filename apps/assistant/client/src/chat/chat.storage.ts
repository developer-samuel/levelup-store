import type { Message } from '@/chat/chat.types'

const MESSAGES_PREFIX = 'levelup_chat_messages'
const TTL_MS = 24 * 60 * 60 * 1000 // 24 hours

type StoredData = {
  messages: Message[]
  expiresAt: number
}

function getKey(conversationId: string): string {
  return `${MESSAGES_PREFIX}_${conversationId}`
}

export function loadMessages(conversationId: string): Message[] {
  try {
    const raw = localStorage.getItem(getKey(conversationId))
    if (!raw) return []
    const stored = JSON.parse(raw) as StoredData
    if (Date.now() > stored.expiresAt) {
      localStorage.removeItem(getKey(conversationId))
      return []
    }
    return stored.messages
  } catch {
    return []
  }
}

// Streaming messages with partial content are included so a page reload mid-stream
// can restore what was already written and reconnect to the worker.
export function saveMessages(conversationId: string, msgs: Message[]): void {
  const data: StoredData = {
    messages: msgs.filter((m) => !m.streaming || m.thinking || m.content.length > 0),
    expiresAt: Date.now() + TTL_MS,
  }
  localStorage.setItem(getKey(conversationId), JSON.stringify(data))
}

export function clearMessages(conversationId: string): void {
  localStorage.removeItem(getKey(conversationId))
}

export function clearAllMessages(): void {
  for (let i = localStorage.length - 1; i >= 0; i--) {
    const key = localStorage.key(i)

    if (key?.startsWith(MESSAGES_PREFIX)) localStorage.removeItem(key)
  }
}

export function purgeExpired(): void {
  const now = Date.now()

  for (let i = localStorage.length - 1; i >= 0; i--) {
    const key = localStorage.key(i)

    if (!key?.startsWith(MESSAGES_PREFIX)) continue

    try {
      const raw = localStorage.getItem(key)
      if (!raw) continue

      const stored = JSON.parse(raw) as StoredData
      if (now > stored.expiresAt) localStorage.removeItem(key)
    } catch {
      localStorage.removeItem(key)
    }
  }
}
