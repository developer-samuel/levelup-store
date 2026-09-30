export const TEST_USER = {
  firstName: 'User',
  lastName: 'Test',
  email: process.env['E2E_USER_EMAIL'] ?? '',
  password: process.env['E2E_USER_PASSWORD'] ?? '',
} as const
