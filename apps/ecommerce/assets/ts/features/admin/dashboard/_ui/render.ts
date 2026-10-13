import type { ApexOptions } from 'apexcharts'

import { logDevError } from '@/ts/shared/utils/logger'

async function renderChart(element: HTMLElement | null, options: ApexOptions): Promise<void> {
  if (!element) return

  const { default: ApexCharts } = await import('apexcharts')
  
  void new ApexCharts(element, options).render()
}

export function renderChartWithLogging(element: HTMLElement | null, options: ApexOptions, logLabel: string): void {
  renderChart(element, options).catch((error) => logDevError(`[${logLabel}]`, error))
}
