import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'

export function handleOrderClick(e: MouseEvent): void {
  handleTableClick(e, {
    hrefContains: ['/admin/orders/show'],
  })
}
