import type { AppModule } from '@/ts/app/types'

export const adminVariantImagesModules: AppModule[] = [
  {
    selector: '#admin-variant-images-table',
    module: () => import('@/ts/features/admin/products_variants_images/list/index'),
  },
]
