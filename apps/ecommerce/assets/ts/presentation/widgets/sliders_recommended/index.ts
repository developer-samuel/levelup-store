import SwiperSlider from '@/ts/plugins/swiper/Swiper'

import config from '@/ts/presentation/widgets/sliders_recommended/config'

const slider = new SwiperSlider('.products-swiper .slider', config)
slider.init()
