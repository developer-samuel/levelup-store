export type AdminUser = {
  id: number
  name: string
  email: string
  role: string
  emailVerifiedAt: string | null
  createdAt: string
  [key: string]: unknown
}
