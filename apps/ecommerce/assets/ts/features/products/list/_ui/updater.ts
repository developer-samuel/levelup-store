import type { ProductListInstance } from '@/ts/features/products/list/types'

const LOAD_MORE_SELECTORS = '.products__card-load-more, #load-more'

function appendNewItems(oldList: HTMLElement, newList: HTMLElement): void {
  Array.from(newList.children)
    .filter((el) => !el.matches(LOAD_MORE_SELECTORS))
    .forEach((item) => oldList.appendChild(item))
}

function insertItemsBeforeNode(returnedWrapper: HTMLElement, beforeNode: Element | null, wrapper: HTMLElement): void {
  Array.from(returnedWrapper.childNodes).forEach((n) => {
    if (!(n instanceof Element)) return
    const el = n
    if (el.matches(LOAD_MORE_SELECTORS)) return

    if (beforeNode) beforeNode.before(el)
    else wrapper.appendChild(el)
  })
}

function replaceListContent(oldList: HTMLElement, newList: HTMLElement): void {
  oldList.innerHTML = newList.innerHTML
}

function replaceWrapperContent(wrapper: HTMLElement, returnedWrapper: HTMLElement): void {
  wrapper.innerHTML = returnedWrapper.innerHTML
}

function handleLoadMoreButton(wrapper: HTMLElement, returnedWrapper: HTMLElement): void {
  const newLoadMore = returnedWrapper.querySelector<HTMLElement>('.products__card-load-more')
  const oldLoadMore = wrapper.querySelector<HTMLElement>('.products__card-load-more')

  if (newLoadMore) {
    if (oldLoadMore) oldLoadMore.replaceWith(newLoadMore)
    else wrapper.appendChild(newLoadMore)
  } else if (oldLoadMore) {
    oldLoadMore.remove()
  }
}

/** Updates the product list DOM based on the requested page */
export function updateProductList(
  newList: HTMLElement | null,
  oldList: HTMLElement | null,
  returnedWrapper: HTMLElement,
  oldLoadMore: HTMLElement | null,
  requestedPage: number | undefined,
  filterInstance: ProductListInstance,
): void {
  const wrapper = filterInstance.productsWrapper

  if (requestedPage && requestedPage > 1) {
    if (newList && oldList) {
      appendNewItems(oldList, newList)
    } else {
      insertItemsBeforeNode(returnedWrapper, oldLoadMore, wrapper)
    }
  } else {
    if (newList && oldList) {
      replaceListContent(oldList, newList)
    } else {
      replaceWrapperContent(wrapper, returnedWrapper)
    }
  }

  handleLoadMoreButton(wrapper, returnedWrapper)
}
