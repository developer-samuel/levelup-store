import type { AppModule } from '@/ts/app/types'

export const searchModules: AppModule[] = [
  { selector: '.search-panel', module: () => import('@/ts/features/search/index') },
]
