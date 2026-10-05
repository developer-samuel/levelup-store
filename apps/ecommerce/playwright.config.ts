import type { PlaywrightTestConfig, Project, BrowserContextOptions } from '@playwright/test'
import { defineConfig, devices } from '@playwright/test'
import { config } from 'dotenv'

config({ path: '.env.test' })

type StorageState = Exclude<BrowserContextOptions['storageState'], string | undefined>

const E2E_PORT = 8008
const APP_URL = `http://127.0.0.1:${E2E_PORT}`
const COOKIE_DOMAIN = new URL(APP_URL).hostname

const projects: Project[] = [
  // Auth setup (runs once before all tests)
  {
    name: 'setup',
    testMatch: '**/setup/auth.setup.ts',
  },

  // Desktop
  { name: 'chromium', dependencies: ['setup'], use: { ...devices['Desktop Chrome'] } },
  { name: 'webkit', dependencies: ['setup'], use: { ...devices['Desktop Safari'] } },
  { name: 'edge', dependencies: ['setup'], use: { ...devices['Desktop Edge'], channel: 'msedge' } },
  {
    name: 'firefox',
    dependencies: ['setup'],
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
  { name: 'tablet-chrome', dependencies: ['setup'], use: { ...devices['Galaxy Tab S9'] } },
  { name: 'tablet-safari', dependencies: ['setup'], use: { ...devices['iPad Pro 11'] } },

  // Mobile
  { name: 'mobile-chrome', dependencies: ['setup'], use: { ...devices['Pixel 10'] } },
  { name: 'mobile-safari', dependencies: ['setup'], use: { ...devices['iPhone 17'] } },
]

const webServer: PlaywrightTestConfig['webServer'] = {
  command: `php -S 127.0.0.1:${E2E_PORT} -t public router.php`,
  url: `${APP_URL}/api/health`,
  reuseExistingServer: false,
  timeout: 60_000,
  stdout: 'ignore',
  stderr: 'ignore',
  env: {
      PHP_CLI_SERVER_WORKERS: '4',
      APP_ENV: 'test',
      REDIS_URL: 'redis://127.0.0.1:6379',
      DB_HOST: '127.0.0.1',
      OTEL_PHP_AUTOLOAD_ENABLED: 'false',
      ELASTICSEARCH_ENABLED: 'false',
    },
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
  testDir: './tests/e2e',
  outputDir: './var/tools/playwright/results',
  tsconfig: './tsconfig.test.json',

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
