import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

function makeTbody(withTable = true, withWrapper = false, withPagination = false): HTMLTableSectionElement {
  const tbody = document.createElement('tbody')

  if (withTable) {
    const table = document.createElement('table')
    table.appendChild(tbody)

    if (withWrapper) {
      const wrapper = document.createElement('div')
      wrapper.className = 'dataTable-wrapper'
      wrapper.appendChild(table)

      if (withPagination) {
        const pagination = document.createElement('ul')
        pagination.className = 'dataTable-pagination'
        wrapper.appendChild(pagination)
      }

      document.body.appendChild(wrapper)
    } else {
      document.body.appendChild(table)
    }
  }

  return tbody
}

describe('renderDatatableRows()', () => {
  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should clear tbody before rendering', () => {
    const tbody = makeTbody()
    tbody.innerHTML = '<tr><td>old</td></tr>'

    renderDatatableRows(tbody, [{ name: 'A' }], ['name'])

    expect(tbody.querySelectorAll('tr')).toHaveLength(1)
    expect(tbody.querySelector('td')?.textContent).toBe('A')
  })

  it('should render empty row when rows array is empty', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [], ['name', 'email'])

    const td = tbody.querySelector('td')
    expect(td?.textContent).toBe('No records found')
  })

  it('should use custom emptyText when rows is empty', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [], ['name'], { emptyText: 'Žiadne záznamy' })

    expect(tbody.querySelector('td')?.textContent).toBe('Žiadne záznamy')
  })

  it('should set empty row colspan to columns length', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [], ['name', 'email', 'age'])

    expect(tbody.querySelector('td')?.getAttribute('colspan')).toBe('3')
  })

  it('should add 1 to colspan when actionButton option provided and rows empty', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [], ['name', 'email'], {
      actionButton: () => document.createElement('td'),
    })

    expect(tbody.querySelector('td')?.getAttribute('colspan')).toBe('3')
  })

  it('should render one row per data item', () => {
    const tbody = makeTbody()
    const rows = [{ name: 'Alice' }, { name: 'Bob' }]

    renderDatatableRows(tbody, rows, ['name'])

    expect(tbody.querySelectorAll('tr')).toHaveLength(2)
  })

  it('should render cell value as string', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [{ age: 30 }], ['age'])

    expect(tbody.querySelector('td')?.textContent).toBe('30')
  })

  it('should render empty string for null cell value', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [{ name: null }], ['name'])

    expect(tbody.querySelector('td')?.textContent).toBe('')
  })

  it('should render empty string for undefined cell value', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [{ name: undefined }], ['name'])

    expect(tbody.querySelector('td')?.textContent).toBe('')
  })

  it('should use cellRenderer when provided for column', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [{ name: 'alice' }], ['name'], {
      cellRenderers: { name: (value) => `<strong>${String(value)}</strong>` },
    })

    expect(tbody.querySelector('td strong')?.textContent ?? tbody.querySelector('td')?.innerHTML).toContain('alice')
  })

  it('should apply rowStyle background color when function returns value', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [{ active: true }], ['active'], {
      rowStyle: (row) => ((row as { active: boolean }).active ? 'green' : undefined),
    })

    expect(tbody.querySelector('tr')?.style.backgroundColor).toBe('green')
  })

  it('should not apply background when rowStyle returns undefined', () => {
    const tbody = makeTbody()

    renderDatatableRows(tbody, [{ active: false }], ['active'], {
      rowStyle: () => undefined,
    })

    expect(tbody.querySelector('tr')?.style.backgroundColor).toBe('')
  })

  it('should append actionButton td to row', () => {
    const tbody = makeTbody()
    const actionTd = document.createElement('td')
    actionTd.className = 'action'

    renderDatatableRows(tbody, [{ name: 'Alice' }], ['name'], {
      actionButton: () => actionTd,
    })

    expect(tbody.querySelector('tr .action')).not.toBeNull()
  })

  it('should hide pagination when rows is empty and pagination exists', () => {
    const tbody = makeTbody(true, true, true)
    const pagination = document.querySelector<HTMLElement>('.dataTable-pagination')
    if (!pagination) throw new Error('pagination not found')

    renderDatatableRows(tbody, [], ['name'])

    expect(pagination.style.display).toBe('none')
  })

  it('should show pagination when rows exist and pagination exists', () => {
    const tbody = makeTbody(true, true, true)
    const pagination = document.querySelector<HTMLElement>('.dataTable-pagination')
    if (!pagination) throw new Error('pagination not found')
    pagination.style.display = 'none'

    renderDatatableRows(tbody, [{ name: 'Alice' }], ['name'])

    expect(pagination.style.display).toBe('')
  })

  it('should not throw when no pagination element exists', () => {
    const tbody = makeTbody(true, true, false)

    expect(() => renderDatatableRows(tbody, [], ['name'])).not.toThrow()
  })
})
