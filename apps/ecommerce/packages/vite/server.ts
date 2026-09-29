import {
  HOST,
  PORT,
  CORS_ORIGIN,
  HMR_PROTOCOL,
  HMR_HOST,
  HMR_PORT,
  WATCH_USE_POLLING,
  WATCH_INTERVAL,
  WatchIgnoredPaths,
} from './constants'

const server = {
  host: HOST,
  port: PORT,
  strictPort: true,
  cors: {
    origin: CORS_ORIGIN,
  },
  hmr: {
    protocol: HMR_PROTOCOL,
    host: HMR_HOST,
    port: HMR_PORT,
  },
  watch: {
    usePolling: WATCH_USE_POLLING,
    interval: WATCH_INTERVAL,
    ignored: [WatchIgnoredPaths.NODE_MODULES, WatchIgnoredPaths.VENDOR],
  },
}

export default server
