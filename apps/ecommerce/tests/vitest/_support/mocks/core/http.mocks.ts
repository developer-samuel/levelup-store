vi.mock('@/ts/core/http/_services/submitFormData', () => ({
  submitFormData: vi.fn(),
}))

vi.mock('@/ts/core/http/api', () => ({
  default: {
    get: vi.fn(),
    post: vi.fn(),
  },
}))

export function mockHttpSubmitFormData(): void {}
export function mockHttpApi(): void {}
