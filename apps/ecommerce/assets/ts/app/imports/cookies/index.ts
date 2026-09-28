import type { AppModule } from '@/ts/app/types'

export const cookiesModules: AppModule[] = [
  { selector: '.cookies', module: () => import('@/ts/features/cookies/index') },
]
