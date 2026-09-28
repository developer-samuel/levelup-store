import type { AppModule } from '@/ts/app/types'

export const widgetsModules: AppModule[] = [
  { selector: '.loading', module: () => import('@/ts/presentation/widgets/loading/index') },
  { selector: '.cursor', module: () => import('@/ts/presentation/widgets/cursor/index') },
  { selector: '#discount-banner', module: () => import('@/ts/presentation/widgets/banners_discounts/index') },
  { selector: '#scroll-to-top', module: () => import('@/ts/presentation/widgets/scroll_top/index') },
  { selector: '.products-swiper', module: () => import('@/ts/presentation/widgets/sliders_recommended/index') },
  { selector: '.header__main-user-dropdown', module: () => import('@/ts/presentation/widgets/user_dropdown/index') },
  {
    selector: '#login-page, #signup-page, #reset-password-page, #change-password-page',
    module: () => import('@/ts/presentation/widgets/password_toggle/index'),
  },
]
