import { Mic, MicOff, Smile, Square } from 'lucide-react'
import { useCallback, useState } from 'react'

import { cn } from '@/utils/classes.utils'

import { useMicInput } from '@/chat/_hooks/useMicInput'
import { MAX_LENGTH } from '@/chat/input/input.constants'
import { EmojiPicker } from '@/chat/input/emoji/EmojiPicker'
import { useEmojiPicker } from '@/chat/input/emoji/useEmojiPicker'
import { useChatInput } from '@/chat/input/useChatInput'
import s from '@/chat/input/ChatInput.module.css'

type Props = {
  onSend: (message: string) => void
  onStop: () => void
  loading: boolean
  disabled: boolean
  restoredValue?: string | null
}

export function ChatInput({ onSend, onStop, loading, disabled, restoredValue }: Props) {
  const [transcript, setTranscript] = useState('')
  const handleTranscript = useCallback((text: string) => setTranscript(text), [])
  const { listening, start, stop: stopMic, supported: micSupported } = useMicInput({ onTranscript: handleTranscript })

  const {
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
  } = useChatInput({ onSend, disabled, restoredValue, transcript })
  const emojiWrapperRef = useEmojiPicker<HTMLDivElement>(() => setEmojiOpen(false))

  const handleMicClick = () => {
    if (listening) stopMic()
    else start()
  }

  const handleSubmit = () => {
    if (listening) stopMic()
    submit()
  }

  return (
    <div
      className={s.wrapper}
      onMouseDown={(e) => {
        const target = e.target as HTMLElement
        if (target !== textareaRef.current && !target.closest('textarea') && !target.closest('[data-emoji-picker]')) {
          e.preventDefault()
          if (!target.closest(`.${s.field}`)) textareaRef.current?.blur()
        }
      }}
    >
      <div ref={emojiWrapperRef} className={s.actions}>
        {emojiOpen && <EmojiPicker onSelect={insertEmoji} anchorRef={emojiWrapperRef} />}
        {micSupported &&
          (listening ? (
            <button className={s.micStopBtn} onClick={handleMicClick} type="button" aria-label="Stop recording">
              <span className={s.micStopDot} />
              Stop recording
            </button>
          ) : (
            <button
              className={cn(s.micBtn, disabled && s.micBtnDisabled)}
              onClick={handleMicClick}
              type="button"
              disabled={disabled}
              aria-label="Start recording"
              title={disabled ? 'Unavailable while thinking' : 'Start recording'}
            >
              {disabled ? <MicOff className={s.micBtnIcon} /> : <Mic className={s.micBtnIcon} />}
            </button>
          ))}
        <button
          data-emoji-toggle
          className={cn(s.emojiBtn, emojiOpen && s.emojiBtnActive, (disabled || listening) && s.emojiBtnDisabled)}
          onMouseDown={(e) => e.preventDefault()}
          onClick={() => !disabled && !listening && setEmojiOpen((v) => !v)}
          type="button"
          aria-label="Emoji"
          title="Emoji"
        >
          <Smile className={s.emojiBtnIcon} />
        </button>
      </div>

      <div className={cn(s.field, isOverLimit && s.fieldError, disabled && s.fieldDisabled, listening && s.fieldRecording)} onClick={onFieldClick}>
        <textarea
          ref={textareaRef}
          id="chat-input"
          rows={1}
          value={value}
          onChange={(e) => !listening && setValue(e.target.value)}
          onKeyDown={onKeyDown}
          onInput={onInput}
          placeholder={listening ? 'Listening…' : 'Ask me anything…'}
          disabled={disabled}
          readOnly={listening}
          className={cn(s.textarea, disabled && 'cursor-default', listening && 'cursor-not-allowed')}
        />
        <div className={s.footer}>
          <div className={s.footerLeft}>
            <span className={cn(s.counter, isOverLimit && s.counterError)}>
              {value.length}/{MAX_LENGTH}
            </span>
          </div>
          {loading ? (
            <button className={s.stopBtn} onClick={onStop} aria-label="Stop generating" title="Stop generating">
              <Square className={s.stopBtnIcon} />
            </button>
          ) : (
            <button
              onClick={handleSubmit}
              disabled={disabled || !value.trim() || isOverLimit}
              className={cn(s.sendBtn, !value.trim() && s.sendBtnHidden)}
            >
              Send
            </button>
          )}
        </div>
      </div>
      <p className={s.hint}>Enter to send · Shift+Enter for new line</p>
    </div>
  )
}
