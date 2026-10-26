declare module '@emoji-mart/react' {
  import type { ComponentType } from 'react'

  type PickerProps = {
    data: object
    onEmojiSelect: (emoji: { native: string }) => void
    categories?: string[]
    previewPosition?: 'top' | 'bottom' | 'none'
    skinTonePosition?: 'top' | 'search' | 'none'
    theme?: 'auto' | 'light' | 'dark'
    locale?: string
    perLine?: number
  }

  const Picker: ComponentType<PickerProps>

  export default Picker
}
