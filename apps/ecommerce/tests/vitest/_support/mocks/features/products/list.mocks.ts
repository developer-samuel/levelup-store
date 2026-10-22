vi.mock('@/ts/features/products/list/_ui/visibility', () => ({
  hide: vi.fn(),
  show: vi.fn(),
  toggle: vi.fn(),
  isVisible: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_interactions/filter', () => ({
  bindFilter: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_interactions/updateProducts', () => ({
  updateProducts: vi.fn(),
}))

export function mockProductsListUiVisibility(): void {}
export function mockProductsListFilter(): void {}
export function mockProductsListUpdateProducts(): void {}
