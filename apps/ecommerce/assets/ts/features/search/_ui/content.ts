import { NO_RESULTS_HTML } from '@/ts/features/search/constants'

export function updateContent(content: HTMLElement, html: string | null | undefined): void {
  content.innerHTML = html ?? NO_RESULTS_HTML
}

export function appendContent(content: HTMLElement, html: string): void {
  const temp = document.createElement('div')
  temp.innerHTML = html
  
  while (temp.firstChild) {
    content.appendChild(temp.firstChild)
  }
}
