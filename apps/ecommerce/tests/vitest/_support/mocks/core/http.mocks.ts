export function mockHttpSubmitFormData(): void {
  vi.mock('@/ts/core/http/_services/submitFormData', () => ({
    submitFormData: vi.fn(),
  }))
}

export function mockHttpApi(): void {
  vi.mock('@/ts/core/http/api', () => ({
    default: {
      get: vi.fn(),
      post: vi.fn(),
    },
  }))
}
