export function mockUtilsLogger(): void {
  vi.mock('@/ts/shared/utils/logger', () => ({
    logDevError: vi.fn(),
  }))
}

export function mockUtilsDebouce(): void {
  vi.mock('@/ts/shared/utils/debounce', () => ({
    default: (fn: (...args: unknown[]) => unknown): ((...args: unknown[]) => unknown) => fn,
  }))
}

export function mockUtilsQuery(): void {
  vi.mock('@/ts/shared/utils/query', () => ({
    parseQueryParams: vi.fn(),
    buildQueryString: vi.fn(),
  }))
}

export function mockUtilsScroll(): void {
  vi.mock('@/ts/shared/utils/scroll', () => ({
    scrollToTop: vi.fn(),
    scrollToContainer: vi.fn(),
  }))
}

export function mockUtilsDomQuery(): void {
  vi.mock('@/ts/shared/utils/dom/query', () => ({
    query: vi.fn(),
    queryAll: vi.fn(),
  }))
}
