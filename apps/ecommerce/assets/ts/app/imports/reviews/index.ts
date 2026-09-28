import type { AppModule } from '@/ts/app/types'

export const reviewsModules: AppModule[] = [
  { selector: '#reviews-modal', module: () => import('@/ts/features/reviews/modal/index') },
  { selector: '#reviews-create-form', module: () => import('@/ts/features/reviews/create/index') },
  { selector: '.reviews__card', module: () => import('@/ts/features/reviews/list/index') },
  { selector: '.reviews__card', module: () => import('@/ts/features/reviews/dropdown/index') },
  { selector: '.reviews__card', module: () => import('@/ts/features/reviews/rating/index') },
  { selector: '.reviews[data-mercure-hub]', module: () => import('@/ts/features/reviews/_realtime/ratings') },
]
