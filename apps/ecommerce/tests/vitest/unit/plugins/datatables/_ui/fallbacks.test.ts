import { LOADING_HTML } from '@/ts/plugins/datatables/constants'
import { showTableFallback, showNoRecordsFallback } from '@/ts/plugins/datatables/_ui/fallbacks'

function makeTable(theadThCount = 0, hasTbody = true): HTMLTableElement {
  const table = document.createElement('table')

  if (theadThCount > 0) {
    const thead = document.createElement('thead')
    const tr = document.createElement('tr')
    for (let i = 0; i < theadThCount; i++) {
      tr.appendChild(document.createElement('th'))
    }
    thead.appendChild(tr)
    table.appendChild(thead)
  }

  if (hasTbody) table.createTBody()

  return table
}

describe('showTableFallback()', () => {
  it('should set LOADING_HTML in tbody', () => {
    const table = makeTable()
    showTableFallback(table)
    expect(table.querySelector('tbody')?.innerHTML).toBe(LOADING_HTML)
  })

  it('should do nothing when table has no tbody', () => {
    const table = makeTable(0, false)
    expect(() => showTableFallback(table)).not.toThrow()
  })
})

describe('showNoRecordsFallback()', () => {
  it('should do nothing when table has no tbody', () => {
    const table = makeTable(0, false)
    expect(() => showNoRecordsFallback(table)).not.toThrow()
  })

  it('should render "No records found" in tbody', () => {
    const table = makeTable()
    showNoRecordsFallback(table)
    expect(table.querySelector('tbody')?.textContent).toContain('No records found')
  })

  it('should set colspan to number of thead th elements', () => {
    const table = makeTable(4)
    showNoRecordsFallback(table)
    const td = table.querySelector('tbody td')
    expect(td?.getAttribute('colspan')).toBe('4')
  })

  it('should fall back to colspan 1 when no thead th elements exist', () => {
    const table = makeTable(0)
    showNoRecordsFallback(table)
    const td = table.querySelector('tbody td')
    expect(td?.getAttribute('colspan')).toBe('1')
  })
})
