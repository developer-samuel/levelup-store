import { API_BASE_URL, API_KEY } from '@/app/api.config'

async function _readSseStream(body: ReadableStream<Uint8Array>, onChunk: (data: string) => void): Promise<void> {
  const reader = body.getReader()
  const decoder = new TextDecoder()

  while (true) {
    const { done, value } = await reader.read()
    if (done) break
    const text = decoder.decode(value, { stream: true })
    for (const line of text.split('\n')) {
      if (line.startsWith('data: ')) onChunk(line.slice(6))
    }
  }
}

export async function streamChat(
  message: string,
  conversationId: string,
  requestId: string,
  onChunk: (data: string) => void,
  signal: AbortSignal,
): Promise<void> {
  const res = await fetch(`${API_BASE_URL}/chat`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', ...(API_KEY && { 'X-Api-Key': API_KEY }) },
    body: JSON.stringify({ message, conversation_id: conversationId, request_id: requestId }),
    signal,
  })

  if (!res.ok || !res.body) {
    if (res.status === 429) {
      const body = (await res.json().catch(() => ({}))) as { message?: string }
      throw new Error(body.message ?? 'Too many requests. Please try again later.')
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
