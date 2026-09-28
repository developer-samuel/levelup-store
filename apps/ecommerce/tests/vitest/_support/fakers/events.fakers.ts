export function makeMouseEvent(target: Element | null): MouseEvent {
  const event = new MouseEvent('click', { bubbles: true, cancelable: true })
  Object.defineProperty(event, 'target', { value: target, writable: false })

  return event
}
