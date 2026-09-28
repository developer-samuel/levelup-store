import type { TimeoutId } from '@/ts/shared/types'

export default function debounce<TArgs extends unknown[]>(
  callback: (...args: TArgs) => void | Promise<void>,
  delay: number,
): (...args: TArgs) => void {
  let timer: TimeoutId | undefined

  return function (...args: TArgs): void {
    clearTimeout(timer)
    timer = setTimeout(() => {
      void callback(...args)
    }, delay)
  }
}
