import { mockDatatablesConstants } from '@/tests/_support/mocks/plugins/datatables.mocks'

mockDatatablesConstants()

vi.mock('@/ts/plugins/datatables/_services/datatablesService', () => ({
  fetchDatatableData: vi.fn(),
}))

import { makeTable } from '@/tests/_support/fakers/dom.fakers'

import { fetchDatatableData } from '@/ts/plugins/datatables/_services/datatablesService'
import { loadAndRenderRows } from '@/ts/plugins/datatables/_interactions/loadAndRender'

const mockedFetchDatatableData = vi.mocked(fetchDatatableData)

describe('loadAndRenderRows()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when table is falsy', async () => {
    await loadAndRenderRows(null as unknown as HTMLTableElement, '/url', vi.fn())
    expect(mockedFetchDatatableData).not.toHaveBeenCalled()
  })

  it('should set LOADING_HTML in tbody before fetching', async () => {
    const table = makeTable()
    let loadingHtml = ''
    mockedFetchDatatableData.mockImplementationOnce(() => {
      loadingHtml = table.querySelector('tbody')?.innerHTML ?? ''
      return Promise.resolve([])
    })

    await loadAndRenderRows(table, '/url', vi.fn())

    expect(loadingHtml).toBe('<tr><td>Loading...</td></tr>')
  })

  it('should call fetchDatatableData with url', async () => {
    mockedFetchDatatableData.mockResolvedValueOnce([])
    await loadAndRenderRows(makeTable(), '/api/products', vi.fn())
    expect(mockedFetchDatatableData).toHaveBeenCalledWith('/api/products', {})
  })

  it('should call fetchDatatableData with dataKey when provided', async () => {
    mockedFetchDatatableData.mockResolvedValueOnce([])
    await loadAndRenderRows(makeTable(), '/api/products', vi.fn(), undefined, 'items')
    expect(mockedFetchDatatableData).toHaveBeenCalledWith('/api/products', { dataKey: 'items' })
  })

  it('should call renderRows with tbody and items', async () => {
    const items = [{ id: 1 }, { id: 2 }]
    mockedFetchDatatableData.mockResolvedValueOnce(items)
    const renderRows = vi.fn()
    const table = makeTable()

    await loadAndRenderRows(table, '/url', renderRows)

    expect(renderRows).toHaveBeenCalledWith(table.querySelector('tbody'), items)
  })

  it('should clear tbody and return when fetchDatatableData returns null', async () => {
    mockedFetchDatatableData.mockResolvedValueOnce(null as unknown as unknown[])
    const renderRows = vi.fn()
    const table = makeTable()

    await loadAndRenderRows(table, '/url', renderRows)

    expect(renderRows).not.toHaveBeenCalled()
    expect(table.querySelector('tbody')?.innerHTML).toBe('')
  })

  it('should attach clickHandler to tbody after rendering', async () => {
    const items = [{ id: 1 }]
    mockedFetchDatatableData.mockResolvedValueOnce(items)
    const clickHandler = vi.fn()
    const table = makeTable()

    await loadAndRenderRows(table, '/url', vi.fn(), clickHandler)

    const tbody = table.querySelector('tbody')
    if (!tbody) throw new Error('tbody not found')
    tbody.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(clickHandler).toHaveBeenCalledTimes(1)
  })

  it('should not attach clickHandler when not provided', async () => {
    mockedFetchDatatableData.mockResolvedValueOnce([])
    const table = makeTable()

    await expect(loadAndRenderRows(table, '/url', vi.fn())).resolves.toBeUndefined()
  })

  it('should create tbody when table has none', async () => {
    const table = makeTable(false)
    mockedFetchDatatableData.mockResolvedValueOnce([])

    await loadAndRenderRows(table, '/url', vi.fn())

    expect(table.querySelector('tbody')).not.toBeNull()
  })
})
