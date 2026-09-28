import OrderForm from '@/ts/features/orders/create/_components/OrderForm'
import OrderShipping from '@/ts/features/orders/create/_components/OrderShipping'

new OrderForm('#order-form')
new OrderShipping('send-shipping', 'order-shipping-data')
