type TableImageOptions = {
  alt?: string
  width?: string
  height?: string
  objectFit?: string
  borderRadius?: string
}

/** Creates an <img> element for table cells with optional styling */
export function createTableImage(
  src: string,
  { alt = 'Image', width = 'auto', height = '100px', objectFit = 'cover', borderRadius = '' }: TableImageOptions = {},
): HTMLImageElement {
  const img = document.createElement('img')

  img.src = src
  img.alt = alt
  img.style.width = width
  img.style.height = height
  img.style.objectFit = objectFit
  img.style.borderRadius = borderRadius

  return img
}
