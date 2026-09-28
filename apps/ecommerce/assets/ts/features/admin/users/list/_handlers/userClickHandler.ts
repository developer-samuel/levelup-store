import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'

export function handleUserClick(e: MouseEvent): void {
  handleTableClick(e, {
    hrefContains: ['/admin/users/show'],
  })
}
