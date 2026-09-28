import type { SearchInstance } from '@/ts/features/search/types'
import { handleClickOutsidePanel } from '@/ts/features/search/_handlers/outsideClickHandler'

type OutsideClickListenerOptions = {
  instance: SearchInstance
  inputs: HTMLInputElement[]
}

export function attachOutsideClickListener({ instance, inputs }: OutsideClickListenerOptions): void {
  document.addEventListener('click', (e) => handleClickOutsidePanel({ instance, inputs, event: e }))
}
