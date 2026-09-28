import type { HtmlElList } from '@/ts/shared/types'
import { query, queryAll } from '@/ts/shared/utils/dom/query'

export function getImageCarouselElements(): {
  track: HTMLElement | null
  thumbs: HtmlElList
} {
  return {
    track: query('.product-detail__gallery-image-track'),
    thumbs: queryAll('.product-detail__gallery-item'),
  }
}
