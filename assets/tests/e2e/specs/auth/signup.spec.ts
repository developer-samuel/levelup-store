import { test, expect } from '@playwright/test'

import { TEST_USER } from '@/tests/e2e/data/users'

import { SignupPage } from '@/tests/e2e/pages/auth/SignupPage'

test.describe('Signup Page', () => {
  let signupPage: SignupPage

  test.beforeEach(async ({ page }) => {
    signupPage = new SignupPage(page)

    await signupPage.goto()
  })

  // ── Page load ──────────────────────────────────────────────────────────────

  test('should load the signup page', async () => {
    await expect(signupPage.root).toBeVisible()
    await expect(signupPage.form).toBeVisible()
    await expect(signupPage.firstNameInput).toBeVisible()
    await expect(signupPage.lastNameInput).toBeVisible()
    await expect(signupPage.emailInput).toBeVisible()
    await expect(signupPage.passwordInput).toBeVisible()
    await expect(signupPage.passwordConfirmInput).toBeVisible()
    await expect(signupPage.termsCheckbox).toBeVisible()
    await expect(signupPage.submitBtn).toBeVisible()
  })

  test('should display terms, forgot password and login links', async () => {
    await expect(signupPage.forgotPasswordLink).toBeVisible()
    await expect(signupPage.loginLink).toBeVisible()
  })

  // ── Form interaction ───────────────────────────────────────────────────────

  test('should allow typing into all text fields', async () => {
    await signupPage.fillFirstName(TEST_USER.firstName)
    await signupPage.fillLastName(TEST_USER.lastName)
    await signupPage.fillEmail(TEST_USER.email)
    await signupPage.fillPassword(TEST_USER.password)
    await signupPage.fillPasswordConfirm(TEST_USER.password)

    await expect(signupPage.firstNameInput).toHaveValue(TEST_USER.firstName)
    await expect(signupPage.lastNameInput).toHaveValue(TEST_USER.lastName)
    await expect(signupPage.emailInput).toHaveValue(TEST_USER.email)
    await expect(signupPage.passwordInput).toHaveValue(TEST_USER.password)
    await expect(signupPage.passwordConfirmInput).toHaveValue(TEST_USER.password)
  })

  test('should have password fields of type password', async () => {
    await expect(signupPage.passwordInput).toHaveAttribute('type', 'password')
    await expect(signupPage.passwordConfirmInput).toHaveAttribute('type', 'password')
  })

  test('should toggle terms checkbox', async () => {
    await expect(signupPage.termsCheckbox).not.toBeChecked()

    await signupPage.acceptTerms()

    await expect(signupPage.termsCheckbox).toBeChecked()
  })

  // ── Validation ─────────────────────────────────────────────────────────────

  test('should show validation feedback when submitting empty form', async ({ page }) => {
    await signupPage.acceptTerms()
    await signupPage.submit()

    const result = await page.waitForFunction(
      () => {
        const emailError = document.querySelector('.auth-page__card-form-group:has([name="email"]) .error')
        const alert = document.querySelector('#signup-page .alert.alert--visible')
        return (emailError?.textContent?.trim() ?? '') !== '' || alert !== null
      },
      { timeout: 8_000 },
    )

    expect(await result.jsonValue()).toBe(true)
  })

  test('should show field error when email is missing', async () => {
    await signupPage.fillFirstName(TEST_USER.firstName)
    await signupPage.fillLastName(TEST_USER.lastName)
    await signupPage.fillPassword(TEST_USER.password)
    await signupPage.fillPasswordConfirm(TEST_USER.password)
    await signupPage.acceptTerms()
    await signupPage.submit()

    await expect(signupPage.emailError).not.toBeEmpty({ timeout: 8_000 })
  })

  test('should show field error when passwords do not match', async ({ page }) => {
    await signupPage.fillFirstName(TEST_USER.firstName)
    await signupPage.fillLastName(TEST_USER.lastName)
    await signupPage.fillEmail(TEST_USER.email)
    await signupPage.fillPassword(TEST_USER.password)
    await signupPage.fillPasswordConfirm('Different123@')
    await signupPage.acceptTerms()
    await signupPage.submit()

    const result = await page.waitForFunction(
      () => {
        const errors = document.querySelectorAll('.auth-page__card-form-group .error')
        const hasFieldError = Array.from(errors).some((el) => (el.textContent?.trim() ?? '') !== '')
        const alert = document.querySelector('#signup-page .alert.alert--visible')
        return hasFieldError || alert !== null
      },
      { timeout: 12_000 },
    )

    expect(await result.jsonValue()).toBe(true)
  })

  // ── Navigation ─────────────────────────────────────────────────────────────

  test('should navigate to login page on link click', async ({ page }) => {
    await signupPage.loginLink.click()
    await page.waitForLoadState('load')

    expect(page.url()).toContain('/login')
  })

  test('should navigate to forgot password page on link click', async ({ page }) => {
    await signupPage.forgotPasswordLink.click()
    await page.waitForLoadState('load')

    expect(page.url()).toContain('/forgot-password')
  })
})
