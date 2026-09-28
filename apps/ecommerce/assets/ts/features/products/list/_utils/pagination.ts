export function parseInitialPage(
  wrapper: HTMLElement,
  optionValue: number | string | undefined,
  dataKey: string,
): number {
  const raw = optionValue ?? wrapper.dataset[dataKey]
  const value = parseInt(String(raw), 10)

  return Number.isFinite(value) && value > 0 ? value : 1
}

export function updateDataset(wrapper: HTMLElement, page: number, maxPages: number): void {
  wrapper.dataset.currentPage = String(page)
  wrapper.dataset.totalPage = String(maxPages)
}

/** Derives page / maxPages from server values, falling back to dataset values */
export function updateFromServer(
  wrapper: HTMLElement,
  currentPage: number | null,
  maxPages: number | null,
): { page: number; maxPages: number } {
  const page =
    currentPage !== null && Number.isFinite(currentPage) && currentPage > 0
      ? currentPage
      : parseInt(wrapper.dataset.currentPage ?? '1', 10)

  const total =
    maxPages !== null && Number.isFinite(maxPages) && maxPages > 0
      ? maxPages
      : parseInt(wrapper.dataset.totalPage ?? '1', 10)

  updateDataset(wrapper, page, total)

  return { page, maxPages: total }
}
