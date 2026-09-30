import { parseProductWrapper } from '@/ts/features/products/list/_ui/wrapper'

describe('parseProductWrapper()', () => {
  it('should return the .products__wrapper element when present in html', () => {
    const html = '<div class="products__wrapper"><p>Items</p></div>'
    const result = parseProductWrapper(html)
    expect(result.className).toBe('products__wrapper')
  })

  it('should return the tempDiv when .products__wrapper is not present', () => {
    const html = '<p>No wrapper here</p>'
    const result = parseProductWrapper(html)
    expect(result.querySelector('p')?.textContent).toBe('No wrapper here')
  })

  it('should return an HTMLElement', () => {
    const result = parseProductWrapper('<div class="products__wrapper"></div>')
    expect(result).toBeInstanceOf(HTMLElement)
  })

  it('should preserve inner content of .products__wrapper', () => {
    const html = '<div class="products__wrapper"><span class="item">Product</span></div>'
    const result = parseProductWrapper(html)
    expect(result.querySelector('.item')?.textContent).toBe('Product')
  })

  it('should return tempDiv for empty html string', () => {
    const result = parseProductWrapper('')
    expect(result).toBeInstanceOf(HTMLElement)
  })
})
