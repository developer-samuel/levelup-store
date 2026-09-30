vi.mock('@/ts/core/http/api', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
  },
}))

import api from '@/ts/core/http/api'
import { submitFormData } from '@/ts/core/http/_services/submitFormData'

const mockedPost = vi.mocked(api.post)

describe('submitFormData()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should return response data on success', async () => {
    mockedPost.mockResolvedValueOnce({ data: { success: true } })

    const result = await submitFormData('/test', { key: 'value' })

    expect(result).toEqual({ success: true })
  })

  it('should send Content-Type application/json for plain objects', async () => {
    mockedPost.mockResolvedValueOnce({ data: {} })

    await submitFormData('/test', { key: 'value' })

    expect(mockedPost).toHaveBeenCalledWith(
      '/test',
      { key: 'value' },
      expect.objectContaining({ headers: { 'Content-Type': 'application/json' } }),
    )
  })

  it('should send empty headers for FormData', async () => {
    mockedPost.mockResolvedValueOnce({ data: {} })

    const formData = new FormData()
    formData.append('field', 'value')

    await submitFormData('/test', formData)

    expect(mockedPost).toHaveBeenCalledWith('/test', formData, expect.objectContaining({ headers: {} }))
  })

  it('should return null and skip POST when duplicate request is in flight', async () => {
    let resolveFirst!: (value: unknown) => void
    const pending = new Promise((resolve) => {
      resolveFirst = resolve
    })

    mockedPost.mockReturnValueOnce(pending)

    const first = submitFormData('/test', null)
    const second = await submitFormData('/test', null)

    expect(second).toBeNull()
    expect(mockedPost).toHaveBeenCalledTimes(1)

    resolveFirst({ data: {} })
    await first
  })

  it('should allow a new request after the previous one completes', async () => {
    mockedPost.mockResolvedValueOnce({ data: { id: 1 } }).mockResolvedValueOnce({ data: { id: 2 } })

    await submitFormData('/test', null)
    const result = await submitFormData('/test', null)

    expect(mockedPost).toHaveBeenCalledTimes(2)
    expect(result).toEqual({ id: 2 })
  })

  it('should remove pending request after error', async () => {
    mockedPost.mockRejectedValueOnce(new Error('Network error'))

    await expect(submitFormData('/test', null)).rejects.toThrow('Network error')

    mockedPost.mockResolvedValueOnce({ data: { ok: true } })
    const result = await submitFormData('/test', null)

    expect(result).toEqual({ ok: true })
    expect(mockedPost).toHaveBeenCalledTimes(2)
  })

  it('should skip deduplication when checkSubmitting is false', async () => {
    let resolveFirst!: (value: unknown) => void
    const pending = new Promise((resolve) => {
      resolveFirst = resolve
    })

    mockedPost.mockReturnValueOnce(pending)
    mockedPost.mockResolvedValueOnce({ data: { id: 2 } })

    const first = submitFormData('/test', null, true, false, false)
    const second = await submitFormData('/test', null, true, false, false)

    expect(mockedPost).toHaveBeenCalledTimes(2)
    expect(second).toEqual({ id: 2 })

    resolveFirst({ data: { id: 1 } })
    await first
  })
})
