import { makeProductListWrapper } from '@/tests/_support/fakers/features/products/list.fakers'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { updatePaginationState } from '@/ts/features/products/list/_state/pagination'

function makeInstance(page = 1, maxPages = 5): ProductListInstance & { productsWrapper: HTMLElement } {
  const productsWrapper = document.createElement('div')
  return { page, maxPages, isLoading: false, productsWrapper }
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

  it.each([
    [2, 0],
    [3, undefined],
  ] as [number, number | undefined][])(
    'should fall back to instance.page=%i when requestedPage is %s',
    (instancePage, requestedPage) => {
      const returned = makeProductListWrapper()
      const instance = makeInstance(instancePage)

      updatePaginationState(returned, requestedPage, instance)

      expect(instance.page).toBe(instancePage)
    },
  )

  it('should fall back to instance.maxPages when returnedWrapper has no totalPage', () => {
    const returned = makeProductListWrapper()
    const instance = makeInstance(1, 7)

    updatePaginationState(returned, undefined, instance)

    expect(instance.maxPages).toBe(7)
  })

  it.each([['0'], ['abc']])('should set page to 1 when parsed currentPage is %s', (currentPage) => {
    const returned = makeProductListWrapper({ currentPage })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.page).toBe(1)
  })

  it.each([['0'], ['abc']])('should set maxPages to 1 when parsed totalPage is %s', (totalPage) => {
    const returned = makeProductListWrapper({ totalPage })
    const instance = makeInstance()

    updatePaginationState(returned, undefined, instance)

    expect(instance.maxPages).toBe(1)
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
})
