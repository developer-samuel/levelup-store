import NOTYF_CONFIG from '@/ts/plugins/notyf/config'

describe('NOTYF_CONFIG', () => {
  it('should have duration 7500 for error type', () => {
    const error = NOTYF_CONFIG.types?.find((t) => t.type === 'error')
    expect(error?.duration).toBe(7500)
  })

  it('should have position center top', () => {
    expect(NOTYF_CONFIG.position).toEqual({ x: 'center', y: 'top' })
  })

  it('should be dismissible', () => {
    expect(NOTYF_CONFIG.dismissible).toBe(true)
  })

  it('should have ripple disabled', () => {
    expect(NOTYF_CONFIG.ripple).toBe(false)
  })

  it('should define success type with correct background', () => {
    const success = NOTYF_CONFIG.types?.find((t) => t.type === 'success')
    expect(success?.background).toBe('#22C55E')
  })

  it('should define error type with correct background', () => {
    const error = NOTYF_CONFIG.types?.find((t) => t.type === 'error')
    expect(error?.background).toBe('#FF4F4F')
  })

  it('should define info type with correct background', () => {
    const info = NOTYF_CONFIG.types?.find((t) => t.type === 'info')
    expect(info?.background).toBe('#3DBBFF')
  })
})
