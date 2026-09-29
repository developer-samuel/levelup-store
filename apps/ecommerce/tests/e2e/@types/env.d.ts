/// <reference types="node" />

declare global {
  namespace NodeJS {
    type ProcessEnv = {
      readonly E2E_APP_URL?: string
      readonly E2E_USER_EMAIL?: string
      readonly E2E_USER_PASSWORD?: string
    }
  }
}
