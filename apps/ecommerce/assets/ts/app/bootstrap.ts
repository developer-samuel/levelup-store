import { refreshTokenGuard } from '@/ts/core/jwt/authGuard'

import { query } from '@/ts/shared/utils/dom/query'

import { modules } from '@/ts/app/imports/index'

await refreshTokenGuard()

/** Initialize modules on page if matching element exists */
modules.forEach(({ selector, module }) => {
  if (query(selector)) {
    void module()
  }
})
