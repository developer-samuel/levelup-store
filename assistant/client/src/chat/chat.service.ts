import { API_BASE_URL } from '@/app/api.config'

export async function streamChat(
  message: string,
  conversationId: string,
  onChunk: (data: string) => void,
  signal: AbortSignal,
): Promise<void> {
  const res = await fetch(`${API_BASE_URL}/chat`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ message, conversation_id: conversationId }),
    signal,
  })

  if (!res.ok || !res.body) {
    throw new Error(`HTTP ${res.status}`)
  }

  const reader = res.body.getReader()
  const decoder = new TextDecoder()

  while (true) {
    const { done, value } = await reader.read()
    if (done) break
    const text = decoder.decode(value, { stream: true })
    for (const line of text.split('\n')) {
      if (line.startsWith('data: ')) {
        onChunk(line.slice(6))
      }
    }
  }
}

export async function deleteConversation(conversationId: string): Promise<void> {
  await fetch(`${API_BASE_URL}/chat/${conversationId}`, { method: 'DELETE' }).catch(() => {})
}
