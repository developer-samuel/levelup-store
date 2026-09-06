import { loadTurnstile } from '@/ts/shared/loaders/turnstile'

const TURNSTILE_SRC = 'https://challenges.cloudflare.com/turnstile/v0/api.js'

describe('loadTurnstile()', () => {
  beforeEach(() => {
    document.head.innerHTML = ''
    document.body.innerHTML = ''
  })

  it('should not inject script when .cf-turnstile element is absent', () => {
    loadTurnstile()

    expect(document.querySelector(`script[src="${TURNSTILE_SRC}"]`)).toBeNull()
  })

  it('should inject script when .cf-turnstile element is present', () => {
    document.body.innerHTML = '<div class="cf-turnstile"></div>'

    loadTurnstile()

    expect(document.querySelector(`script[src="${TURNSTILE_SRC}"]`)).not.toBeNull()
  })

  it('should set async and defer on the injected script', () => {
    document.body.innerHTML = '<div class="cf-turnstile"></div>'

    loadTurnstile()

    const script = document.querySelector<HTMLScriptElement>(`script[src="${TURNSTILE_SRC}"]`)
    expect(script?.async).toBe(true)
    expect(script?.defer).toBe(true)
  })

  it('should not inject script twice when called multiple times', () => {
    document.body.innerHTML = '<div class="cf-turnstile"></div>'

    loadTurnstile()
    loadTurnstile()

    expect(document.querySelectorAll(`script[src="${TURNSTILE_SRC}"]`)).toHaveLength(1)
  })

  it('should append script to document.head', () => {
    document.body.innerHTML = '<div class="cf-turnstile"></div>'

    loadTurnstile()

    expect(document.head.querySelector(`script[src="${TURNSTILE_SRC}"]`)).not.toBeNull()
  })
})
