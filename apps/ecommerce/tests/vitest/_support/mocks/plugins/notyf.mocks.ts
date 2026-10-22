vi.mock('@/ts/plugins/notyf/_components/NotyfAlert', () => ({
  default: { success: vi.fn(), error: vi.fn() },
}))

export function mockNotyfAlert(): void {}
