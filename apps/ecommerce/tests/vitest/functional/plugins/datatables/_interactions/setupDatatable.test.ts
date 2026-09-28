import { mockUtilsLogger } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockDatatablesUiFallbacks, mockDatatablesConstants } from '@/tests/_support/mocks/plugins/datatables.mocks'

mockUtilsLogger()
mockDatatablesUiFallbacks()
mockDatatablesConstants()

vi.mock('vanilla-datatables', () => ({
  default: vi.fn().mockImplementation(function () {
    return { instance: true }
  }),
}))

vi.mock('vanilla-datatables/dist/vanilla-dataTables.min.css', () => ({}))

vi.mock('@/ts/plugins/datatables/config', () => ({
  DATATABLES_CONFIG: {},
}))

vi.mock('@/ts/plugins/datatables/_interactions/loadAndRender', () => ({
  loadAndRenderRows: vi.fn(),
}))

vi.mock('@/ts/plugins/datatables/_interactions/enhanceWrapper', () => ({
  enhanceWrapper: vi.fn(),
}))

import { makeTable } from '@/tests/_support/fakers/dom.fakers'

import DataTable from 'vanilla-datatables'

import { logDevError } from '@/ts/shared/utils/logger'

import { showNoRecordsFallback } from '@/ts/plugins/datatables/_ui/fallbacks'
import { setupDatatable } from '@/ts/plugins/datatables/_interactions/setupDatatable'
import { loadAndRenderRows } from '@/ts/plugins/datatables/_interactions/loadAndRender'
import { enhanceWrapper } from '@/ts/plugins/datatables/_interactions/enhanceWrapper'

const mockedDataTable = vi.mocked(DataTable)
const mockedLoadAndRenderRows = vi.mocked(loadAndRenderRows)
const mockedEnhanceWrapper = vi.mocked(enhanceWrapper)
const mockedShowNoRecordsFallback = vi.mocked(showNoRecordsFallback)
const mockedLogDevError = vi.mocked(logDevError)

describe('setupDatatable()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockedLoadAndRenderRows.mockResolvedValue(undefined)
  })

  it('should return null when table is falsy', async () => {
    const result = await setupDatatable(null as unknown as HTMLTableElement, '/url', vi.fn())
    expect(result).toBeNull()
  })

  it('should set LOADING_HTML in tbody on init', async () => {
    const table = makeTable()
    let html = ''
    mockedLoadAndRenderRows.mockImplementationOnce(() => {
      html = table.querySelector('tbody')?.innerHTML ?? ''
      return Promise.resolve()
    })

    await setupDatatable(table, '/url', vi.fn())

    expect(html).toBe('<tr><td>Loading...</td></tr>')
  })

  it('should call loadAndRenderRows with correct args', async () => {
    const table = makeTable()
    const renderRows = vi.fn()
    const clickHandler = vi.fn()

    await setupDatatable(table, '/api/products', renderRows, clickHandler, 'items')

    expect(mockedLoadAndRenderRows).toHaveBeenCalledWith(table, '/api/products', renderRows, clickHandler, 'items')
  })

  it('should instantiate DataTable after loading rows', async () => {
    const table = makeTable()
    await setupDatatable(table, '/url', vi.fn())
    expect(mockedDataTable).toHaveBeenCalledWith(table, {})
  })

  it('should call enhanceWrapper after instantiating DataTable', async () => {
    const table = makeTable()
    await setupDatatable(table, '/url', vi.fn())
    expect(mockedEnhanceWrapper).toHaveBeenCalledWith(table)
  })

  it('should return DataTable instance on success', async () => {
    const table = makeTable()
    const result = await setupDatatable(table, '/url', vi.fn())
    expect(result).toEqual({ instance: true })
  })

  it('should return null and show fallback on error', async () => {
    const table = makeTable()
    mockedLoadAndRenderRows.mockRejectedValueOnce(new Error('Network error'))

    const result = await setupDatatable(table, '/url', vi.fn())

    expect(result).toBeNull()
    expect(mockedShowNoRecordsFallback).toHaveBeenCalledWith(table)
  })

  it('should log error when exception occurs', async () => {
    const table = makeTable()
    const error = new Error('Network error')
    mockedLoadAndRenderRows.mockRejectedValueOnce(error)

    await setupDatatable(table, '/url', vi.fn())

    expect(mockedLogDevError).toHaveBeenCalledWith('[Datatable]', error)
  })

  it('should create tbody when table has none', async () => {
    const table = makeTable(false)

    await setupDatatable(table, '/url', vi.fn())

    expect(table.querySelector('tbody')).not.toBeNull()
  })
})
