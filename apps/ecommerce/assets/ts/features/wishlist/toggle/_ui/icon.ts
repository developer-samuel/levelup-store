export function updateWishlist(el: HTMLElement, hover = false): void {
  const img = el.querySelector('img')
  if (!img) return

  const isActive = el.dataset.active === 'true'

  img.src = isActive ? '/img/icons/elements/heart/heart.png' : '/img/icons/elements/heart/heart-outline.png'

  if (isActive) {
    img.style.opacity = hover ? '0.8' : '1'
    img.style.transform = hover ? 'scale(1)' : 'scale(1.1)'
  } else {
    img.style.opacity = hover ? '1' : '0.5'
    img.style.transform = hover ? 'scale(1.1)' : 'scale(1)'
  }

  el.title = isActive ? 'Remove from wishlist' : 'Add to wishlist'
}
