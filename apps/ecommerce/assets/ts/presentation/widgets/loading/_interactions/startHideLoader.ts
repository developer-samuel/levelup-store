import type { LoadingInstance } from '@/ts/presentation/widgets/loading/types'

export function startHideLoader(instance: LoadingInstance): void {
  instance.cancelHide?.()

  if (!instance.element) return

  const element = instance.element

  const timeoutId = setTimeout(() => {
    element.classList.add(instance.hiddenClass)
  }, instance.delay)

  instance.cancelHide = (): void => clearTimeout(timeoutId)
}
