import type { AppModule } from '@/ts/app/types'

export const layoutModules: AppModule[] = [
  { selector: '.header', module: () => import('@/ts/presentation/layout/header/index') },
  { selector: '.navigation', module: () => import('@/ts/presentation/layout/navigation/index') },
  { selector: '.header__main-search', module: () => import('@/ts/features/search/index') },
  { selector: '.cart', module: () => import('@/ts/features/cart/index') },
]
