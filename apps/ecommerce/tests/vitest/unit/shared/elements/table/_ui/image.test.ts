import { createTableImage } from '@/ts/shared/elements/table/_ui/image'

describe('createTableImage()', () => {
  it('should return an img element', () => {
    const img = createTableImage('photo.jpg')
    expect(img.tagName).toBe('IMG')
  })

  it('should set the src attribute', () => {
    const img = createTableImage('photo.jpg')
    expect(img.src).toContain('photo.jpg')
  })

  it('should use default alt when not provided', () => {
    const img = createTableImage('photo.jpg')
    expect(img.alt).toBe('Image')
  })

  it('should use provided alt', () => {
    const img = createTableImage('photo.jpg', { alt: 'Product' })
    expect(img.alt).toBe('Product')
  })

  it('should use default width auto', () => {
    const img = createTableImage('photo.jpg')
    expect(img.style.width).toBe('auto')
  })

  it('should use provided width', () => {
    const img = createTableImage('photo.jpg', { width: '50px' })
    expect(img.style.width).toBe('50px')
  })

  it('should use default height 100px', () => {
    const img = createTableImage('photo.jpg')
    expect(img.style.height).toBe('100px')
  })

  it('should use provided height', () => {
    const img = createTableImage('photo.jpg', { height: '200px' })
    expect(img.style.height).toBe('200px')
  })

  it('should use default objectFit cover', () => {
    const img = createTableImage('photo.jpg')
    expect(img.style.objectFit).toBe('cover')
  })

  it('should use provided objectFit', () => {
    const img = createTableImage('photo.jpg', { objectFit: 'contain' })
    expect(img.style.objectFit).toBe('contain')
  })

  it('should use default borderRadius empty string', () => {
    const img = createTableImage('photo.jpg')
    expect(img.style.borderRadius).toBe('')
  })

  it('should use provided borderRadius', () => {
    const img = createTableImage('photo.jpg', { borderRadius: '50%' })
    expect(img.style.borderRadius).toBe('50%')
  })
})
