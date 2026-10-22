vi.mock('@/ts/plugins/datatables/constants', () => ({
  LOADING_HTML: '<tr><td>Loading...</td></tr>',
}))

vi.mock('@/ts/plugins/datatables/_ui/fallbacks', () => ({
  showTableFallback: vi.fn(),
  showNoRecordsFallback: vi.fn(),
}))

vi.mock('@/ts/plugins/datatables/_ui/pagination', () => ({
  limitPaginationWindow: vi.fn(),
}))

export function mockDatatablesConstants(): void {}
export function mockDatatablesUiFallbacks(): void {}
export function mockDatatablesUiPagination(): void {}
