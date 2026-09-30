import { queryAll } from '@/ts/shared/utils/dom/query'

export function resetFilterUI(initialMaxPrice: string): void {
  queryAll<HTMLInputElement>('input[name="brand[]"]').forEach((el) => { el.checked = false })
  queryAll<HTMLElement>('[data-subtype]').forEach((el) => el.classList.remove('products__filter-list-item--active'))

  const minPrice = document.getElementById('minPrice') as HTMLInputElement | null
  const maxPrice = document.getElementById('maxPrice') as HTMLInputElement | null

  if (minPrice) {
    minPrice.value = '0'
    minPrice.dispatchEvent(new Event('input', { bubbles: true }))
  }

  if (maxPrice) {
    maxPrice.value = initialMaxPrice
    maxPrice.dispatchEvent(new Event('input', { bubbles: true }))
  }
}
