import type { StringListRecord } from '@/ts/shared/types'
import { showErrors, clearErrors } from '@/ts/shared/elements/form/_ui/errors'
import { query } from '@/ts/shared/utils/dom/query'

export default class FormErrors {
  private readonly form: HTMLFormElement | null
  private readonly errorGroupClass: string | null

  constructor(formSelector: string, errorGroupClass: string | null) {
    this.form = query<HTMLFormElement>(formSelector)
    this.errorGroupClass = errorGroupClass
  }

  show(errors: StringListRecord): void {
    showErrors(this.form, this.errorGroupClass, errors)
  }

  clear(): void {
    clearErrors(this.form)
  }
}
