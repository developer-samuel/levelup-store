import type { AppModule } from '@/ts/app/types'
import { layoutModules } from '@/ts/app/imports/layout/index'
import { widgetsModules } from '@/ts/app/imports/widgets/index'
import { cookiesModules } from '@/ts/app/imports/cookies/index'
import { authModules } from '@/ts/app/imports/auth/index'
import { usersModules } from '@/ts/app/imports/users/index'
import { productsModules } from '@/ts/app/imports/products/index'
import { reviewsModules } from '@/ts/app/imports/reviews/index'
import { wishlistsModules } from '@/ts/app/imports/wishlist/index'
import { ordersModules } from '@/ts/app/imports/orders/index'
import { adminModules } from '@/ts/app/imports/admin/index'
import { searchModules } from '@/ts/app/imports/search/index'

export const modules: AppModule[] = [
  ...layoutModules,
  ...widgetsModules,
  ...cookiesModules,
  ...authModules,
  ...usersModules,
  ...productsModules,
  ...reviewsModules,
  ...wishlistsModules,
  ...ordersModules,
  ...adminModules,
  ...searchModules,
]
