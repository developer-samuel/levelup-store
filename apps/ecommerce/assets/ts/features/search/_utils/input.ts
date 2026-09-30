import type { SearchInstance } from '@/ts/features/search/types'
import { hasAnyText } from '@/ts/features/search/_utils/text'

export function hasInputValue(instance: SearchInstance): boolean {
  return instance.inputs.some((input) => hasAnyText(input.value))
}

export function syncInputValues(inputs: HTMLInputElement[], value: string): void {
  inputs.forEach((input) => {
    if (input.value !== value) {
      input.value = value
    }
  })
}
