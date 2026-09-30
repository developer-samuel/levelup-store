import type { ApexOptions } from 'apexcharts'

import { CHART_HEIGHTS, MARKERS, STROKE } from '@/ts/features/admin/dashboard/constants'
import { calculateYAxis } from '@/ts/features/admin/dashboard/_utils/calculateYAxis'
import { getCurrentMonthName } from '@/ts/features/admin/dashboard/_utils/dates'

export function getOrdersCountChartOptions(ordersPerDay: number[] = []): ApexOptions {
  const days = Array.from({ length: ordersPerDay.length }, (_, i) => i + 1)
  const maxValue = Math.max(...ordersPerDay, 0)
  const { max, tickValues } = calculateYAxis(maxValue, { allowDynamicScale: true })

  return {
    chart: {
      type: 'line',
      height: CHART_HEIGHTS.line,
    },
    series: [
      {
        name: 'Orders',
        data: ordersPerDay,
      },
    ],
    stroke: STROKE.smooth,
    markers: MARKERS.default,
    xaxis: {
      categories: days,
      title: { text: 'Day of Month' },
    },
    yaxis: {
      min: 0,
      max,
      tickAmount: tickValues.length - 1,
      labels: {
        formatter: (val: number) => String(Math.floor(val)),
      },
    },
    title: {
      text: `Total Orders (${getCurrentMonthName()})`,
      align: 'left',
    },
  }
}
