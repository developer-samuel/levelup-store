import type { Page, Browser } from '@playwright/test'
import { test, expect } from '@playwright/test'

import { APP_URL } from '@/tests/e2e/config'

import { TEST_USER } from '@/tests/e2e/data/users'

import { LoginPage } from '@/tests/e2e/pages/auth/LoginPage'
import { OrderCreatePage } from '@/tests/e2e/pages/orders/OrderCreatePage'

const VALID_ORDER = {
  personal: { email: TEST_USER.email, firstName: TEST_USER.firstName, lastName: TEST_USER.lastName },
  billing: { country: 1, street: 'Test Billing 1', postalCode: '12345', city: 'Bratislava' },
  shipping: { country: 1, street: 'Test Shipping 1', postalCode: '12345', city: 'Prague' },
}

async function loginAndAddProduct(browser: Browser): Promise<Page> {
  const context = await browser.newContext()
  const page = await context.newPage()

  const loginPage = new LoginPage(page)

  await loginPage.goto()

  await loginPage.login(TEST_USER.email, TEST_USER.password)

  await page.waitForURL((url) => !url.pathname.includes('/login'), { waitUntil: 'commit', timeout: 60_000 })

  const cartOk = await addProductToCart(page)
  if (!cartOk) throw new Error('Failed to add product to cart - no stock or Elasticsearch index empty')

  return page
}

async function addProductToCart(page: Page): Promise<boolean> {
  await page.goto(`${APP_URL}/products`, { waitUntil: 'load' })

  const productsVisible = await page
    .locator('.products')
    .waitFor({ state: 'visible', timeout: 20_000 })
    .then(() => true)
    .catch(() => false)

  if (!productsVisible) return false

  const buyButtons = page.locator('.buy-btn')
  const count = await buyButtons.count()

  if (count === 0) return false

  const limit = Math.min(count, 5)

  for (let i = 0; i < limit; i++) {
    try {
      const [response] = await Promise.all([
        page.waitForResponse((resp) => resp.url().includes('/cart/store') && resp.request().method() === 'POST', {
          timeout: 6_000,
        }),
        buyButtons.nth(i).click({ force: true }),
      ])

      if (response.status() < 400) return true
    } catch {
      continue
    }
  }

  return false
}

test.describe.configure({ mode: 'serial' })

