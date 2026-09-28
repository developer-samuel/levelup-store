import type { ProductListInstance } from '@/ts/features/products/list/types'
import { updateProductList } from '@/ts/features/products/list/_ui/updater'

function makeInstance(wrapperHtml = ''): ProductListInstance & { productsWrapper: HTMLElement } {
  const wrapper = document.createElement('div')

  wrapper.innerHTML = wrapperHtml

  return { productsWrapper: wrapper, page: 1, maxPages: 5, isLoading: false } as unknown as ProductListInstance & {
    productsWrapper: HTMLElement
  }
}

function makeEl(html: string): HTMLElement {
  const el = document.createElement('div')
  el.innerHTML = html

  return el
}

describe('updateProductList()', () => {
  it('should appendNewItems when requestedPage > 1 and newList + oldList exist', () => {
    const oldList = document.createElement('ul')
    oldList.innerHTML = '<li>old</li>'

    const newList = document.createElement('ul')
    newList.innerHTML = '<li>new</li><div class="products__card-load-more"></div>'

    const instance = makeInstance()

    instance.productsWrapper.appendChild(oldList)

    updateProductList(newList, oldList, makeEl(''), null, 2, instance)

    expect(oldList.querySelectorAll('li')).toHaveLength(2)
  })

  it('should not append load-more elements when appending new items', () => {
    const oldList = document.createElement('ul')
    const newList = document.createElement('ul')

    newList.innerHTML = '<li>item</li><div class="products__card-load-more"></div>'

    const instance = makeInstance()

    instance.productsWrapper.appendChild(oldList)

    updateProductList(newList, oldList, makeEl(''), null, 2, instance)

    expect(oldList.querySelector('.products__card-load-more')).toBeNull()
  })

  it('should insertItemsBeforeNode when requestedPage > 1 and no newList/oldList', () => {
    const instance = makeInstance()
    const beforeNode = document.createElement('div')

    beforeNode.className = 'products__card-load-more'

    instance.productsWrapper.appendChild(beforeNode)

    const returned = makeEl('<p class="item">Item</p>')

    updateProductList(null, null, returned, beforeNode, 2, instance)

    expect(instance.productsWrapper.querySelector('.item')).not.toBeNull()
  })

  it('should replaceListContent when page <= 1 and newList + oldList exist', () => {
    const oldList = document.createElement('ul')
    oldList.innerHTML = '<li>old</li>'

    const newList = document.createElement('ul')
    newList.innerHTML = '<li>new</li>'

    const instance = makeInstance()
    instance.productsWrapper.appendChild(oldList)

    updateProductList(newList, oldList, makeEl(''), null, 1, instance)

    expect(oldList.querySelector('li')?.textContent).toBe('new')
  })

  it('should replaceWrapperContent when page <= 1 and no newList/oldList', () => {
    const instance = makeInstance('<p>old</p>')
    const returned = makeEl('<p class="new-item">new</p>')

    updateProductList(null, null, returned, null, 1, instance)

    expect(instance.productsWrapper.querySelector('.new-item')).not.toBeNull()
  })

  it('should replaceWrapperContent when requestedPage is undefined', () => {
    const instance = makeInstance('<p>old</p>')
    const returned = makeEl('<p class="replaced">replaced</p>')

    updateProductList(null, null, returned, null, undefined, instance)

    expect(instance.productsWrapper.querySelector('.replaced')).not.toBeNull()
  })

  it('should replace old load-more with new one', () => {
    const instance = makeInstance()
    const oldLoadMore = document.createElement('div')

    oldLoadMore.className = 'products__card-load-more'
    oldLoadMore.textContent = 'old'

    instance.productsWrapper.appendChild(oldLoadMore)

    const returned = makeEl('<div class="products__card-load-more">new</div>')

    updateProductList(null, null, returned, null, 1, instance)

    expect(instance.productsWrapper.querySelector('.products__card-load-more')?.textContent).toBe('new')
  })

  it('should append new load-more when none existed before', () => {
    const instance = makeInstance()
    const returned = makeEl('<div class="products__card-load-more">load</div>')

    updateProductList(null, null, returned, null, 1, instance)

    expect(instance.productsWrapper.querySelector('.products__card-load-more')).not.toBeNull()
  })

  it('should remove old load-more when returned wrapper has none', () => {
    const instance = makeInstance()
    const oldLoadMore = document.createElement('div')

    oldLoadMore.className = 'products__card-load-more'

    instance.productsWrapper.appendChild(oldLoadMore)

    updateProductList(null, null, makeEl('<p>content</p>'), null, 1, instance)

    expect(instance.productsWrapper.querySelector('.products__card-load-more')).toBeNull()
  })

  it('should insertItemsBeforeNode with appendChild when beforeNode is null', () => {
    const instance = makeInstance()
    const returned = makeEl('<p class="inserted">inserted</p>')

    updateProductList(null, null, returned, null, 2, instance)

    expect(instance.productsWrapper.querySelector('.inserted')).not.toBeNull()
  })

  it('should append load-more from returned when none exists in wrapper and page > 1', () => {
    const instance = makeInstance()
    const returned = makeEl('<p class="item">item</p><div class="products__card-load-more">new</div>')

    updateProductList(null, null, returned, null, 2, instance)

    expect(instance.productsWrapper.querySelector('.products__card-load-more')).not.toBeNull()
  })

  it('should skip text nodes when insertItemsBeforeNode', () => {
    const instance = makeInstance()
    const returned = document.createElement('div')
    returned.appendChild(document.createTextNode('ignored text'))
    const p = document.createElement('p')
    p.className = 'real-item'
    returned.appendChild(p)

    updateProductList(null, null, returned, null, 2, instance)

    expect(instance.productsWrapper.querySelector('.real-item')).not.toBeNull()
  })

  it('should not insert load-more elements when insertItemsBeforeNode', () => {
    const instance = makeInstance()
    const existingLoadMore = document.createElement('div')

    existingLoadMore.className = 'products__card-load-more'
    existingLoadMore.textContent = 'old'

    instance.productsWrapper.appendChild(existingLoadMore)

    const returned = makeEl('<p class="item">item</p><div class="products__card-load-more">new</div>')

    updateProductList(null, null, returned, existingLoadMore, 2, instance)

    expect(instance.productsWrapper.querySelector('.item')).not.toBeNull()

    const loadMores = instance.productsWrapper.querySelectorAll('.products__card-load-more')

    expect(loadMores).toHaveLength(1)
  })
})
