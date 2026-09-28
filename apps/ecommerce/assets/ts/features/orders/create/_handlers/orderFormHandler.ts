import type { AxiosError } from 'axios'

import type { FormResponse } from '@/ts/shared/elements/form/types'
import { createFormHandler } from '@/ts/shared/elements/form/_factory/formHandlerFactory'

import type { CartResponse } from '@/ts/features/cart/types'
import { renderCart } from '@/ts/features/cart/_ui/render'
import orderSubmit from '@/ts/features/orders/create/_services/orderService'

type OrderErrorResponse = FormResponse & { cart?: CartResponse }

export const handleOrderFormSubmit = createFormHandler(orderSubmit, {
  onSuccess: () => {},
  onHttpError: (error: AxiosError<OrderErrorResponse>) => {
    const data = error.response?.data
    if (error.response?.status === 409 && data?.cart) {
      renderCart(data.cart)
    }
  },
})
