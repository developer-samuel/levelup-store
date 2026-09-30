export function mockCartUiToggle(): void {
  vi.mock('@/ts/features/cart/_ui/toggle', () => ({
    toggleCart: vi.fn(),
  }))
}

export function mockCartActionHandler(): void {
  vi.mock('@/ts/features/cart/_handlers/cartActionHandler', () => ({
    handleCartAction: vi.fn(),
  }))
}
