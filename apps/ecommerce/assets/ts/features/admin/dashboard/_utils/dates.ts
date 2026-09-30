export function getCurrentMonthName(locale = 'sk-SK', timeZone = 'Europe/Bratislava'): string {
  return new Date().toLocaleString(locale, { month: 'long', timeZone })
}

export function getLastNDaysLabels(n = 7, locale = 'sk-SK', timeZone = 'Europe/Bratislava'): string[] {
  return Array.from({ length: n }, (_, i) => {
    const d = new Date()
    d.setDate(d.getDate() - (n - 1 - i))
    return d.toLocaleDateString(locale, { month: 'short', day: 'numeric', timeZone })
  })
}
