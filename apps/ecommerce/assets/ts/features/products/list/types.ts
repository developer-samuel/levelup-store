export type ProductListInstance = {
  productsWrapper: HTMLElement
  page: number
  maxPages: number
  isLoading: boolean
}

export type ProductListParams = {
  brand?: string
  subtype?: string
  minPrice?: string | number
  maxPrice?: string | number
  sort?: string
  page?: number
  [key: string]: string | number | undefined | null
}

export type ValueCallback = (el: HTMLElement) => Record<string, string | number | undefined | null>
