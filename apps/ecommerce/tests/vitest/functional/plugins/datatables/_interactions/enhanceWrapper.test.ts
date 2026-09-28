import { enhanceWrapper } from '@/ts/plugins/datatables/_interactions/enhanceWrapper'

function makeTable(wrapperHtml: string): HTMLTableElement {
  const wrapper = document.createElement('div')

  wrapper.className = 'dataTable-wrapper'
  wrapper.innerHTML = wrapperHtml

  const table = document.createElement('table')

  wrapper.appendChild(table)
  document.body.appendChild(wrapper)

  return table
}

describe('enhanceWrapper()', () => {
  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should do nothing when table has no .dataTable-wrapper ancestor', () => {
    const table = document.createElement('table')
    expect(() => enhanceWrapper(table)).not.toThrow()
  })

  it('should set name on select when name is missing', () => {
    const table = makeTable('<select></select>')
    enhanceWrapper(table)
    expect(table.closest('.dataTable-wrapper')?.querySelector('select')?.name).toBe('datatable-perpage')
  })

  it('should not overwrite select name when already set', () => {
    const table = makeTable('<select name="existing"></select>')
    enhanceWrapper(table)
    expect(table.closest('.dataTable-wrapper')?.querySelector('select')?.name).toBe('existing')
  })

  it('should set name on input when name is missing', () => {
    const table = makeTable('<input />')
    enhanceWrapper(table)
    expect(table.closest('.dataTable-wrapper')?.querySelector('input')?.name).toBe('datatable-search')
  })

  it('should not overwrite input name when already set', () => {
    const table = makeTable('<input name="existing" />')
    enhanceWrapper(table)
    expect(table.closest('.dataTable-wrapper')?.querySelector('input')?.name).toBe('existing')
  })

  it('should not throw when wrapper has no select or input', () => {
    const table = makeTable('')
    expect(() => enhanceWrapper(table)).not.toThrow()
  })
})
