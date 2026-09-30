import type { ProductListInstance } from '@/ts/features/products/list/types'
import { parseInitialPage, updateDataset } from '@/ts/features/products/list/_utils/pagination'
import { checkLoadMoreVisibility } from '@/ts/features/products/list/_ui/loadMore'
import { attachFilterListener } from '@/ts/features/products/list/_listeners/filterListener'
import { attachScrollListener } from '@/ts/features/products/list/_listeners/scrollListener'
import { setupMobileFilter } from '@/ts/features/products/list/_interactions/mobileFilter'

/**
 * Product filter component.
 *
 * Owns pagination state ('page', 'maxPages', 'isLoading') and orchestrates
 * all filter/sort/load-more interactions on the product list page.
 */
export default class ProductList implements ProductListInstance {
  readonly productsWrapper: HTMLElement
  page: number
  maxPages: number
  isLoading: boolean

  constructor(wrapperId: string) {
    const wrapper = document.getElementById(wrapperId)
    if (!wrapper) throw new Error(`ProductList: #${wrapperId} not found`)

    this.productsWrapper = wrapper
    this.page = parseInitialPage(wrapper, undefined, 'currentPage')
    this.maxPages = parseInitialPage(wrapper, undefined, 'totalPage')
    this.isLoading = false

    attachFilterListener(this)
    updateDataset(this.productsWrapper, this.page, this.maxPages)
    checkLoadMoreVisibility(this.page, this.maxPages, this.productsWrapper)
    attachScrollListener()
    setupMobileFilter()
  }
}
