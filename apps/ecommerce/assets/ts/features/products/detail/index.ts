import ImageCarousel from '@/ts/features/products/detail/_components/ImageCarousel'
import Scroll from '@/ts/features/products/detail/_components/Scroll'

ImageCarousel.init()
Scroll.init('#scroll-descriptions span', '.product-detail__description')
Scroll.init('#scroll-review span', '.reviews')
