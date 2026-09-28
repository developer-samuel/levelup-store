import type { Locator, Response } from '@playwright/test'
import { expect } from '@playwright/test'

import { BasePage } from '@/tests/e2e/pages/abstracts/BasePage'

export class LoginPage extends BasePage {
  // Root
  readonly root: Locator

  // Form
  readonly form: Locator
  readonly emailInput: Locator
  readonly passwordInput: Locator
  readonly submitBtn: Locator
  readonly alert: Locator

  // Field errors
  readonly emailError: Locator
  readonly passwordError: Locator

  // Links
  readonly forgotPasswordLink: Locator
  readonly signUpLink: Locator

  constructor(page: ConstructorParameters<typeof BasePage>[0]) {
    super(page)

    this.root = page.locator('#login-page')
    this.form = page.locator('#login-form')
    this.emailInput = page.locator('input[name="email"]')
    this.passwordInput = page.locator('input[name="password"]')
    this.submitBtn = page.locator('.auth-page__card-form-action-btn')
    this.alert = page.locator('#login-page .alert.alert--visible')
    this.emailError = page.locator('.auth-page__card-form-group:has([name="email"]) .error')
    this.passwordError = page.locator('.auth-page__card-form-group:has([name="password"]) .error')
    this.forgotPasswordLink = page.locator('.auth-page__card-form-consent-link')
    this.signUpLink = page.locator('.auth-page__card-form-switch a')
  }

  async goto(): Promise<void> {
    await super.goto('/login')

    await expect(this.root).toBeVisible()
  }

  async fillEmail(email: string): Promise<void> {
    await this.emailInput.fill(email)
  }

  async fillPassword(password: string): Promise<void> {
    await this.passwordInput.fill(password)
  }

  async submit(): Promise<void> {
    await this.disableNativeValidation(this.form)
    await this.form.evaluate((f) => {
      f.requestSubmit()
    })
  }

  async login(email: string, password: string): Promise<void> {
    await this.fillEmail(email)
    await this.fillPassword(password)
    await this.submit()
  }

  waitForAuthResponse(timeout = 30_000): Promise<Response> {
    return this._page.waitForResponse((res) => res.url().includes('/api/auth/login'), { timeout })
  }

  getFieldError(fieldName: string): Locator {
    return this.form.locator(`.auth-page__card-form-group:has(input[name="${fieldName}"]) .form-error`)
  }
}
