vi.mock('@/ts/shared/events/loading', () => ({
  dispatchLoadingShow: vi.fn(),
  dispatchLoadingHide: vi.fn(),
}))

export function mockSharedEventsLoading(): void {}
