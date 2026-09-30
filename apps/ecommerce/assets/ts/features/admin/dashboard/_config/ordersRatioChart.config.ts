import type { ApexOptions } from 'apexcharts'

import { CHART_HEIGHTS, COLORS } from '@/ts/features/admin/dashboard/constants'
import { getCurrentMonthName } from '@/ts/features/admin/dashboard/_utils/dates'

export function getOrdersRatioChartOptions(ordersRatio: number[] = []): ApexOptions {
  return {
    chart: {
      type: 'donut',
      height: CHART_HEIGHTS.donut,
    },
    series: ordersRatio,
    labels: ['Paid', 'Unpaid'],
    colors: [...COLORS.ordersRatio],
    title: {
      text: `Paid Orders Ratio (${getCurrentMonthName()})`,
      align: 'left',
    },
  }
}
