export type Order = {
  id: number
  code: string
  price: number | string
  payment: string
  isPaid: boolean
  status: string
  createdAt: string
  [key: string]: unknown
}

export type OrderRenderOptions = {
  rowStyle?: (row: Order) => string | undefined
}
