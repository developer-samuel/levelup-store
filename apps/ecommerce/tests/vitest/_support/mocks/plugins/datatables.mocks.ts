export function mockDatatablesConstants(): void {
  vi.mock('@/ts/plugins/datatables/constants', () => ({
    LOADING_HTML: '<tr><td>Loading...</td></tr>',
  }))
}

export function mockDatatablesUiFallbacks(): void {
  vi.mock('@/ts/plugins/datatables/_ui/fallbacks', () => ({
    showTableFallback: vi.fn(),
    showNoRecordsFallback: vi.fn(),
  }))
}

export function mockDatatablesUiPagination(): void {
  vi.mock('@/ts/plugins/datatables/_ui/pagination', () => ({
    limitPaginationWindow: vi.fn(),
  }))
}
