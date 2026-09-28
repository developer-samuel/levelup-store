import type { SearchInstance } from '@/ts/features/search/types'
import { handleMobileHeaderClick } from '@/ts/features/search/_handlers/mobileHeaderHandler'

export function attachMobileHeaderListener(input: HTMLInputElement, instance: SearchInstance): void {
  input.addEventListener('click', () => {
    handleMobileHeaderClick({ instance, input })
  })
}
