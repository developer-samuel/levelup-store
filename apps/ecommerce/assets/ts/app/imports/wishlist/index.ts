import type { AppModule } from '@/ts/app/types'

export const wishlistsModules: AppModule[] = [
  { selector: '.product-detail__details-wishlist', module: () => import('@/ts/features/wishlist/toggle/index') },
  { selector: '.wishlist', module: () => import('@/ts/features/wishlist/destroy/index') },
]
