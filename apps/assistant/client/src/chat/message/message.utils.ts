export function formatTimestamp(ts: number): { date: string; time: string } {
  const d = new Date(ts)
  const date = d.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
  const time = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false })

  return { date, time }
}

export function formatThinkingTime(seconds: number): string {
  if (seconds < 60) return `${seconds}s`

  const m = Math.floor(seconds / 60)
  const s = seconds % 60

  return s > 0 ? `${m}m ${s}s` : `${m}m`
}

export function collectImages(text: string): string[] {
  const re = /!\[[^\]]*\]\(([^)]+)\)/g
  const urls: string[] = []

  let m: RegExpExecArray | null

  while ((m = re.exec(text)) !== null) {
    if (m[1]) urls.push(m[1])
  }

  return urls
}