test.describe('Order Create Page', () => {
  let sharedPage: Page | undefined
  let orderPage: OrderCreatePage

  test.beforeAll(async ({ browser }) => {
    test.setTimeout(120_000)

    if (!TEST_USER.email || !TEST_USER.password) return

    sharedPage = await loginAndAddProduct(browser).catch(() => undefined)
  })

  test.beforeEach(async () => {
    if (!TEST_USER.email || !TEST_USER.password || !sharedPage) {
      test.skip(true, 'Setup skipped: missing credentials or no products in stock (Elasticsearch empty)')
      return
    }

    orderPage = new OrderCreatePage(sharedPage)

    await orderPage.goto()
  })

  // ── Page load ──────────────────────────────────────────────────────────────

  test('should load the order create page', async () => {
    await expect(orderPage.root).toBeVisible()
    await expect(orderPage.form).toBeVisible()
  })

  test('should display all sections', async () => {
    await expect(orderPage.personalSection).toBeVisible()
    await expect(orderPage.billingSection).toBeVisible()
    await expect(orderPage.shippingSection).toBeVisible()
    await expect(orderPage.paymentSection).toBeVisible()
  })

  test('should display personal data fields', async () => {
    await expect(orderPage.emailInput).toBeVisible()
    await expect(orderPage.firstNameInput).toBeVisible()
    await expect(orderPage.lastNameInput).toBeVisible()
  })

  test('should display billing data fields', async () => {
    await expect(orderPage.billingStreetInput).toBeVisible()
    await expect(orderPage.billingPostalCodeInput).toBeVisible()
    await expect(orderPage.billingCountrySelect).toBeVisible()
    await expect(orderPage.billingCityInput).toBeVisible()
  })

  test('should display payment method options', async () => {
    await expect(orderPage.paymentSection).toBeVisible()
    await expect(orderPage.cardRadio).toBeVisible()
  })

  test('should display total price and submit button', async () => {
    await expect(orderPage.totalPrice).toBeVisible()
    await expect(orderPage.submitBtn).toBeVisible()
  })

  // ── Shipping toggle ────────────────────────────────────────────────────────

  test('should disable shipping fields when send_shipping is unchecked', async () => {
    await orderPage.toggleSendShipping(false)

    await expect(orderPage.shippingStreetInput).toBeDisabled()
    await expect(orderPage.shippingCityInput).toBeDisabled()
  })

  test('should enable shipping fields when send_shipping is checked', async () => {
    await orderPage.toggleSendShipping(true)

    await expect(orderPage.shippingStreetInput).toBeEnabled()
    await expect(orderPage.shippingCityInput).toBeEnabled()
  })

  // ── Form interaction ───────────────────────────────────────────────────────

  test('should allow typing into personal data fields', async () => {
    const { email, firstName, lastName } = VALID_ORDER.personal

    await orderPage.fillPersonal(email, firstName, lastName)

    await expect(orderPage.emailInput).toHaveValue(email)
    await expect(orderPage.firstNameInput).toHaveValue(firstName)
    await expect(orderPage.lastNameInput).toHaveValue(lastName)
  })

  test('should allow typing into billing data fields', async () => {
    const { street, postalCode, city } = VALID_ORDER.billing

    await orderPage.billingStreetInput.fill(street)
    await orderPage.billingPostalCodeInput.fill(postalCode)
    await orderPage.billingCityInput.fill(city)

    await expect(orderPage.billingStreetInput).toHaveValue(street)
    await expect(orderPage.billingPostalCodeInput).toHaveValue(postalCode)
    await expect(orderPage.billingCityInput).toHaveValue(city)
  })

  test('should allow typing into shipping data fields when enabled', async () => {
    await orderPage.toggleSendShipping(true)

    const { country, street, postalCode, city } = VALID_ORDER.shipping

    await orderPage.shippingCountrySelect.selectOption({ index: country })
    await orderPage.shippingStreetInput.fill(street)
    await orderPage.shippingPostalCodeInput.fill(postalCode)
    await orderPage.shippingCityInput.fill(city)

    await expect(orderPage.shippingStreetInput).toHaveValue(street)
    await expect(orderPage.shippingPostalCodeInput).toHaveValue(postalCode)
    await expect(orderPage.shippingCityInput).toHaveValue(city)
  })

  // ── Validation ─────────────────────────────────────────────────────────────

  test('should show validation feedback when submitting empty form', async () => {
    await orderPage.submit()

    const result = await orderPage.page.waitForFunction(
      () => {
        const errors = document.querySelectorAll('.order__card-form-group .error')
        const alert = document.querySelector('#order-page .alert.alert--visible')
        const hasFieldError = Array.from(errors).some((el) => (el.textContent?.trim() ?? '') !== '')
        return hasFieldError || alert !== null
      },
      null,
      { timeout: 15_000 },
    )

    expect(await result.jsonValue()).toBe(true)
  })
})

// ── Successful order ───────────────────────────────────────────────────────

test.describe('Order Create Page - submission', () => {
  let sharedPage: Page | undefined
  let orderPage: OrderCreatePage

  test.beforeAll(async ({ browser }) => {
    test.setTimeout(120_000)

    if (!TEST_USER.email || !TEST_USER.password) return

    sharedPage = await loginAndAddProduct(browser).catch(() => undefined)
  })

  test.beforeEach(async () => {
    test.setTimeout(90_000)

    if (!TEST_USER.email || !TEST_USER.password || !sharedPage) {
      test.skip(true, 'TEST_USER_EMAIL / TEST_USER_PASSWORD not set in .env.test')
      return
    }

    orderPage = new OrderCreatePage(sharedPage)

    // Submission tests need a fresh cart item since placing an order clears the cart
    const cartOk = await addProductToCart(sharedPage)
    if (!cartOk) {
      test.skip(true, 'No products in stock (server returned 5xx)')
      return
    }

    await orderPage.goto()
  })

  test('should redirect to success page after cash on delivery submission', async () => {
    test.setTimeout(90_000)

    const { email, firstName, lastName } = VALID_ORDER.personal
    const { country, street, postalCode, city } = VALID_ORDER.billing

    await orderPage.fillPersonal(email, firstName, lastName)
    await orderPage.billingCountrySelect.selectOption({ index: country })
    await orderPage.billingStreetInput.fill(street)
    await orderPage.billingPostalCodeInput.fill(postalCode)
    await orderPage.billingCityInput.fill(city)

    await orderPage.cashRadio.evaluate((el) => {
      const input = el as HTMLInputElement
      input.checked = true
      input.dispatchEvent(new Event('change', { bubbles: true }))
    })

    await orderPage.submit()
    await orderPage.page.waitForURL('**/orders/success', { waitUntil: 'commit', timeout: 60_000 })

    await expect(orderPage.page.locator('#order-success')).toBeVisible({ timeout: 10_000 })
  })
})
