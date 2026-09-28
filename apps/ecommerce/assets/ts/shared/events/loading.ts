export const LOADING_SHOW = 'loading:show'
export const LOADING_HIDE = 'loading:hide'

export function dispatchLoadingShow(): void {
  document.dispatchEvent(new CustomEvent(LOADING_SHOW))
}

export function dispatchLoadingHide(): void {
  document.dispatchEvent(new CustomEvent(LOADING_HIDE))
}
