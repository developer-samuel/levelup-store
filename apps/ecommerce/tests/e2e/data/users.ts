export const TEST_USER = {
  firstName: 'User',
  lastName: 'Test',
  email: process.env['TEST_USER_EMAIL'] ?? '',
  password: process.env['TEST_USER_PASSWORD'] ?? '',
} as const
