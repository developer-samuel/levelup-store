import { logDevError } from '@/ts/shared/utils/logger'
import { query } from '@/ts/shared/utils/dom/query'

import { renderOrdersCountChart } from '@/ts/features/admin/dashboard/_charts/ordersCountChart'
import { renderOrdersRatioChart } from '@/ts/features/admin/dashboard/_charts/ordersRatioChart'
import { renderUsersCountChart } from '@/ts/features/admin/dashboard/_charts/usersCountChart'

export default class AdminDashboard {
  constructor() {
    const el = query<HTMLElement>('#admin-dashboard-page')
    if (!el) return

    try {
      const { ordersPerDay, ordersRatio, usersLast7Days } = this.parseChartData(el)

      this.init(ordersPerDay, ordersRatio, usersLast7Days)
    } catch (error) {
      logDevError('[Dashboard]', error)
    }
  }

  private parseChartData(el: HTMLElement): { ordersPerDay: number[]; ordersRatio: number[]; usersLast7Days: number[] } {
    return {
      ordersPerDay: this.parseNumberArray(el.dataset.orders),
      ordersRatio: this.parseNumberArray(el.dataset.ordersRatio),
      usersLast7Days: this.parseNumberArray(el.dataset.users),
    }
  }

  private parseNumberArray(raw: string | undefined): number[] {
    const parsed: unknown = JSON.parse(raw ?? '[]')

    return Array.isArray(parsed) ? parsed.filter((x): x is number => typeof x === 'number') : []
  }

  private init(ordersPerDay: number[], ordersRatio: number[], usersLast7Days: number[]): void {
    renderOrdersCountChart(query<HTMLElement>('#ordersCountChart'), ordersPerDay)
    renderOrdersRatioChart(query<HTMLElement>('#ordersRatioChart'), ordersRatio)
    renderUsersCountChart(query<HTMLElement>('#usersCountChart'), usersLast7Days)
  }
}
