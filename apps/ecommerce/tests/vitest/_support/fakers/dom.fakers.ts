export function makeTable(hasTbody = true): HTMLTableElement {
  const table = document.createElement('table')
  if (hasTbody) table.createTBody()

  return table
}

export function makeInput(value: string, type = 'text'): HTMLInputElement {
  const input = document.createElement('input')

  input.type = type
  input.value = value

  return input
}

export function makeCheckbox(value = '', checked = false): HTMLInputElement {
  const input = makeInput(value, 'checkbox')

  input.checked = checked

  return input
}
