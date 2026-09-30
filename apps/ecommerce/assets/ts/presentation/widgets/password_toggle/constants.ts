const EYE_ICON_BASE = '/img/icons/actions/eye'

export const ICON_SHOW = `${EYE_ICON_BASE}/show.png`
export const ICON_HIDE = `${EYE_ICON_BASE}/hide.png`
export const TOGGLE_ICON_SELECTOR = '.auth-page__card-form-icon'
export const PASSWORD_FIELDS_SELECTORS = [
  '#password',
  '#password-confirmation',
  '#old-password',
  '#new-password',
  '#new-password-confirmation',
] as const
