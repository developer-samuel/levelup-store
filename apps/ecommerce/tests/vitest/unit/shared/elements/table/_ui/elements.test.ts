import { createCell, createRow, createEmptyRow, removeRow } from '@/ts/shared/elements/table/_ui/elements'

describe('createCell()', () => {
  it('should return a td element', () => {
    expect(createCell('text').tagName).toBe('TD')
  })

  it('should set textContent for string content', () => {
    const td = createCell('Hello')
    expect(td.textContent).toBe('Hello')
  })

  it('should append HTMLElement as child', () => {
    const span = document.createElement('span')
    const td = createCell(span)
    expect(td.firstElementChild).toBe(span)
  })

  it('should use empty string as default content', () => {
    const td = createCell()
    expect(td.textContent).toBe('')
  })
})

describe('createRow()', () => {
  it('should return a tr element', () => {
    expect(createRow().tagName).toBe('TR')
  })

  it('should append all provided cells', () => {
    const cells = [createCell('A'), createCell('B'), createCell('C')]
    const tr = createRow(cells)
    expect(tr.querySelectorAll('td')).toHaveLength(3)
  })

  it('should append actions element when provided', () => {
    const actions = document.createElement('td')
    actions.className = 'actions'
    const tr = createRow([], actions)
    expect(tr.querySelector('.actions')).toBe(actions)
  })

  it('should not append actions when null', () => {
    const tr = createRow([createCell('A')], null)
    expect(tr.querySelectorAll('td')).toHaveLength(1)
  })

  it('should return empty tr when no args provided', () => {
    const tr = createRow()
    expect(tr.children).toHaveLength(0)
  })
})

describe('createEmptyRow()', () => {
  it('should append a tr to tbody', () => {
    const tbody = document.createElement('tbody')
    createEmptyRow(tbody, 3)
    expect(tbody.querySelectorAll('tr')).toHaveLength(1)
  })

  it('should set colSpan on the td', () => {
    const tbody = document.createElement('tbody')
    createEmptyRow(tbody, 5)
    const td = tbody.querySelector('td')
    expect(td?.colSpan).toBe(5)
  })

  it('should use default text "No records found"', () => {
    const tbody = document.createElement('tbody')
    createEmptyRow(tbody, 3)
    expect(tbody.querySelector('td')?.textContent).toBe('No records found')
  })

  it('should use provided text', () => {
    const tbody = document.createElement('tbody')
    createEmptyRow(tbody, 3, 'Empty list')
    expect(tbody.querySelector('td')?.textContent).toBe('Empty list')
  })

  it('should center align the td', () => {
    const tbody = document.createElement('tbody')
    createEmptyRow(tbody, 3)
    expect(tbody.querySelector('td')?.style.textAlign).toBe('center')
  })
})

describe('removeRow()', () => {
  it('should remove the closest tr from DOM', () => {
    const table = document.createElement('table')
    const tbody = document.createElement('tbody')
    const tr = document.createElement('tr')
    const td = document.createElement('td')
    tbody.appendChild(tr)
    tr.appendChild(td)
    table.appendChild(tbody)
    document.body.appendChild(table)

    removeRow(td)

    expect(tbody.querySelectorAll('tr')).toHaveLength(0)

    document.body.innerHTML = ''
  })

  it('should do nothing when element has no parent tr', () => {
    const div = document.createElement('div')
    expect(() => removeRow(div)).not.toThrow()
  })
})
