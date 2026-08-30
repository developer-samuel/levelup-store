import { test, expect } from '@playwright/test'

import { TEST_USER } from '@/tests/e2e/data/users'

import { LoginPage } from '@/tests/e2e/pages/auth/LoginPage'

const GUEST_USER = { email: 'nonexistent@example.com', password: 'wrongpassword123' }
const INVALID_USER = { email: 'not-an-email', password: 'somepassword' }

test.describe('Login Page', () => {
  let loginPage: LoginPage

  test.beforeEach(async ({ page }) => {
    loginPage = new LoginPage(page)

    await loginPage.goto()
  })

  // ── Page load ──────────────────────────────────────────────────────────────

  test('should load the login page', async () => {
    await expect(loginPage.root).toBeVisible()
    await expect(loginPage.form).toBeVisible()
    await expect(loginPage.emailInput).toBeVisible()
    await expect(loginPage.passwordInput).toBeVisible()
    await expect(loginPage.submitBtn).toBeVisible()
  })

  test('should display forgot password and sign up links', async () => {
    await expect(loginPage.forgotPasswordLink).toBeVisible()
    await expect(loginPage.signUpLink).toBeVisible()
  })

  // ── Validation ─────────────────────────────────────────────────────────────

  test('should show field error when submitting empty form', async () => {
    await loginPage.submit()

    await expect(loginPage.emailError).not.toBeEmpty({ timeout: 15_000 })
  })

  test('should show field error when email is invalid format', async () => {
    await loginPage.fillEmail(INVALID_USER.email)
    await loginPage.fillPassword(INVALID_USER.password)
    await loginPage.submit()

    await expect(loginPage.emailError).not.toBeEmpty({ timeout: 15_000 })
  })

  test('should show error when credentials are invalid', async () => {
    await loginPage.login(GUEST_USER.email, GUEST_USER.password)

    await expect(loginPage.alert).toBeVisible({ timeout: 8_000 })
  })

  // ── Form interaction ───────────────────────────────────────────────────────

  test('should allow typing into email and password fields', async () => {
    await loginPage.fillEmail(TEST_USER.email)
    await loginPage.fillPassword(TEST_USER.password)

    await expect(loginPage.emailInput).toHaveValue(TEST_USER.email)
    await expect(loginPage.passwordInput).toHaveValue(TEST_USER.password)
  })

  test('should have password field of type password', async () => {
    await expect(loginPage.passwordInput).toHaveAttribute('type', 'password')
  })

  // ── Navigation ─────────────────────────────────────────────────────────────

  test('should navigate to forgot password page on link click', async ({ page }) => {
    await loginPage.forgotPasswordLink.click()
    await page.waitForLoadState('load')

    expect(page.url()).toContain('/forgot-password')
  })

  test('should navigate to sign up page on link click', async ({ page }) => {
    await loginPage.signUpLink.click()
    await page.waitForLoadState('load')

    expect(page.url()).toContain('/signup')
  })

  // ── Successful login ───────────────────────────────────────────────────────

  test('should redirect after successful login', async ({ page }) => {
    test.setTimeout(90_000)

    if (!TEST_USER.email || !TEST_USER.password) {
      test.skip()
      return
    }

    const loginResponsePromise = page.waitForResponse(
      (res) => res.url().includes('/api/auth/login') && res.request().method() === 'POST',
      { timeout: 15_000 },
    )

    await loginPage.login(TEST_USER.email, TEST_USER.password)

    const loginResponse = await loginResponsePromise.catch(() => null)
    if (!loginResponse) {
      throw new Error('Login form did not submit via HTTP request - ensure the page JS loaded before clicking submit')
    }

    const loginBody = await loginResponse.text()
    console.log(`[e2e] login response: HTTP ${loginResponse.status()} - ${loginBody}`)

    await page.waitForURL((url) => !url.pathname.includes('/login'), { waitUntil: 'commit', timeout: 60_000 })

    expect(page.url()).not.toContain('/login')
  })
})
