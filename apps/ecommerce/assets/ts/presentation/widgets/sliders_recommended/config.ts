import type { SwiperOptions } from 'swiper/types'

import { BREAKPOINT_SM, BREAKPOINT_XL, BREAKPOINT_2XL } from '@/ts/shared/constants/breakpoints'

const recommendedSliderConfig: SwiperOptions = {
  slidesPerView: 1,
  spaceBetween: 10,
  loop: true,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
    pauseOnMouseEnter: true,
  },
  breakpoints: {
    [BREAKPOINT_SM]: { slidesPerView: 2 },
    [BREAKPOINT_XL]: { slidesPerView: 3 },
    [BREAKPOINT_2XL]: { slidesPerView: 4 },
  },
}

export default recommendedSliderConfig
