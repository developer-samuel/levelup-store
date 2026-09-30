import { capitalize } from '@/ts/shared/utils/capitalize'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

import type { AdminUser } from '@/ts/features/admin/users/list/types'

const COLUMNS = ['name', 'email', 'role', 'emailVerifiedAt', 'createdAt'] as const

export function renderUsers(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const users = items as AdminUser[]
  renderDatatableRows(tbody, users, COLUMNS, {
    cellRenderers: {
      role: (val: unknown) => capitalize(String(val ?? '')),
      emailVerifiedAt: (val: unknown) => (val ? '✅' : '❌'),
    },
    emptyText: 'No users found',
  })
}
