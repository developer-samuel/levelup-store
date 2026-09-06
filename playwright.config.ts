import type { PlaywrightTestConfig, Project, BrowserContextOptions } from '@playwright/test'
import { defineConfig, devices } from '@playwright/test'
import { config } from 'dotenv'

config({ path: '.env.test' })

type StorageState = Exclude<BrowserContextOptions['storageState'], string | undefined>

const APP_URL = process.env['APP_URL'] ?? 'http://127.0.0.1:8000'
const COOKIE_DOMAIN = new URL(APP_URL).hostname

const projects: Project[] = [
  // Desktop
  { name: 'chromium', use: { ...devices['Desktop Chrome'] } },
  { name: 'webkit', use: { ...devices['Desktop Safari'] } },
  { name: 'edge', use: { ...devices['Desktop Edge'], channel: 'msedge' } },
  {
    name: 'firefox',
    use: {
      ...devices['Desktop Firefox'],
      launchOptions: {
        firefoxUserPrefs: {
          'layers.acceleration.disabled': true,
          'gfx.webrender.all': false,
          'gfx.webrender.enabled': false,
          'gfx.canvas.accelerated': false
        }
      }
    }
  },

  // Tablet
  { name: 'tablet-chrome', use: { ...devices['Galaxy Tab S9'] } },
  { name: 'tablet-safari', use: { ...devices['iPad Pro 11'] } },

  // Mobile
  { name: 'mobile-chrome', use: { ...devices['Pixel 10'] } },
  { name: 'mobile-safari', use: { ...devices['iPhone 17'] } },
]

const webServer: PlaywrightTestConfig['webServer'] = {
  command: 'php -S 127.0.0.1:8000 -t public',
  url: 'http://127.0.0.1:8000',
  reuseExistingServer: true,
  timeout: 30_000,
  env: { PHP_CLI_SERVER_WORKERS: '4' },
}

const storageState: StorageState = {
  cookies: [
    {
      name: 'cookie_consent',
      value: 'true',
      domain: COOKIE_DOMAIN,
      path: '/',
      expires: -1,
      httpOnly: true,
      secure: false,
      sameSite: 'Lax',
    },
  ],
  origins: [],
}

export default defineConfig({
  testDir: './assets/tests/e2e',
  outputDir: './var/tools/playwright/results',

  webServer,

  fullyParallel: false,
  workers: 1,
  retries: 1,

  reporter: [
    ['html', { outputFolder: 'var/tools/playwright/html', open: 'on-failure' }],
    ['list'],
  ],

  timeout: 60_000,
  expect: { timeout: 8_000 },

  use: {
    baseURL: APP_URL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',

    actionTimeout: 10_000,
    navigationTimeout: 60_000,

    storageState,
  },

  projects,
})
