let token: string | null = null

export const accessToken = {
  get(): string | null {
    return token
  },

  set(value: string): void {
    token = value
  },

  clear(): void {
    token = null
  },
}
