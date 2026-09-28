import { LOADING_HTML } from '@/ts/plugins/datatables/constants'

/** Shows a neutral loading message in the table body if DataTable fails to initialise */
export function showTableFallback(table: HTMLTableElement): void {
  const tbody = table.querySelector('tbody')
  if (tbody) tbody.innerHTML = LOADING_HTML
}

/** Shows a "No records found" message in the table body */
export function showNoRecordsFallback(table: HTMLTableElement): void {
  const tbody = table.querySelector('tbody')
  if (!tbody) return

  const thCount = table.querySelectorAll('thead th').length || 1
  tbody.innerHTML = `<tr><td colspan="${thCount}">No records found</td></tr>`
}
