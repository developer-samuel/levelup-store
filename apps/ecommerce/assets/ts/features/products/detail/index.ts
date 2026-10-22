import ImageCarousel from '@/ts/features/products/detail/_components/ImageCarousel'
import Scroll from '@/ts/features/products/detail/_components/Scroll'

void ImageCarousel.init()
void Scroll.init('#scroll-descriptions span', '.product-detail__description')
void Scroll.init('#scroll-review span', '.reviews')
