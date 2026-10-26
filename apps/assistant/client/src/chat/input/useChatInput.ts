import { type KeyboardEvent, type MouseEvent, useCallback, useEffect, useRef, useState } from 'react'

import { MAX_LENGTH } from '@/chat/input/input.constants'

type Options = {
  onSend: (message: string) => void
  disabled: boolean
  restoredValue?: string | null
  transcript?: string
}

function resizeTextarea(el: HTMLTextAreaElement) {
  el.style.height = 'auto'
  el.style.height = `${Math.min(el.scrollHeight, 160)}px`
}

export function useChatInput({ onSend, disabled, restoredValue, transcript }: Options) {
  const [value, setValue] = useState('')
  const [emojiOpen, setEmojiOpen] = useState(false)
  const textareaRef = useRef<HTMLTextAreaElement>(null)
  const isOverLimit = value.length > MAX_LENGTH

  useEffect(() => {
    if (!restoredValue) return

    setValue(restoredValue)
    textareaRef.current?.focus()
  }, [restoredValue])

  useEffect(() => {
    if (!transcript) return

    setValue(transcript)
    requestAnimationFrame(() => {
      if (textareaRef.current) resizeTextarea(textareaRef.current)
    })
  }, [transcript])

  const insertEmoji = (emoji: string) => {
    const el = textareaRef.current

    if (!el) {
      setValue((v) => v + emoji)
      return
    }

    const start = el.selectionStart ?? value.length
    const end = el.selectionEnd ?? value.length

    setValue(value.slice(0, start) + emoji + value.slice(end))

    requestAnimationFrame(() => {
      el.focus()
      el.setSelectionRange(start + emoji.length, start + emoji.length)
      resizeTextarea(el)
    })
  }

  const submit = () => {
    const text = value.trim()
    if (!text || disabled || isOverLimit) return

    onSend(text)
    setValue('')

    if (textareaRef.current) {
      textareaRef.current.style.height = 'auto'
    }
  }

  const onKeyDown = (e: KeyboardEvent<HTMLTextAreaElement>) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault()
      submit()
    }
  }

  const onFieldClick = (e: MouseEvent) => {
    if ((e.target as HTMLElement).closest('button')) return

    textareaRef.current?.focus()
  }

  const onInput = useCallback(() => {
    if (textareaRef.current) resizeTextarea(textareaRef.current)
  }, [])

  return {
    value,
    setValue,
    emojiOpen,
    setEmojiOpen,
    isOverLimit,
    textareaRef,
    insertEmoji,
    submit,
    onKeyDown,
    onFieldClick,
    onInput,
  }
}
