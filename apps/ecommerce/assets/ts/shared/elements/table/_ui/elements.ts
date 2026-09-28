/** Creates a table cell with the given content */
export function createCell(content: string | HTMLElement = ''): HTMLTableCellElement {
  const td = document.createElement('td')

  if (content instanceof HTMLElement) {
    td.appendChild(content)
  } else {
    td.textContent = content
  }

  return td
}

/** Creates a table row (<tr>) with given cells and optional action element */
export function createRow(cells: HTMLTableCellElement[] = [], actions: HTMLElement | null = null): HTMLTableRowElement {
  const tr = document.createElement('tr')

  cells.forEach((td) => tr.appendChild(td))

  if (actions) tr.appendChild(actions)

  return tr
}

/** Renders a fallback row for empty tables with a centered message */
export function createEmptyRow(tbody: HTMLTableSectionElement, colCount: number, text = 'No records found'): void {
  const tr = document.createElement('tr')
  const td = document.createElement('td')

  td.colSpan = colCount
  td.textContent = text
  td.style.textAlign = 'center'
  td.style.fontSize = '20px'
  td.style.fontWeight = 'bold'

  tr.appendChild(td)
  tbody.appendChild(tr)
}

/** Removes the closest table row of the given element from the DOM */
export function removeRow(el: Element): void {
  const tr = el.closest('tr')

  if (tr) tr.remove()
}
