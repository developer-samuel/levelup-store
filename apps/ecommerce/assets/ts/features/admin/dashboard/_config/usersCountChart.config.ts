import type { ApexOptions } from 'apexcharts'

import { CHART_HEIGHTS } from '@/ts/features/admin/dashboard/constants'
import { calculateYAxis } from '@/ts/features/admin/dashboard/_utils/calculateYAxis'
import { getLastNDaysLabels } from '@/ts/features/admin/dashboard/_utils/dates'

export function getUsersCountChartOptions(usersLast7Days: number[] = []): ApexOptions {
  const last7DaysLabels = getLastNDaysLabels(7)
  const maxValue = Math.max(...usersLast7Days, 0)
  const { max, tickValues } = calculateYAxis(maxValue)

  return {
    chart: { type: 'bar', height: CHART_HEIGHTS.bar },
    series: [{ name: 'New Users', data: usersLast7Days }],
    xaxis: { categories: last7DaysLabels },
    yaxis: {
      min: 0,
      max,
      tickAmount: tickValues.length - 1,
      labels: {
        formatter: (val: number) => String(Math.floor(val)),
      },
    },
    title: { text: 'New Users (Last 7 Days)', align: 'left' },
  }
}
