type NotyfShape = {
  type: 'success' | 'error' | 'info'
  duration: number
  background: string
  icon: {
    className: string
    tagName: keyof HTMLElementTagNameMap
  }
}

export type NotyfConfig = {
  position: {
    x: 'left' | 'center' | 'right'
    y: 'top' | 'bottom'
  }
  dismissible: boolean
  ripple: boolean
  types: NotyfShape[]
}
