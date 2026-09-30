import { query, queryAll } from '@/ts/shared/utils/dom/query'

import type { SearchElements } from '@/ts/features/search/types'

export function getSearchElements(): SearchElements {
  return {
    inputs: queryAll<HTMLInputElement>('[name="search"]'),
    icons: queryAll<HTMLElement>('.header__main-search-icon'),
    closes: queryAll<HTMLElement>('.header__main-search-close'),
    userButton: query<HTMLElement>('.header__main-user'),
    searchButton: query<HTMLElement>('.header__main-search-btn'),
    mobileSearchButton: query<HTMLElement>('.navigation__mobile-btn'),
    mobileClose: query<HTMLElement>('.navigation__mobile-close'),
    mobileCloseImage: query<HTMLElement>('.navigation__mobile-close-image'),
    headerCloseImage: query<HTMLElement>('.header__main-search-close-img'),
  }
}
