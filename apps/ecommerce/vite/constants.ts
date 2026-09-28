import path from 'path'

// ── Paths ────────────────────────────────────────────────
export const AssetPaths = Object.freeze({
  ROOT: path.resolve(__dirname, '../assets'),
})

export const BuildPaths = Object.freeze({
  STYLES: './assets/scss/app.scss',
  SCRIPTS: './assets/ts/app/bootstrap.ts',
})

// ── Server ───────────────────────────────────────────────
export const HOST = '0.0.0.0'
export const PORT = 5173
export const CORS_ORIGIN = ['http://localhost:8000']

export const WATCH_USE_POLLING = true
export const WATCH_INTERVAL = 100

export const HMR_PROTOCOL = 'ws'
export const HMR_HOST = 'localhost'
export const HMR_PORT = PORT

// ── Watch ────────────────────────────────────────────────
export const WatchIgnoredPaths = Object.freeze({
  NODE_MODULES: '**/node_modules/**',
  VENDOR: '**/vendor/**',
})

// ── Test ─────────────────────────────────────────────────
export const TestConfig = Object.freeze({
  GLOBALS: true,
  ENVIRONMENT: 'jsdom',
  include: [
    '**/tests/vitest/unit/**/*.test.ts',
    '**/tests/vitest/integration/**/*.test.ts',
    '**/tests/vitest/functional/**/*.test.ts',
  ],
  COVERAGE: {
    reporter: ['html'],
    reportsDirectory: 'var/tools/vitest/html',
    provider: 'v8' as const,
  },
})
