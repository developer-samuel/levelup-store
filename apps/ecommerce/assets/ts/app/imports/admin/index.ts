import type { AppModule } from '@/ts/app/types'
import { adminDashboardModules } from '@/ts/app/imports/admin/dashboard/index'
import { adminBannersModules } from '@/ts/app/imports/admin/banners/index'
import { adminBrandsModules } from '@/ts/app/imports/admin/brands/index'
import { adminProductsModules } from '@/ts/app/imports/admin/products/index'
import { adminProductsVariantsModules } from '@/ts/app/imports/admin/products_variants/index'
import { adminOrdersModules } from '@/ts/app/imports/admin/orders/index'
import { adminUsersModules } from '@/ts/app/imports/admin/users/index'

export const adminModules: AppModule[] = [
  ...adminDashboardModules,
  ...adminBannersModules,
  ...adminBrandsModules,
  ...adminProductsModules,
  ...adminProductsVariantsModules,
  ...adminOrdersModules,
  ...adminUsersModules,
]
