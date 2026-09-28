import type { StringListRecord } from '@/ts/shared/types'

export type FormResponse = {
  success?: boolean
  redirect?: string
  message?: string
  errors?: StringListRecord
}

export type FormSubmitResult = Promise<FormResponse | null>

export type FormAlert = {
  display: (success: boolean, message: string) => void
}

export type FormErrorsHandler = {
  clear?: () => void
  show: (errors: StringListRecord) => void
}

export type FormSubmitHandler = (form: HTMLFormElement, alert: FormAlert, errors: FormErrorsHandler) => Promise<void>
