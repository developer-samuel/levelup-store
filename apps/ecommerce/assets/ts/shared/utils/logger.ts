import { ENV_CONFIG } from '@/ts/app/config/env.config'

export function logDevError(context: string, error: unknown): void {
  if (ENV_CONFIG.APP_ENV === 'dev') {
    console.error(`❌ ${context} error:`, error)
  }
}
