// 🧰 tools/eslint/constants/base.js

import tsParser from "@typescript-eslint/parser";
import tsPlugin from "@typescript-eslint/eslint-plugin";
import tsdoc from "eslint-plugin-tsdoc";
import prettierPlugin from "eslint-plugin-prettier";
import prettierConfig from "eslint-config-prettier";

export const BASE_TS_RECOMMENDED = tsPlugin.configs["flat/recommended"] ?? [];

export const BASE_LANGUAGE_OPTIONS = {
  parser: tsParser,
  parserOptions: {
    ecmaVersion: "latest",
    sourceType: "module",
    project: true,
  },
};

export const BASE_PLUGINS = {
  "@typescript-eslint": tsPlugin,
  tsdoc,
  prettier: prettierPlugin,
};

export const BASE_RULES = {
  // ─────────────────────────────────────────────────────────────────────────
  // Prettier
  // ─────────────────────────────────────────────────────────────────────────
  ...prettierConfig.rules,
  "prettier/prettier": "error",

  // ─────────────────────────────────────────────────────────────────────────
  // TypeScript
  // ─────────────────────────────────────────────────────────────────────────

  // Variables & types
  "@typescript-eslint/no-unused-vars": ["error", { argsIgnorePattern: "^_" }],
  "@typescript-eslint/no-explicit-any": "error",
  "@typescript-eslint/no-unsafe-assignment": "error",
  "@typescript-eslint/no-unsafe-member-access": "error",
  "@typescript-eslint/no-unsafe-call": "error",
  "@typescript-eslint/no-unsafe-return": "error",
  "@typescript-eslint/no-unsafe-argument": "error",

  // Promises
  "@typescript-eslint/no-floating-promises": "error",
  "@typescript-eslint/no-misused-promises": [
    "error",
    { checksVoidReturn: { attributes: false } },
  ],
  "@typescript-eslint/require-await": "error",
  "@typescript-eslint/await-thenable": "error",

  // Type safety
  "@typescript-eslint/no-unnecessary-type-assertion": "error",
  "@typescript-eslint/prefer-nullish-coalescing": "error",
  "@typescript-eslint/prefer-optional-chain": "error",
  "@typescript-eslint/explicit-function-return-type": "error",
  "@typescript-eslint/switch-exhaustiveness-check": "error",

  // Null safety
  "@typescript-eslint/no-non-null-assertion": "error",
  "@typescript-eslint/no-non-null-asserted-optional-chain": "error",

  // Imports
  "@typescript-eslint/consistent-type-imports": [
    "error",
    { prefer: "type-imports" },
  ],

  // Docs
  "tsdoc/syntax": "warn",
};
