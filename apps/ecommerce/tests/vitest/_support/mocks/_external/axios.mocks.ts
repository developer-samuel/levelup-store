export function mockAxios(): void {
  vi.mock('axios', () => ({
    default: {
      get: vi.fn(),
      post: vi.fn(),
      isAxiosError: vi.fn(),
      isCancel: vi.fn(),
    },
  }))
}
