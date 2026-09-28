import { isAuth } from '@/ts/core/jwt/isAuth'

describe('isAuth()', () => {
  afterEach(() => {
    delete document.body.dataset.authenticated
  })

  it('should return true when body dataset authenticated is "true"', () => {
    document.body.dataset.authenticated = 'true'
    expect(isAuth()).toBe(true)
  })

  it('should return false when body dataset authenticated is "false"', () => {
    document.body.dataset.authenticated = 'false'
    expect(isAuth()).toBe(false)
  })

  it('should return false when body dataset authenticated is not set', () => {
    expect(isAuth()).toBe(false)
  })

  it('should return false when body dataset authenticated is an arbitrary string', () => {
    document.body.dataset.authenticated = 'yes'
    expect(isAuth()).toBe(false)
  })
})
