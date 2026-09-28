/// <reference types="node" />

declare global {
  namespace NodeJS {
    type ProcessEnv = {
      readonly APP_URL?: string
      readonly TEST_USER_EMAIL?: string
      readonly TEST_USER_PASSWORD?: string
    }
  }
}
