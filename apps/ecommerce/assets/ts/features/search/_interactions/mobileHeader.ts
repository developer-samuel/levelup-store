import { queryAll } from '@/ts/shared/utils/dom/query'

import type { SearchInstance } from '@/ts/features/search/types'
import { attachMobileHeaderListener } from '@/ts/features/search/_listeners/mobileHeaderListener'

export function bindMobileHeader(instance: SearchInstance): void {
  const inputs = queryAll<HTMLInputElement>('.navigation__mobile-input, .header__main-search-input')

  inputs.forEach((input) => attachMobileHeaderListener(input, instance))
}
