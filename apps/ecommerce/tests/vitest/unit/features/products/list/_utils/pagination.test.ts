import { makeProductListWrapper } from '@/tests/_support/fakers/features/products/list.fakers'

import { parseInitialPage, updateDataset, updateFromServer } from '@/ts/features/products/list/_utils/pagination'

describe('parseInitialPage()', () => {
  it('should return optionValue when provided as number', () => {
    const wrapper = makeProductListWrapper({ currentPage: '3' })
    expect(parseInitialPage(wrapper, 7, 'currentPage')).toBe(7)
  })

  it('should return optionValue when provided as string number', () => {
    const wrapper = makeProductListWrapper()
    expect(parseInitialPage(wrapper, '4', 'currentPage')).toBe(4)
  })

  it('should read from dataset when optionValue is undefined', () => {
    const wrapper = makeProductListWrapper({ currentPage: '5' })
    expect(parseInitialPage(wrapper, undefined, 'currentPage')).toBe(5)
  })

  it('should return 1 when dataset value is missing', () => {
    const wrapper = makeProductListWrapper()
    expect(parseInitialPage(wrapper, undefined, 'currentPage')).toBe(1)
  })

  it('should return 1 when value is 0', () => {
    const wrapper = makeProductListWrapper({ currentPage: '0' })
    expect(parseInitialPage(wrapper, undefined, 'currentPage')).toBe(1)
  })

  it('should return 1 when value is negative', () => {
    const wrapper = makeProductListWrapper({ currentPage: '-3' })
    expect(parseInitialPage(wrapper, undefined, 'currentPage')).toBe(1)
  })

  it('should return 1 when value is NaN', () => {
    const wrapper = makeProductListWrapper({ currentPage: 'abc' })
    expect(parseInitialPage(wrapper, undefined, 'currentPage')).toBe(1)
  })
})

describe('updateDataset()', () => {
  it('should set currentPage on wrapper dataset', () => {
    const wrapper = makeProductListWrapper()
    updateDataset(wrapper, 3, 10)
    expect(wrapper.dataset.currentPage).toBe('3')
  })

  it('should set totalPage on wrapper dataset', () => {
    const wrapper = makeProductListWrapper()
    updateDataset(wrapper, 3, 10)
    expect(wrapper.dataset.totalPage).toBe('10')
  })
})

describe('updateFromServer()', () => {
  it('should use server currentPage when valid', () => {
    const wrapper = makeProductListWrapper({ currentPage: '1', totalPage: '5' })
    const result = updateFromServer(wrapper, 3, 5)
    expect(result.page).toBe(3)
  })

  it('should use server maxPages when valid', () => {
    const wrapper = makeProductListWrapper({ currentPage: '1', totalPage: '5' })
    const result = updateFromServer(wrapper, 1, 8)
    expect(result.maxPages).toBe(8)
  })

  it('should fall back to dataset currentPage when server returns null', () => {
    const wrapper = makeProductListWrapper({ currentPage: '4', totalPage: '10' })
    const result = updateFromServer(wrapper, null, null)
    expect(result.page).toBe(4)
  })

  it('should fall back to dataset totalPage when server returns null', () => {
    const wrapper = makeProductListWrapper({ currentPage: '1', totalPage: '7' })
    const result = updateFromServer(wrapper, null, null)
    expect(result.maxPages).toBe(7)
  })

  it('should fall back to 1 when server returns 0 and dataset is missing', () => {
    const wrapper = makeProductListWrapper()
    const result = updateFromServer(wrapper, 0, 0)
    expect(result.page).toBe(1)
    expect(result.maxPages).toBe(1)
  })

  it('should update wrapper dataset after resolving values', () => {
    const wrapper = makeProductListWrapper()
    updateFromServer(wrapper, 2, 6)
    expect(wrapper.dataset.currentPage).toBe('2')
    expect(wrapper.dataset.totalPage).toBe('6')
  })

  it('should return resolved page and maxPages', () => {
    const wrapper = makeProductListWrapper()
    const result = updateFromServer(wrapper, 3, 9)
    expect(result).toEqual({ page: 3, maxPages: 9 })
  })
})
