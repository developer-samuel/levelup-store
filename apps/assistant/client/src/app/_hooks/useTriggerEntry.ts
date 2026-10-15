import { useEffect, useState } from 'react'

const SOUND_KEY = 'assistant:entry_sound_played_at'
const TTL_MS = 24 * 60 * 60 * 1000

function shouldPlaySound(): boolean {
  try {
    const raw = localStorage.getItem(SOUND_KEY)
    if (!raw) return true
    return Date.now() - Number(raw) > TTL_MS
  } catch {
    return false
  }
}

function markSoundPlayed() {
  try {
    localStorage.setItem(SOUND_KEY, String(Date.now()))
  } catch {}
}

function playEntrySound() {
  if (!shouldPlaySound()) return
  try {
    const ctx = new AudioContext()
    const osc = ctx.createOscillator()
    const gain = ctx.createGain()

    osc.connect(gain)
    gain.connect(ctx.destination)

    osc.frequency.value = 600
    osc.type = 'sine'

    gain.gain.setValueAtTime(0.08, ctx.currentTime)
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.25)

    osc.start(ctx.currentTime)
    osc.stop(ctx.currentTime + 0.25)
    osc.onended = () => ctx.close()

    markSoundPlayed()
  } catch {}
}

export function useTriggerEntry() {
  const [visible, setVisible] = useState(false)

  useEffect(() => {
    const delayMs = shouldPlaySound() ? 3000 : 1000
    const t = setTimeout(() => {
      setVisible(true)
      playEntrySound()
    }, delayMs)

    return () => clearTimeout(t)
  }, [])

  return visible
}
