export type NavigationInstance = {
  navList: HTMLElement | null
  navMenu: HTMLElement | null
  mobileContainer: HTMLElement | null
  mobileIcon: HTMLImageElement | null
  resetMenu(): void
  activeMenu: HTMLElement | boolean | null
  idFromUrl: string | null
  currentHoveredItem: string | null
  mutationObserver: MutationObserver | null
  resizeObserver: ResizeObserver | null
}
