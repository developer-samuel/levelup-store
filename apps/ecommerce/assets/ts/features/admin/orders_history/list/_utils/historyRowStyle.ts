import type { Order } from '@/ts/features/admin/orders_shared/types'

/**
 * Returns a background-color hex string for a history row based on its status.
 * Returns `undefined` when no special styling is needed.
 */
export function getHistoryRowStyle(row: Order): string | undefined {
  if (row.status?.toLowerCase() === 'refunded') {
    return '#FFF8B0'
  }

  return undefined
}
