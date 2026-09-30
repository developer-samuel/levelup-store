/** Limits the visible page numbers in a DataTable pagination to a window around the active page */
export function limitPaginationWindow(table: HTMLTableElement): void {
  const wrapper = table.closest('.dataTable-wrapper')
  if (!wrapper) return

  const items = Array.from(wrapper.querySelectorAll<HTMLLIElement>('.dataTable-pagination li')).filter(
    (li) => !li.classList.contains('pager'),
  )

  const activeLi = wrapper.querySelector<HTMLLIElement>('.dataTable-pagination li.active')
  if (!activeLi) return

  const currentIndex = items.indexOf(activeLi)

  items.forEach((li, idx) => {
    li.style.display = idx >= currentIndex - 1 && idx <= currentIndex + 1 ? 'inline-block' : 'none'
  })
}
