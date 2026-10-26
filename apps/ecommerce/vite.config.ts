import { defineConfig } from 'vitest/config'

import { base, plugins, resolve, build, test } from './presets/vite/config.ts'
import server from './presets/vite/server.ts'

export default defineConfig({
  base,
  plugins,
  resolve,
  build,
  server,
  test,
})
