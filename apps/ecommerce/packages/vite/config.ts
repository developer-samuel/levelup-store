import path from 'path'
import symfonyPlugin from 'vite-plugin-symfony'

import { AssetPaths, BuildPaths, TestConfig } from './constants'

export const base = '/dist/ecommerce/'

export const plugins = [
  symfonyPlugin()
]

export const resolve = {
  alias: {
    '@/tests': path.resolve(__dirname, '../../tests/vitest'),
    '@/e2e': path.resolve(__dirname, '../../tests/e2e'),
    '@': AssetPaths.ROOT,
  },
}

export const build = {
  outDir: '../../dist/ecommerce',
  emptyOutDir: true,
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
