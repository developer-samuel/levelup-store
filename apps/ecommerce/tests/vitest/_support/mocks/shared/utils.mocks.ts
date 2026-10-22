vi.mock('@/ts/shared/utils/logger', () => ({
  logDevError: vi.fn(),
}))

vi.mock('@/ts/shared/utils/debounce', () => ({
  default: (fn: (...args: unknown[]) => unknown): ((...args: unknown[]) => unknown) => fn,
}))

vi.mock('@/ts/shared/utils/query', () => ({
  parseQueryParams: vi.fn(),
  buildQueryString: vi.fn(),
}))

vi.mock('@/ts/shared/utils/scroll', () => ({
  scrollToTop: vi.fn(),
  scrollToContainer: vi.fn(),
}))

vi.mock('@/ts/shared/utils/dom/query', () => ({
  query: vi.fn(),
  queryAll: vi.fn(),
}))

export function mockUtilsLogger(): void {}
export function mockUtilsDebouce(): void {}
export function mockUtilsQuery(): void {}
export function mockUtilsScroll(): void {}
export function mockUtilsDomQuery(): void {}
