export function mockNotyfAlert(): void {
  vi.mock('@/ts/plugins/notyf/_components/NotyfAlert', () => ({
    default: { success: vi.fn(), error: vi.fn() },
  }))
}
