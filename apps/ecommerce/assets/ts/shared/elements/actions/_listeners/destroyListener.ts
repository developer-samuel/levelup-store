import { queryAll } from '@/ts/shared/utils/dom/query'

/** Attaches a destroy click listener to all elements matching the given selector */
export function attachDestroyListener(selector: string, handler: (e: Event) => void | Promise<void>): void {
  queryAll<HTMLElement>(selector).forEach((btn) => {
    btn.addEventListener('click', (e) => void handler(e))
  })
}
