vi.mock('@/ts/features/cart/_ui/toggle', () => ({
  toggleCart: vi.fn(),
}))

vi.mock('@/ts/features/cart/_handlers/cartActionHandler', () => ({
  handleCartAction: vi.fn(),
}))

export function mockCartUiToggle(): void {}
export function mockCartActionHandler(): void {}
