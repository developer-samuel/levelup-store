import Swiper from 'swiper'
import { Autoplay } from 'swiper/modules'
import type { SwiperOptions } from 'swiper/types'

import { query } from '@/ts/shared/utils/dom/query'

type SwiperInstance = {
  init(): void
  getInstance(): Swiper | null
}

// Register Autoplay module globally for consistent slider behaviour
Swiper.use([Autoplay])

class SwiperSlider implements SwiperInstance {
  private readonly selector: string
  private readonly options: SwiperOptions
  private swiperInstance: Swiper | null = null

  constructor(selector: string, options: SwiperOptions = {}) {
    this.selector = selector
    this.options = options
  }

  init(): void {
    const sliderElement = query<HTMLElement>(this.selector)
    if (!sliderElement) {
      throw new Error(`SwiperSlider: No element found for selector '${this.selector}'`)
    }

    this.swiperInstance = new Swiper(sliderElement, this.options)
  }

  getInstance(): Swiper | null {
    return this.swiperInstance
  }
}

export default SwiperSlider
