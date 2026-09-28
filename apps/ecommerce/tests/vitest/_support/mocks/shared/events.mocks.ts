export function mockSharedEventsLoading(): void {
  vi.mock('@/ts/shared/events/loading', () => ({
    dispatchLoadingShow: vi.fn(),
    dispatchLoadingHide: vi.fn(),
  }))
}
