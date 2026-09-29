import { defineConfig } from 'vitest/config'

import { base, plugins, resolve, build, test } from './packages/vite/config'
import server from './packages/vite/server'

export default defineConfig({
  base,
  plugins,
  resolve,
  build,
  server,
  test,
})
