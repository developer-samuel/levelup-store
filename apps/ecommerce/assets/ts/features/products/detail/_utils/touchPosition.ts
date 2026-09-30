export function getPositionX(event: MouseEvent | TouchEvent): number {
  if (event instanceof TouchEvent) {
    return event.touches[0]?.clientX ?? 0
  }

  return event.clientX
}

export function getPositionY(event: MouseEvent | TouchEvent): number {
  if (event instanceof TouchEvent) {
    return event.touches[0]?.clientY ?? 0
  }

  return event.clientY
}
