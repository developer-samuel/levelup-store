import type { AppModule } from '@/ts/app/types'

const main: AppModule[] = [
  { selector: '#login-page', module: () => import('@/ts/features/auth/login/index') },
  { selector: '#signup-page', module: () => import('@/ts/features/auth/signup/index') },
  { selector: '#verification-page', module: () => import('@/ts/features/auth/verification/index') },
  { selector: '.logout-btn', module: () => import('@/ts/features/auth/logout/index') },
]

const password: AppModule[] = [
  { selector: '#forgot-password-page', module: () => import('@/ts/features/auth/password_forgot/index') },
  { selector: '#reset-password-page', module: () => import('@/ts/features/auth/password_reset/index') },
]

export const authModules: AppModule[] = [...main, ...password]
