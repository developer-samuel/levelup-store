import { useCallback, useState } from 'react'

type ConversationSession = {
  conversationId: string
  isAuthenticated: boolean
  sessionLoaded: boolean
  onConversationReset: (newId: string) => void
}

const GUEST_ID_KEY = 'levelup_chat_id'

function getOrCreateGuestId(): string {
  const existing = localStorage.getItem(GUEST_ID_KEY)
  if (existing) return existing
  const id = crypto.randomUUID()
  localStorage.setItem(GUEST_ID_KEY, id)
  return id
}

function resolveSession(): { conversationId: string; isAuthenticated: boolean } {
  const body = document.body
  const isAuthenticated = body.dataset.authenticated === 'true'
  const userId = body.dataset.userId ?? ''

  if (isAuthenticated && userId !== '') {
    return { conversationId: `user-${userId}`, isAuthenticated: true }
  }

  return { conversationId: getOrCreateGuestId(), isAuthenticated: false }
}

export function useConversationSession(): ConversationSession {
  const [{ conversationId, isAuthenticated }] = useState(resolveSession)

  const onConversationReset = useCallback((newId: string) => {
    localStorage.setItem(GUEST_ID_KEY, newId)
  }, [])

  return { conversationId, isAuthenticated, sessionLoaded: true, onConversationReset }
}
