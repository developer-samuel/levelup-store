export function checkLoadMoreVisibility(page: number, maxPages: number, root: ParentNode = document): void {
  const p = Number.isFinite(Number(page)) ? Number(page) : 1
  const max = Number.isFinite(Number(maxPages)) ? Number(maxPages) : 1

  const containers = Array.from(root.querySelectorAll<HTMLElement>('.products__card-load-more'))

  if (!containers.length) return

  if (p >= max) {
    containers.forEach((c) => c.classList.add('products__card-load-more--hidden'))
    return
  }

  containers.forEach((c, i) => {
    if (i === 0) c.classList.remove('products__card-load-more--hidden')
    else c.remove()
  })
}

export function normalizeLoadMore(wrapper: HTMLElement): void {
  const containers = Array.from(wrapper.querySelectorAll<HTMLElement>('.products__card-load-more'))

  if (containers.length <= 1) return

  containers.forEach((container, i) => {
    if (i === 0) {
      container.classList.remove('products__card-load-more--hidden')

      const buttons = container.querySelectorAll('#load-more')
      if (buttons.length > 1) {
        buttons.forEach((btn, bi) => {
          if (bi > 0) btn.remove()
        })
      }
    } else {
      container.remove()
    }
  })
}
