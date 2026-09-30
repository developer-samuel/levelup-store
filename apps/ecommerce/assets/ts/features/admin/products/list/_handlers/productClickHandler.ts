import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'

export function handleProductClick(e: MouseEvent): void {
  handleTableClick(e, {
    hrefContains: ['/admin/products/subtypes'],
  })

  handleTableClick(e, {
    hrefContains: ['/admin/variants'],
  })
}
