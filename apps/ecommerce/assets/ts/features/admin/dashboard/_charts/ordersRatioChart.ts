import { getOrdersRatioChartOptions } from '@/ts/features/admin/dashboard/_config/ordersRatioChart.config'
import { renderChartWithLogging } from '@/ts/features/admin/dashboard/_ui/render'

export function renderOrdersRatioChart(element: HTMLElement | null, ordersRatio: number[] = []): void {
  const options = getOrdersRatioChartOptions(ordersRatio)

  renderChartWithLogging(element, options, 'OrdersRatioChart')
}
