import { mockUtilsDomQuery } from '@/tests/_support/mocks/shared/utils.mocks'

mockUtilsDomQuery()

vi.mock('swiper', () => ({
  default: Object.assign(
    vi.fn().mockImplementation(function () {
      return { swiper: true }
    }),
    { use: vi.fn() },
  ),
}))

vi.mock('swiper/modules', () => ({
  Autoplay: {},
}))

import Swiper from 'swiper'

import { query } from '@/ts/shared/utils/dom/query'

import SwiperSlider from '@/ts/plugins/swiper/Swiper'

const mockedSwiper = vi.mocked(Swiper)
const mockedQuery = vi.mocked(query)

describe('SwiperSlider', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should return null from getInstance before init', () => {
    const slider = new SwiperSlider('.swiper')
    expect(slider.getInstance()).toBeNull()
  })

  it('should throw when selector matches no element', () => {
    mockedQuery.mockReturnValueOnce(null)
    const slider = new SwiperSlider('.swiper')
    expect(() => slider.init()).toThrow("SwiperSlider: No element found for selector '.swiper'")
  })

  it('should instantiate Swiper with element and options on init', () => {
    const el = document.createElement('div')
    mockedQuery.mockReturnValueOnce(el)
    const options = { loop: true }

    const slider = new SwiperSlider('.swiper', options)
    slider.init()

    expect(mockedSwiper).toHaveBeenCalledWith(el, options)
  })

  it('should return Swiper instance from getInstance after init', () => {
    const el = document.createElement('div')
    mockedQuery.mockReturnValueOnce(el)

    const slider = new SwiperSlider('.swiper')
    slider.init()

    expect(slider.getInstance()).toEqual({ swiper: true })
  })

  it('should use empty options by default', () => {
    const el = document.createElement('div')
    mockedQuery.mockReturnValueOnce(el)

    new SwiperSlider('.swiper').init()

    expect(mockedSwiper).toHaveBeenCalledWith(el, {})
  })
})
