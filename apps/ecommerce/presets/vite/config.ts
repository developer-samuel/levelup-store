import path from 'path'
import symfonyPlugin from 'vite-plugin-symfony'

import { AssetPaths, BuildPaths, TestConfig } from './constants.ts'

export const base = '/dist/ecommerce/'

export const plugins = [
  symfonyPlugin()
]

export const resolve = {
  alias: {
    '@/tests': path.resolve(import.meta.dirname, '../../tests/vitest'),
    '@/e2e': path.resolve(import.meta.dirname, '../../tests/e2e'),
    '@': AssetPaths.ROOT,
  },
}

export const build = {
  outDir: '../../dist/ecommerce',
  emptyOutDir: true,
  chunkSizeWarningLimit: 1000,
  rollupOptions: {
    input: {
      styles: BuildPaths.STYLES,
      scripts: BuildPaths.SCRIPTS,
    },
  },
}

export const test = {
  globals: TestConfig.GLOBALS,
  environment: TestConfig.ENVIRONMENT,
  include: TestConfig.include,
  coverage: TestConfig.COVERAGE,
}
