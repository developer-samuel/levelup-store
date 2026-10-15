import { API_BASE_URL, API_KEY } from '@/app/api.config'
import { getCurrentUserId } from '@/auth/user'

export class RateLimitError extends Error {
  retryAfter: number | null

  constructor(retryAfter: number | null) {
    super('rate_limit')
    this.name = 'RateLimitError'
    this.retryAfter = retryAfter
  }
}

async function _readSseStream(body: ReadableStream<Uint8Array>, onChunk: (data: string) => void): Promise<void> {
  const reader = body.getReader()
  const decoder = new TextDecoder()
  let buffer = ''

  while (true) {
    const { done, value } = await reader.read()
    if (done) break

    buffer += decoder.decode(value, { stream: true })

    const lines = buffer.split('\n')

    buffer = lines.pop() ?? ''

    for (const line of lines) {
      if (line.startsWith('data: ')) onChunk(line.slice(6))
    }
  }

  for (const line of buffer.split('\n')) {
    if (line.startsWith('data: ')) onChunk(line.slice(6))
  }
}

export async function streamChat(
  message: string,
  conversationId: string,
  requestId: string,
  onChunk: (data: string) => void,
  signal: AbortSignal,
): Promise<void> {
  const userId = getCurrentUserId()

  const res = await fetch(`${API_BASE_URL}/chat`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', ...(API_KEY && { 'X-Api-Key': API_KEY }) },
    body: JSON.stringify({ message, conversation_id: conversationId, request_id: requestId, ...(userId && { user_id: userId }) }),
    signal,
  })

  if (!res.ok || !res.body) {
    if (res.status === 429) {
      const body = (await res.json().catch(() => ({}))) as { retry_after?: number }
      throw new RateLimitError(body.retry_after ?? null)
    }

    if (res.status === 422) {
      const body = (await res.json().catch(() => ({}))) as { detail?: Array<{ msg?: string }> }
      const msg = body.detail?.[0]?.msg?.replace('Value error, ', '') ?? "I'm unable to process that message. Please contact our support."
      throw new Error(msg)
    }

    throw new Error(`HTTP ${res.status}`)
  }

  await _readSseStream(res.body, onChunk)
}

export async function reattachStream(
  conversationId: string,
  requestId: string,
  onChunk: (data: string) => void,
  signal: AbortSignal,
): Promise<void> {
  const res = await fetch(`${API_BASE_URL}/chat/reattach/${conversationId}/${requestId}`, {
    headers: { ...(API_KEY && { 'X-Api-Key': API_KEY }) },
    signal,
  })

  if (!res.ok || !res.body) throw new Error(`HTTP ${res.status}`)

  await _readSseStream(res.body, onChunk)
}

export async function cancelChatRequest(requestId: string): Promise<void> {
  const response = await fetch(`${API_BASE_URL}/chat/requests/${requestId}/cancel`, {
    method: 'POST',
    headers: { ...(API_KEY && { 'X-Api-Key': API_KEY }) },
  })
  
  if (!response.ok) {
    throw new Error(`HTTP ${response.status}`)
  }
}

export async function deleteConversation(conversationId: string): Promise<void> {
  await fetch(`${API_BASE_URL}/chat/${conversationId}`, {
    method: 'DELETE',
    headers: { ...(API_KEY && { 'X-Api-Key': API_KEY }) },
  }).catch(() => {})
}
