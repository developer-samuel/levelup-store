import type { SearchInstance } from '@/ts/features/search/types'
import { isVisible, hide } from '@/ts/features/search/_ui/visibility'

type OutsideClickOptions = {
  instance: SearchInstance
  inputs: HTMLInputElement[]
  event: MouseEvent
}

export function handleClickOutsidePanel({ instance, inputs, event }: OutsideClickOptions): void {
  if (!isVisible(instance.panel)) return

  const target = event.target instanceof Node ? event.target : null

  const clickedInsideInput = inputs.some((input) => input.contains(target))
  const clickedSearchButton = instance.searchButton?.contains(target) ?? false
  const clickedMobileSearchButton = instance.mobileSearchButton?.contains(target) ?? false

  if (!clickedInsideInput && !clickedSearchButton && !clickedMobileSearchButton && !instance.panel.contains(target)) {
    hide(instance.panel)
  }
}
