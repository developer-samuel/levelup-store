import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { renderUsers } from '@/ts/features/admin/users/list/_ui/render'
import { handleUserClick } from '@/ts/features/admin/users/list/_handlers/userClickHandler'

const TABLE_SELECTOR = '#admin-users-table'
const DATA_KEY = 'users'
const URL = '/api/admin/users/list'

export default class AdminUsersTable extends BaseDatatable {
  constructor() {
    super(TABLE_SELECTOR, URL, renderUsers as RenderRowsFn, handleUserClick, DATA_KEY)
  }
}
