export function getZoomModalElements(): {
  modal: HTMLElement | null
  img: HTMLImageElement | null
  close: HTMLElement | null
} {
  return {
    modal: document.getElementById('gallery-zoom-modal'),
    img: document.getElementById('gallery-zoom-modal-img') as HTMLImageElement | null,
    close: document.getElementById('gallery-zoom-modal-close'),
  }
}
