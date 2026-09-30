import { query, queryAll } from '@/ts/shared/utils/dom/query'

describe('query()', () => {
  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should return element when found', () => {
    const el = document.createElement('div')
    el.id = 'test'
    document.body.appendChild(el)
    expect(query('#test')).toBe(el)
  })

  it('should return null when not found', () => {
    expect(query('#nonexistent')).toBeNull()
  })
})

describe('queryAll()', () => {
  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should return matching elements', () => {
    const a = document.createElement('div')
    const b = document.createElement('div')
    a.className = 'item'
    b.className = 'item'
    document.body.append(a, b)
    expect(queryAll('.item').length).toBe(2)
  })

  it('should return empty NodeList when none found', () => {
    expect(queryAll('.nonexistent').length).toBe(0)
  })
})
