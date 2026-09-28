export function resolveNavId(pathname = window.location.pathname): string | null {
  if (pathname.startsWith('/discounts')) {
    return 'discount-item'
  }

  if (pathname.startsWith('/products')) {
    const [, category] = pathname.split('/').filter(Boolean)
    return category ? `${category}-item` : null
  }

  return null
}
