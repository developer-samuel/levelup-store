/** Ensures DataTable wrapper has proper form control names for per-page select and search input */
export function enhanceWrapper(table: HTMLTableElement): void {
  const wrapper = table.closest('.dataTable-wrapper')
  if (!wrapper) return

  const select = wrapper.querySelector('select')
  if (select && !select.name) select.name = 'datatable-perpage'

  const input = wrapper.querySelector('input')
  if (input && !input.name) input.name = 'datatable-search'
}
