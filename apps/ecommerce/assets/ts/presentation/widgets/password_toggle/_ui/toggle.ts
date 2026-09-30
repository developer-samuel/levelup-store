import { ICON_SHOW, ICON_HIDE } from '@/ts/presentation/widgets/password_toggle/constants'

export function showPasswords(passwordFields: HTMLInputElement[], iconElements: HTMLImageElement[]): void {
  passwordFields.forEach((field) => (field.type = 'text'))
  iconElements.forEach((icon) => {
    icon.src = ICON_HIDE
    icon.alt = 'Hide password'
  })
}

export function hidePasswords(passwordFields: HTMLInputElement[], iconElements: HTMLImageElement[]): void {
  passwordFields.forEach((field) => (field.type = 'password'))
  iconElements.forEach((icon) => {
    icon.src = ICON_SHOW
    icon.alt = 'Show password'
  })
}
