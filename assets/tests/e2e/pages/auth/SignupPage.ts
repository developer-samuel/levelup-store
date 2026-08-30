import type { Locator } from '@playwright/test'
import { expect } from '@playwright/test'

import { BasePage } from '@/tests/e2e/pages/abstracts/BasePage'

export class SignupPage extends BasePage {
  // Root
  readonly root: Locator

  // Form
  readonly form: Locator
  readonly firstNameInput: Locator
  readonly lastNameInput: Locator
  readonly emailInput: Locator
  readonly passwordInput: Locator
  readonly passwordConfirmInput: Locator
  readonly termsCheckbox: Locator
  readonly submitBtn: Locator
  readonly alert: Locator

  // Field errors
  readonly firstNameError: Locator
  readonly lastNameError: Locator
  readonly emailError: Locator
  readonly passwordError: Locator
  readonly passwordConfirmError: Locator

  // Links
  readonly termsLink: Locator
  readonly forgotPasswordLink: Locator
  readonly loginLink: Locator

  constructor(page: ConstructorParameters<typeof BasePage>[0]) {
    super(page)

    this.root = page.locator('#signup-page')
    this.form = page.locator('#signup-form')
    this.firstNameInput = page.locator('input[name="first_name"]')
    this.lastNameInput = page.locator('input[name="last_name"]')
    this.emailInput = page.locator('input[name="email"]')
    this.passwordInput = page.locator('input[name="password"]')
    this.passwordConfirmInput = page.locator('input[name="password_confirmation"]')
    this.termsCheckbox = page.locator('input[name="terms_and_conditions"]')
    this.submitBtn = page.locator('.auth-page__card-form-action-btn')
    this.alert = page.locator('#signup-page .alert.alert--visible')

    this.firstNameError = page.locator('.auth-page__card-form-group:has([name="first_name"]) .error')
    this.lastNameError = page.locator('.auth-page__card-form-group:has([name="last_name"]) .error')
    this.emailError = page.locator('.auth-page__card-form-group:has([name="email"]) .error')
    this.passwordError = page.locator('.auth-page__card-form-group:has([name="password"]) .error')
    this.passwordConfirmError = page.locator('.auth-page__card-form-group:has([name="password_confirmation"]) .error')

    this.termsLink = page
      .locator('.auth-page__card-form-consent-checkbox ~ * .auth-page__card-form-consent-link')
      .first()
    this.forgotPasswordLink = page.locator(
      '.auth-page__card-form-consent-text:has([href*="forgot-password"]) .auth-page__card-form-consent-link',
    )
    this.loginLink = page.locator('.auth-page__card-form-switch a')
  }

  async goto(): Promise<void> {
    await super.goto('/signup')

    await expect(this.root).toBeVisible()
  }

  async fillFirstName(value: string): Promise<void> {
    await this.firstNameInput.fill(value)
  }

  async fillLastName(value: string): Promise<void> {
    await this.lastNameInput.fill(value)
  }

  async fillEmail(value: string): Promise<void> {
    await this.emailInput.fill(value)
  }

  async fillPassword(value: string): Promise<void> {
    await this.passwordInput.fill(value)
  }

  async fillPasswordConfirm(value: string): Promise<void> {
    await this.passwordConfirmInput.fill(value)
  }

  async acceptTerms(): Promise<void> {
    await this.termsCheckbox.check()
  }

  async submit(): Promise<void> {
    await this.disableNativeValidation(this.form)
    await this.form.evaluate((f) => {
      f.requestSubmit()
    })
  }

  async fillValidForm(email: string, password: string): Promise<void> {
    await this.fillFirstName('Test')
    await this.fillLastName('User')
    await this.fillEmail(email)
    await this.fillPassword(password)
    await this.fillPasswordConfirm(password)
    await this.acceptTerms()
  }
}
