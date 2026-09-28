import type { AppModule } from '@/ts/app/types'

export const usersModules: AppModule[] = [
  { selector: '#change-password-page', module: () => import('@/ts/features/users/password_change/index') },
  { selector: '#profile-page', module: () => import('@/ts/features/users/profile/index') },
  { selector: '#delete-account-btn', module: () => import('@/ts/features/users/profile_destroy/index') },
]
