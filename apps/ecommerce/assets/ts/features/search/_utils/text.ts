export function hasAnyText(val: unknown): boolean {
  return String(val ?? '').length > 0
}
