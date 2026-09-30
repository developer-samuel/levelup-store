import { mockDatatablesUiFallbacks, mockDatatablesUiPagination } from '@/tests/_support/mocks/plugins/datatables.mocks'

mockDatatablesUiFallbacks()
mockDatatablesUiPagination()

vi.mock('@/ts/plugins/datatables/_observers/paginationObserver', () => ({
  observeDatatablePagination: vi.fn(),
}))

import { showTableFallback } from '@/ts/plugins/datatables/_ui/fallbacks'
import { limitPaginationWindow } from '@/ts/plugins/datatables/_ui/pagination'
import { observeDatatablePagination } from '@/ts/plugins/datatables/_observers/paginationObserver'
import { applyDatatableEnhancements } from '@/ts/plugins/datatables/_interactions/applyEnhancements'

const mockedShowTableFallback = vi.mocked(showTableFallback)
const mockedLimitPaginationWindow = vi.mocked(limitPaginationWindow)
const mockedObserveDatatablePagination = vi.mocked(observeDatatablePagination)

describe('applyDatatableEnhancements()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when table is falsy', () => {
    applyDatatableEnhancements(null as unknown as HTMLTableElement)
    expect(mockedLimitPaginationWindow).not.toHaveBeenCalled()
    expect(mockedObserveDatatablePagination).not.toHaveBeenCalled()
  })

  it('should call limitPaginationWindow with table', () => {
    const table = document.createElement('table')
    applyDatatableEnhancements(table)
    expect(mockedLimitPaginationWindow).toHaveBeenCalledWith(table)
  })

  it('should call observeDatatablePagination with table', () => {
    const table = document.createElement('table')
    applyDatatableEnhancements(table)
    expect(mockedObserveDatatablePagination).toHaveBeenCalledWith(table)
  })

  it('should call showTableFallback when limitPaginationWindow throws', () => {
    const table = document.createElement('table')
    mockedLimitPaginationWindow.mockImplementationOnce(() => {
      throw new Error('fail')
    })

    applyDatatableEnhancements(table)

    expect(mockedShowTableFallback).toHaveBeenCalledWith(table)
  })

  it('should call showTableFallback when observeDatatablePagination throws', () => {
    const table = document.createElement('table')
    mockedObserveDatatablePagination.mockImplementationOnce(() => {
      throw new Error('fail')
    })

    applyDatatableEnhancements(table)

    expect(mockedShowTableFallback).toHaveBeenCalledWith(table)
  })

  it('should not call showTableFallback when no error occurs', () => {
    const table = document.createElement('table')
    applyDatatableEnhancements(table)
    expect(mockedShowTableFallback).not.toHaveBeenCalled()
  })
})
