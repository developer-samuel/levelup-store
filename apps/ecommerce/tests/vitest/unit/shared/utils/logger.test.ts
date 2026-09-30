vi.mock('@/ts/app/config/env.config', () => ({
  ENV_CONFIG: { APP_ENV: undefined as string | undefined },
}))

import { logDevError } from '@/ts/shared/utils/logger'

import { ENV_CONFIG } from '@/ts/app/config/env.config'

const mockedEnv = ENV_CONFIG as { APP_ENV: string | undefined }

describe('logDevError()', () => {
  beforeEach(() => {
    vi.spyOn(console, 'error').mockImplementation(() => undefined)
  })

  afterEach(() => {
    vi.restoreAllMocks()
  })

  it('should log to console.error in dev environment', () => {
    mockedEnv.APP_ENV = 'dev'

    logDevError('[API]', new Error('test error'))

    expect(console.error).toHaveBeenCalledTimes(1)
  })

  it('should not log to console.error in prod environment', () => {
    mockedEnv.APP_ENV = 'prod'

    logDevError('[API]', new Error('test error'))

    expect(console.error).not.toHaveBeenCalled()
  })

  it('should not log to console.error when APP_ENV is undefined', () => {
    mockedEnv.APP_ENV = undefined

    logDevError('[API]', new Error('test error'))

    expect(console.error).not.toHaveBeenCalled()
  })

  it('should include context and error in the log message', () => {
    mockedEnv.APP_ENV = 'dev'
    const error = new Error('something failed')

    logDevError('[Service]', error)

    expect(console.error).toHaveBeenCalledWith('❌ [Service] error:', error)
  })
})
