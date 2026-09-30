import type { FormAlert, FormErrorsHandler, FormSubmitHandler } from '@/ts/shared/elements/form/types'
import { attachSubmitListener } from '@/ts/shared/elements/form/_listeners/submitListener'
import { query } from '@/ts/shared/utils/dom/query'

export default abstract class BaseForm {
  protected readonly form: HTMLFormElement | null
  protected readonly alert: FormAlert
  protected readonly errors: FormErrorsHandler
  protected readonly submitHandler: FormSubmitHandler
  protected readonly scrollAfterSubmit: boolean

  constructor(
    formSelector: string,
    alert: FormAlert,
    errors: FormErrorsHandler,
    submitHandler: FormSubmitHandler,
    scrollAfterSubmit = false,
  ) {
    this.form = query<HTMLFormElement>(formSelector)
    this.alert = alert
    this.errors = errors
    this.submitHandler = submitHandler
    this.scrollAfterSubmit = scrollAfterSubmit

    this.init()
  }

  protected init(): void {
    attachSubmitListener(this.form, this.submitHandler, this.errors, this.alert)
  }
}
