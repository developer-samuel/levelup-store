import { query } from '@/ts/shared/utils/dom/query'

export function getCsrfToken(id: string): string {
  const input = query<HTMLInputElement>(`#${id}`)
  if (!input) throw new Error(`CSRF token not found for #${id}`)

  return input.value
}
