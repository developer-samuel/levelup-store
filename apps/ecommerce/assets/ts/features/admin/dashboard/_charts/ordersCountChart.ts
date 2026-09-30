import { getOrdersCountChartOptions } from '@/ts/features/admin/dashboard/_config/ordersCountChart.config'
import { renderChartWithLogging } from '@/ts/features/admin/dashboard/_ui/render'

export function renderOrdersCountChart(element: HTMLElement | null, ordersPerDay: number[] = []): void {
  const options = getOrdersCountChartOptions(ordersPerDay)

  renderChartWithLogging(element, options, 'OrdersCountChart')
}
