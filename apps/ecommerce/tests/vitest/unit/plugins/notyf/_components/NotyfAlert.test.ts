vi.mock('notyf', () => ({
  Notyf: vi.fn().mockImplementation(function (this: {
    success: ReturnType<typeof vi.fn>
    error: ReturnType<typeof vi.fn>
    open: ReturnType<typeof vi.fn>
  }) {
    this.success = vi.fn()
    this.error = vi.fn()
    this.open = vi.fn()
  }),
}))

vi.mock('notyf/notyf.min.css', () => ({}))

vi.mock('@/ts/plugins/notyf/config', () => ({
  default: {},
}))

import { Notyf } from 'notyf'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

const mockedNotyf = vi.mocked(Notyf)

function getNotyfInstance(): {
  success: ReturnType<typeof vi.fn>
  error: ReturnType<typeof vi.fn>
  open: ReturnType<typeof vi.fn>
} {
  return mockedNotyf.mock.results[0]?.value as {
    success: ReturnType<typeof vi.fn>
    error: ReturnType<typeof vi.fn>
    open: ReturnType<typeof vi.fn>
  }
}

describe('NotyfAlert', () => {
  it('should call notyf.success with message', () => {
    NotyfAlert.success('Saved.')
    expect(getNotyfInstance().success).toHaveBeenCalledWith('Saved.')
  })

  it('should call notyf.error with message', () => {
    NotyfAlert.error('Failed.')
    expect(getNotyfInstance().error).toHaveBeenCalledWith('Failed.')
  })

  it('should call notyf.open with info type and message', () => {
    NotyfAlert.info('Note.')
    expect(getNotyfInstance().open).toHaveBeenCalledWith({ type: 'info', message: 'Note.' })
  })
})
