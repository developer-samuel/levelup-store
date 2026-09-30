import ApexCharts, { type ApexOptions } from 'apexcharts'

import { logDevError } from '@/ts/shared/utils/logger'

function renderChart(element: HTMLElement | null, options: ApexOptions): void {
  if (!element) return

  void new ApexCharts(element, options).render()
}

export function renderChartWithLogging(element: HTMLElement | null, options: ApexOptions, logLabel: string): void {
  try {
    renderChart(element, options)
  } catch (error) {
    logDevError(`[${logLabel}]`, error)
  }
}
