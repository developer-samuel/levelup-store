import { accessToken } from '@/ts/core/jwt/accessToken'

describe('accessToken', () => {
  beforeEach(() => {
    accessToken.clear()
  })

  describe('get()', () => {
    it('should return null when no token has been set', () => {
      expect(accessToken.get()).toBeNull()
    })

    it('should return the token after set()', () => {
      accessToken.set('my-token-abc')
      expect(accessToken.get()).toBe('my-token-abc')
    })
  })

  describe('set()', () => {
    it('should store the given token value', () => {
      accessToken.set('token-xyz')
      expect(accessToken.get()).toBe('token-xyz')
    })

    it('should overwrite the previous token when called again', () => {
      accessToken.set('first-token')
      accessToken.set('second-token')
      expect(accessToken.get()).toBe('second-token')
    })
  })

  describe('clear()', () => {
    it('should set the token back to null', () => {
      accessToken.set('some-token')
      accessToken.clear()
      expect(accessToken.get()).toBeNull()
    })

    it('should not throw when called with no token set', () => {
      expect(() => accessToken.clear()).not.toThrow()
    })
  })
})
