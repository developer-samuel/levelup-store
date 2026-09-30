import { getUsersCountChartOptions } from '@/ts/features/admin/dashboard/_config/usersCountChart.config'
import { renderChartWithLogging } from '@/ts/features/admin/dashboard/_ui/render'

export function renderUsersCountChart(element: HTMLElement | null, usersLast7Days: number[] = []): void {
  const options = getUsersCountChartOptions(usersLast7Days)

  renderChartWithLogging(element, options, 'UsersCountChart')
}
