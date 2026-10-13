import { defineConfig } from 'vitest/config'

import { base, plugins, resolve, build, test } from './presets/vite/config'
import server from './presets/vite/server'

export default defineConfig({
  base,
  plugins,
  resolve,
  build,
  server,
  test,
})
