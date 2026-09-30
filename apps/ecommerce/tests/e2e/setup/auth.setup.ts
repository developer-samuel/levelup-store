import { test as setup } from '@playwright/test'

import { APP_URL } from '@/e2e/config'
import { TEST_USER } from '@/e2e/data/users'
import { LoginPage } from '@/e2e/pages/auth/LoginPage'

export const AUTH_STATE_PATH = 'var/tools/playwright/auth/user.json'

setup('authenticate', async ({ page }) => {
  if (!TEST_USER.email || !TEST_USER.password) {
    setup.skip(true, 'E2E_USER_EMAIL / E2E_USER_PASSWORD not set')
    return
  }

  const loginPage = new LoginPage(page)

  await loginPage.goto()
  await loginPage.login(TEST_USER.email, TEST_USER.password)
  await page.waitForURL((url) => !url.pathname.includes('/login'), { timeout: 60_000 })

  // Add a product to cart so order create page is accessible
  await page.goto(`${APP_URL}/products`, { waitUntil: 'load' })

  const buyBtn = page.locator('.buy-btn').first()
  const hasBuyBtn = await buyBtn.isVisible({ timeout: 10_000 }).catch(() => false)

  if (hasBuyBtn) {
    await Promise.all([
      page.waitForResponse((r) => r.url().includes('/cart/store') && r.request().method() === 'POST', {
        timeout: 10_000,
      }),
      buyBtn.click({ force: true }),
    ]).catch(() => {})
  }

  await page.context().storageState({ path: AUTH_STATE_PATH })
})
