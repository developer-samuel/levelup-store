import { makeProductListWrapper } from '@/tests/_support/fakers/features/products/list.fakers'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { updatePaginationState } from '@/ts/features/products/list/_state/pagination'

function makeInstance(page = 1, maxPages = 5): ProductListInstance & { productsWrapper: HTMLElement } {
  const productsWrapper = document.createElement('div')
  return { page, maxPages, isLoading: false, productsWrapper } as unknown as ProductListInstance & {
    productsWrapper: HTMLElement
  }
}

describe('updatePaginationState()', () => {
  it('should use returnedWrapper currentPage when present', () => {
    const returned = makeProductListWrapper({ currentPage: '3' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.page).toBe(3)
  })

  it('should use returnedWrapper totalPage when present', () => {
    const returned = makeProductListWrapper({ currentPage: '1', totalPage: '8' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.maxPages).toBe(8)
  })

  it('should fall back to requestedPage when returnedWrapper has no currentPage', () => {
    const returned = makeProductListWrapper()
    const instance = makeInstance(1)

    updatePaginationState(returned, 4, instance)

    expect(instance.page).toBe(4)
  })

  it('should fall back to instance.page when requestedPage is 0', () => {
    const returned = makeProductListWrapper()
    const instance = makeInstance(2)

    updatePaginationState(returned, 0, instance)

    expect(instance.page).toBe(2)
  })

  it('should fall back to instance.page when requestedPage is undefined', () => {
    const returned = makeProductListWrapper()
    const instance = makeInstance(3)

    updatePaginationState(returned, undefined, instance)

    expect(instance.page).toBe(3)
  })

  it('should fall back to instance.maxPages when returnedWrapper has no totalPage', () => {
    const returned = makeProductListWrapper()
    const instance = makeInstance(1, 7)

    updatePaginationState(returned, undefined, instance)

    expect(instance.maxPages).toBe(7)
  })

  it('should set page to 1 when parsed currentPage is 0', () => {
    const returned = makeProductListWrapper({ currentPage: '0' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.page).toBe(1)
  })

  it('should set maxPages to 1 when parsed totalPage is 0', () => {
    const returned = makeProductListWrapper({ totalPage: '0' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.maxPages).toBe(1)
  })

  it('should set page to 1 when parsed currentPage is NaN', () => {
    const returned = makeProductListWrapper({ currentPage: 'abc' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.page).toBe(1)
  })

  it('should update productsWrapper dataset currentPage', () => {
    const returned = makeProductListWrapper({ currentPage: '5' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.productsWrapper.dataset.currentPage).toBe('5')
  })

  it('should update productsWrapper dataset totalPage', () => {
    const returned = makeProductListWrapper({ totalPage: '10' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.productsWrapper.dataset.totalPage).toBe('10')
  })

  it('should set maxPages to 1 when parsed totalPage is NaN', () => {
    const returned = makeProductListWrapper({ totalPage: 'abc' })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.maxPages).toBe(1)
  })
})
