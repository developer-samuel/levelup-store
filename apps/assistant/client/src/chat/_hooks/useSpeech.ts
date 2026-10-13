import { useCallback, useEffect, useRef, useState } from 'react'

function detectLang(text: string): string {
  const s = text.slice(0, 300)

  if (/[一-鿿㐀-䶿]/.test(s)) return 'zh'
  if (/[぀-ゟ゠-ヿ]/.test(s)) return 'ja'
  if (/[가-힯ᄀ-ᇿ]/.test(s)) return 'ko'
  if (/[؀-ۿݐ-ݿ]/.test(s)) return 'ar'
  if (/[ऀ-ॿ]/.test(s)) return 'hi'
  if (/[฀-๿]/.test(s)) return 'th'
  if (/[֐-׿]/.test(s)) return 'he'
  if (/[Ѐ-ӿ]/.test(s)) return 'ru'
  if (/[áäčďéíľĺňóôŕšťúýžÁÄČĎÉÍĽĹŇÓÔŔŠŤÚÝŽ]/.test(s)) return 'sk'
  if (/[àâçèéêëîïôùûüÀÂÇÈÉÊËÎÏÔÙÛÜ]/.test(s)) return 'fr'
  if (/[äöüßÄÖÜ]/.test(s)) return 'de'
  if (/[áéíóúüñÁÉÍÓÚÜÑ]/.test(s)) return 'es'
  if (/[àèéìòùÀÈÉÌÒÙ]/.test(s)) return 'it'
  if (/[ąćęłńóśźżĄĆĘŁŃÓŚŹŻ]/.test(s)) return 'pl'

  return 'en'
}

function stripMarkdown(text: string): string {
  return text
    .replace(/```[\s\S]*?```/g, '')
    .replace(/`[^`]*`/g, '')
    .replace(/#{1,6}\s+/g, '')
    .replace(/\*\*([^*]+)\*\*/g, '$1')
    .replace(/\*([^*]+)\*/g, '$1')
    .replace(/~~([^~]+)~~/g, '$1')
    .replace(/\[([^\]]+)\]\([^)]+\)/g, '$1')
    .replace(/<[^>]+>/g, '')
    .replace(/^\|[-| :]+\|$/gm, '')
    .replace(/^\|.+\|$/gm, (row) => row.split('|').map((c) => c.trim()).filter(Boolean).join(', '))
    .replace(/^[-*+]\s+/gm, '')
    .replace(/^\d+\.\s+/gm, '')
    .replace(/^>\s+/gm, '')
    .replace(/\n{2,}/g, '. ')
    .trim()
}

let _voices: SpeechSynthesisVoice[] = []

if (typeof window !== 'undefined') {
  _voices = window.speechSynthesis?.getVoices() ?? []
  window.speechSynthesis?.addEventListener('voiceschanged', () => {
    _voices = window.speechSynthesis.getVoices()
  })
}

function hasVoiceForLang(lang: string): boolean {
  return _voices.some((v) => v.lang.startsWith(lang))
}

export function useSpeech() {
  const [speakingId, setSpeakingId] = useState<string | null>(null)
  const keepAliveRef = useRef<ReturnType<typeof setInterval> | null>(null)
  const sessionRef = useRef(0)

  const clearKeepAlive = useCallback(() => {
    if (keepAliveRef.current) {
      clearInterval(keepAliveRef.current)

      keepAliveRef.current = null
    }
  }, [])

  const startKeepAlive = useCallback(() => {
    clearKeepAlive()

    keepAliveRef.current = setInterval(() => {
      if (!window.speechSynthesis.speaking) return

      window.speechSynthesis.pause()
      window.speechSynthesis.resume()
    }, 10_000)
  }, [clearKeepAlive])

  const speak = useCallback((id: string, text: string) => {
    if (!window.speechSynthesis) return

    const session = ++sessionRef.current

    window.speechSynthesis.cancel()
    clearKeepAlive()

    const detected = detectLang(text)
    const lang = detected !== 'en' && !hasVoiceForLang(detected) ? 'en' : detected
    const sentences = stripMarkdown(text)
      .split(/\n+/)
      .map((s) => s.trim())
      .filter(Boolean)

    if (!sentences.length) return

    setSpeakingId(id)
    startKeepAlive()

    setTimeout(() => {
      let current = 0

      function speakNext() {
        if (sessionRef.current !== session) return

        if (current >= sentences.length) {
          setSpeakingId(null)
          clearKeepAlive()

          return
        }

        const utterance = new SpeechSynthesisUtterance(sentences[current++])

        utterance.lang = lang

        let done = false
        let pollTimer: ReturnType<typeof setInterval> | null = null

        const finish = () => {
          if (done) return
          done = true
          if (pollTimer) clearInterval(pollTimer)
          speakNext()
        }

        utterance.onend = finish
        utterance.onerror = finish

        window.speechSynthesis.speak(utterance)
        
        let started = false

        pollTimer = setInterval(() => {
          if (window.speechSynthesis.speaking) { started = true; return }
          if (started && !window.speechSynthesis.pending) finish()
        }, 250)
      }

      speakNext()
    }, 100)
  }, [startKeepAlive, clearKeepAlive])

  const stop = useCallback(() => {
    sessionRef.current++
    window.speechSynthesis?.cancel()
    setSpeakingId(null)
    clearKeepAlive()
  }, [clearKeepAlive])

  useEffect(() => () => {
    window.speechSynthesis?.cancel()
    clearKeepAlive()
  }, [clearKeepAlive])

  return { speakingId, speak, stop }
}
