export function updateWishlistWrapper(itemElement: Element | null): void {
  if (!itemElement) return

  const wishlistContainer = itemElement.closest('.products__card-list')
  if (!wishlistContainer) return

  const wrapper = wishlistContainer.parentElement
  itemElement.remove()

  if (wrapper && !wrapper.querySelector('.product-item')) {
    wrapper.innerHTML = `
      <div class="products__card-no-results">
        <p>No records found.</p>
      </div>
    `
  }
}
