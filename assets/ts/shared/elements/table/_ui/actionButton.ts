type ActionButtonOptions = {
  className: string
  text: string
  id?: string | number
}

type ActionButtonDef = {
  className: string
  text: string
  href: string
  attrs: Record<string, string>
}

/** Creates a table cell containing a single action link */
export function createActionButton({ className, text, id }: ActionButtonOptions): HTMLTableCellElement {
  const a = document.createElement('a')
  a.className = className
  a.textContent = text
  a.dataset.id = id !== undefined ? String(id) : ''
  a.href = ''

  const td = document.createElement('td')
  td.appendChild(a)

  return td
}

/** Renders a list of action buttons into a td element */
export function renderActionButtons(td: HTMLTableCellElement, buttons: ActionButtonDef[], rowId: number): void {
  buttons.forEach((btn) => {
    const el = createActionButton({ className: btn.className, text: btn.text, id: rowId })
    const a = el.querySelector('a')
    if (a) {
      a.href = btn.href
      Object.entries(btn.attrs).forEach(([k, v]) => a.setAttribute(k, v))
      td.appendChild(a)
    }
  })
}
