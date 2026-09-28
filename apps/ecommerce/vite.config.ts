import { defineConfig } from 'vite'

import { base, plugins, resolve, build, test } from './vite/config'
import server from './vite/server'

export default defineConfig({
  base,
  plugins,
  resolve,
  build,
  server,
  test,
})
