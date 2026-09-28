export function mockProductsListUiVisibility(): void {
  vi.mock('@/ts/features/products/list/_ui/visibility', () => ({
    hide: vi.fn(),
    show: vi.fn(),
    toggle: vi.fn(),
    isVisible: vi.fn(),
  }))
}

export function mockProductsListFilter(): void {
  vi.mock('@/ts/features/products/list/_interactions/filter', () => ({
    bindFilter: vi.fn(),
  }))
}

export function mockProductsListUpdateProducts(): void {
  vi.mock('@/ts/features/products/list/_interactions/updateProducts', () => ({
    updateProducts: vi.fn(),
  }))
}
