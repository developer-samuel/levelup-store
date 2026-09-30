import globals from 'globals'
import tsParser from '@typescript-eslint/parser'
import tsPlugin from '@typescript-eslint/eslint-plugin'
import reactRefresh from 'eslint-plugin-react-refresh'
import reactHooks from 'eslint-plugin-react-hooks'
import importPlugin from 'eslint-plugin-import-x'

export default [
  {
    ignores: ['dist', 'node_modules'],
  },
  {
    files: ['**/*.{ts,tsx}'],

    languageOptions: {
      parser: tsParser,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
        ecmaFeatures: { jsx: true },
      },
      globals: globals.browser,
    },

    plugins: {
      '@typescript-eslint': tsPlugin,
      'react-hooks': reactHooks,
      'react-refresh': reactRefresh,
      import: importPlugin,
    },

    rules: {
      // TypeScript
      '@typescript-eslint/no-unused-vars': ['error', { varsIgnorePattern: '^[A-Z_]' }],
      '@typescript-eslint/explicit-function-return-type': 'off',
      '@typescript-eslint/explicit-module-boundary-types': 'off',
      '@typescript-eslint/consistent-type-imports': ['error', { prefer: 'type-imports' }],

      // React hooks
      'react-hooks/rules-of-hooks': 'error',
      'react-hooks/exhaustive-deps': 'warn',
    },
    settings: {
      react: { version: 'detect' },
    },
  },
]
