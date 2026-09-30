import type { BaseModalInstance } from '@/ts/shared/elements/modal/types'

export type ZoomModalInstance = BaseModalInstance & {
  img: HTMLImageElement
  close: HTMLElement
}
