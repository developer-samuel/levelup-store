export function parseProductWrapper(html: string): HTMLElement {
  const tempDiv = document.createElement('div')
  tempDiv.innerHTML = html

  return tempDiv.querySelector<HTMLElement>('.products__wrapper') ?? tempDiv
}
