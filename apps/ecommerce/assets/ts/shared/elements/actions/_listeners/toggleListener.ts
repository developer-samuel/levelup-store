import debounce from '@/ts/shared/utils/debounce'
import { queryAll } from '@/ts/shared/utils/dom/query'

/** Attaches a debounced click listener to all elements matching 'selector' */
export function attachToggleListener(
  selector: string,
  getData: (el: HTMLElement) => { id: string | undefined; [key: string]: unknown },
  handler: (event: MouseEvent, el: HTMLElement, id: string) => void | Promise<void>,
  debounceMs = 200,
  syncFn?: (el: HTMLElement) => void,
): void {
  const elements = queryAll<HTMLElement>(selector)

  elements.forEach((el) => {
    const { id } = getData(el)
    if (!id) return

    syncFn?.(el)

    const debouncedClick = debounce((event: MouseEvent) => void handler(event, el, id), debounceMs)

    el.addEventListener('click', debouncedClick)
  })
}
