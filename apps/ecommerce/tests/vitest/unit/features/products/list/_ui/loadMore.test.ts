import { checkLoadMoreVisibility, normalizeLoadMore } from '@/ts/features/products/list/_ui/loadMore'

function makeRoot(containers: number): { root: HTMLElement; containers: HTMLElement[] } {
  const root = document.createElement('div')
  const els: HTMLElement[] = []
  for (let i = 0; i < containers; i++) {
    const c = document.createElement('div')
    c.className = 'products__card-load-more'
    root.appendChild(c)
    els.push(c)
  }
  return { root, containers: els }
}

describe('checkLoadMoreVisibility()', () => {
  it('should do nothing when no containers exist', () => {
    const root = document.createElement('div')
    expect(() => checkLoadMoreVisibility(1, 5, root)).not.toThrow()
  })

  it('should add hidden class to all containers when page >= maxPages', () => {
    const { root, containers } = makeRoot(2)
    checkLoadMoreVisibility(5, 5, root)
    containers.forEach((c) => expect(c.classList.contains('products__card-load-more--hidden')).toBe(true))
  })

  it('should remove hidden class from first container when page < maxPages', () => {
    const { root, containers } = makeRoot(1)
    const first = containers[0]
    if (!first) throw new Error('Expected container')
    first.classList.add('products__card-load-more--hidden')
    checkLoadMoreVisibility(1, 5, root)
    expect(first.classList.contains('products__card-load-more--hidden')).toBe(false)
  })

  it('should remove extra containers beyond the first when page < maxPages', () => {
    const { root } = makeRoot(3)
    checkLoadMoreVisibility(1, 5, root)
    expect(root.querySelectorAll('.products__card-load-more')).toHaveLength(1)
  })

  it('should treat non-finite page as 1', () => {
    const { root, containers } = makeRoot(1)
    const first = containers[0]
    if (!first) throw new Error('Expected container')
    checkLoadMoreVisibility(NaN, 1, root)
    expect(first.classList.contains('products__card-load-more--hidden')).toBe(true)
  })

  it('should treat non-finite maxPages as 1', () => {
    const { root, containers } = makeRoot(1)
    const first = containers[0]
    if (!first) throw new Error('Expected container')
    checkLoadMoreVisibility(1, NaN, root)
    expect(first.classList.contains('products__card-load-more--hidden')).toBe(true)
  })
})

describe('normalizeLoadMore()', () => {
  it('should do nothing when only one container exists', () => {
    const wrapper = document.createElement('div')
    const c = document.createElement('div')
    c.className = 'products__card-load-more'
    c.classList.add('products__card-load-more--hidden')
    wrapper.appendChild(c)

    normalizeLoadMore(wrapper)

    expect(c.classList.contains('products__card-load-more--hidden')).toBe(true)
  })

  it('should remove all containers except the first when multiple exist', () => {
    const wrapper = document.createElement('div')
    for (let i = 0; i < 3; i++) {
      const c = document.createElement('div')
      c.className = 'products__card-load-more'
      wrapper.appendChild(c)
    }

    normalizeLoadMore(wrapper)

    expect(wrapper.querySelectorAll('.products__card-load-more')).toHaveLength(1)
  })

  it('should remove hidden class from first container', () => {
    const wrapper = document.createElement('div')
    for (let i = 0; i < 2; i++) {
      const c = document.createElement('div')
      c.className = 'products__card-load-more products__card-load-more--hidden'
      wrapper.appendChild(c)
    }

    normalizeLoadMore(wrapper)

    expect(
      wrapper.querySelector('.products__card-load-more')?.classList.contains('products__card-load-more--hidden'),
    ).toBe(false)
  })

  it('should do nothing when zero containers exist', () => {
    const wrapper = document.createElement('div')
    expect(() => normalizeLoadMore(wrapper)).not.toThrow()
  })

  it('should not remove single #load-more button inside first container', () => {
    const wrapper = document.createElement('div')
    const c1 = document.createElement('div')
    c1.className = 'products__card-load-more'
    const btn = document.createElement('button')
    btn.id = 'load-more'
    c1.appendChild(btn)
    const c2 = document.createElement('div')
    c2.className = 'products__card-load-more'
    wrapper.append(c1, c2)

    normalizeLoadMore(wrapper)

    expect(wrapper.querySelectorAll('#load-more')).toHaveLength(1)
  })

  it('should remove duplicate #load-more buttons inside first container', () => {
    const wrapper = document.createElement('div')
    const c1 = document.createElement('div')
    c1.className = 'products__card-load-more'
    for (let i = 0; i < 3; i++) {
      const btn = document.createElement('button')
      btn.id = 'load-more'
      c1.appendChild(btn)
    }
    const c2 = document.createElement('div')
    c2.className = 'products__card-load-more'
    wrapper.append(c1, c2)

    normalizeLoadMore(wrapper)

    expect(wrapper.querySelectorAll('#load-more')).toHaveLength(1)
  })
})
