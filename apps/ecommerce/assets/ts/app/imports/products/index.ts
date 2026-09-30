import type { AppModule } from '@/ts/app/types'

export const productsModules: AppModule[] = [
  { selector: '.products', module: () => import('@/ts/features/products/list/index') },
  { selector: '.product-detail', module: () => import('@/ts/features/products/detail/index') },
  {
    selector: '.product-detail__details-stocks[data-mercure-hub]',
    module: () => import('@/ts/features/products/_realtime/stock'),
  },
]
