import { useCallback, useEffect, useRef } from 'react'
import SpeechRecognition, { useSpeechRecognition } from 'react-speech-recognition'

type Options = {
  onTranscript: (text: string) => void
}

function beep(freq: number, duration: number) {
  try {
    const ctx = new AudioContext()
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()

    osc.connect(gain)
    gain.connect(ctx.destination)

    osc.frequency.value = freq
    osc.type = 'sine'

    gain.gain.setValueAtTime(0.15, ctx.currentTime)
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration)

    osc.start(ctx.currentTime)
    osc.stop(ctx.currentTime + duration)
    osc.onended = () => ctx.close()
  } catch {}
}

const SILENCE_TIMEOUT_MS = 15_000

export function useMicInput({ onTranscript }: Options) {
  const { transcript, listening, resetTranscript, browserSupportsSpeechRecognition } = useSpeechRecognition()
  const silenceTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null)

  useEffect(() => {
    if (transcript) onTranscript(transcript)
  }, [transcript, onTranscript])

  useEffect(() => {
    if (!listening) {
      if (silenceTimerRef.current) clearTimeout(silenceTimerRef.current)
      return
    }

    if (silenceTimerRef.current) clearTimeout(silenceTimerRef.current)

    silenceTimerRef.current = setTimeout(() => {
      SpeechRecognition.stopListening()
      beep(330, 0.2)
    }, SILENCE_TIMEOUT_MS)

    return () => {
      if (silenceTimerRef.current) clearTimeout(silenceTimerRef.current)
    }
  }, [transcript, listening])

  const start = useCallback(() => {
    resetTranscript()
    beep(440, 0.15)

    void SpeechRecognition.startListening({ continuous: true, language: navigator.languages[0] ?? navigator.language })
  }, [resetTranscript])

  const stop = useCallback(() => {
    SpeechRecognition.stopListening()
    beep(330, 0.2)
  }, [])

  return { listening, start, stop, supported: browserSupportsSpeechRecognition }
}
