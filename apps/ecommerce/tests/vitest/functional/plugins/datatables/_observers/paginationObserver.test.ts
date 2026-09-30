import { mockDatatablesUiPagination } from '@/tests/_support/mocks/plugins/datatables.mocks'

mockDatatablesUiPagination()

import { limitPaginationWindow } from '@/ts/plugins/datatables/_ui/pagination'
import { observeDatatablePagination } from '@/ts/plugins/datatables/_observers/paginationObserver'

const mockedLimitPaginationWindow = vi.mocked(limitPaginationWindow)

function makeTableWithWrapper(hasPagination = true): {
  table: HTMLTableElement
  wrapper: HTMLElement
  pagination: HTMLElement | null
} {
  const wrapper = document.createElement('div')
  wrapper.className = 'dataTable-wrapper'

  const table = document.createElement('table')
  wrapper.appendChild(table)

  let pagination: HTMLElement | null = null
  if (hasPagination) {
    pagination = document.createElement('ul')
    pagination.className = 'dataTable-pagination'
    wrapper.appendChild(pagination)
  }

  document.body.appendChild(wrapper)

  return { table, wrapper, pagination }
}

describe('observeDatatablePagination()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
  })

  it('should do nothing when table has no .dataTable-wrapper ancestor', () => {
    const table = document.createElement('table')
    expect(() => observeDatatablePagination(table)).not.toThrow()
    expect(mockedLimitPaginationWindow).not.toHaveBeenCalled()
  })

  it('should do nothing when wrapper has no .dataTable-pagination', () => {
    const { table } = makeTableWithWrapper(false)
    expect(() => observeDatatablePagination(table)).not.toThrow()
    expect(mockedLimitPaginationWindow).not.toHaveBeenCalled()
  })

  it('should call limitPaginationWindow when pagination DOM changes', async () => {
    const { table, pagination } = makeTableWithWrapper()
    if (!pagination) throw new Error('pagination not found')

    observeDatatablePagination(table)

    const li = document.createElement('li')
    pagination.appendChild(li)

    await vi.waitFor(() => {
      expect(mockedLimitPaginationWindow).toHaveBeenCalledWith(table)
    })
  })

  it('should call limitPaginationWindow on each subsequent mutation', async () => {
    const { table, pagination } = makeTableWithWrapper()
    if (!pagination) throw new Error('pagination not found')

    observeDatatablePagination(table)

    pagination.appendChild(document.createElement('li'))
    await vi.waitFor(() => expect(mockedLimitPaginationWindow).toHaveBeenCalledTimes(1))

    pagination.appendChild(document.createElement('li'))
    await vi.waitFor(() => expect(mockedLimitPaginationWindow).toHaveBeenCalledTimes(2))
  })

  it('should observe with childList and subtree options', () => {
    const observeSpy = vi.spyOn(MutationObserver.prototype, 'observe')
    const { table, pagination } = makeTableWithWrapper()

    observeDatatablePagination(table)

    expect(observeSpy).toHaveBeenCalledWith(pagination, { childList: true, subtree: true })
  })
})
