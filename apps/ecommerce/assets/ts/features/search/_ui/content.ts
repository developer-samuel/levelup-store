import { NO_RESULTS_HTML } from '@/ts/features/search/constants'

export function updateContent(content: HTMLElement, html: string | null | undefined): void {
  content.innerHTML = html ?? NO_RESULTS_HTML
}
