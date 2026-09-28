import type { HtmlElList, HtmlInputList } from '@/ts/shared/types'

export type SearchInstance = {
  panel: HTMLElement
  content: HTMLElement
  inputs: HTMLInputElement[]
  icons: HTMLElement[]
  closes: HTMLElement[]
  userButton: HTMLElement | null
  searchButton: HTMLElement | null
  mobileSearchButton: HTMLElement | null
  mobileClose: HTMLElement | null
  mobileCloseImage: HTMLElement | null
  headerCloseImage: HTMLElement | null
  currentSearchTerm: string
  prevWidthRef: { value: number }
  isClosed: boolean
}

export type SearchElements = {
  inputs: HtmlInputList
  icons: HtmlElList
  closes: HtmlElList
  userButton: HTMLElement | null
  searchButton: HTMLElement | null
  mobileSearchButton: HTMLElement | null
  mobileClose: HTMLElement | null
  mobileCloseImage: HTMLElement | null
  headerCloseImage: HTMLElement | null
}

export type CloseHandlerOptions = {
  inputs: HTMLInputElement[]
  setSearchTerm: (val: string) => void
  updateContent: (html: string) => void
  hidePanel: () => void
  updateUI: (val?: string) => void
  setIsClosed?: (val: boolean) => void
}

export type RenderFetchOptions = {
  loadingHtml: string
  noResultsHtml: string
  errorHtml?: string
  signal?: AbortSignal
  showPanel?: boolean
}

export type ResizeHandlerOptions = {
  instance: SearchInstance
  inputs: HTMLInputElement[]
  prevWidthRef: { value: number }
}
