export function hasAnyText(val: unknown): boolean {
  return (typeof val === 'string' ? val : '').length > 0
}
