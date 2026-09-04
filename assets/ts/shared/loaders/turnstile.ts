const TURNSTILE_SRC = 'https://challenges.cloudflare.com/turnstile/v0/api.js'

/**
 * Dynamically injects the Cloudflare Turnstile script.
 *
 * Safe to call multiple times - skips if already injected.
 */
export function loadTurnstile(): void {
  if (!document.querySelector('.cf-turnstile')) return
  if (document.querySelector(`script[src="${TURNSTILE_SRC}"]`)) return

  const script = document.createElement('script')
  script.src = TURNSTILE_SRC
  script.async = true
  script.defer = true
  document.head.appendChild(script)
}
