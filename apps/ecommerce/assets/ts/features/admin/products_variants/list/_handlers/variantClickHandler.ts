import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'

export function handleVariantClick(e: MouseEvent): void {
  handleTableClick(e, {
    hrefContains: ['/admin/variants/eans'],
  })

  handleTableClick(e, {
    hrefContains: ['/admin/variants/images'],
  })

  handleTableClick(e, {
    hrefContains: ['/admin/variants/descriptions'],
  })
}
