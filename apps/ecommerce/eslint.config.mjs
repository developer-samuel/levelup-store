import { BASE_TS_RECOMMENDED, BASE_LANGUAGE_OPTIONS, BASE_PLUGINS, BASE_RULES } from "./tools/eslint/base.js";
import { TEST_GLOBALS } from "./tools/eslint/test.js";

export default [
  ...BASE_TS_RECOMMENDED,
  {
    files: ["ts/**/*.ts"],
    languageOptions: BASE_LANGUAGE_OPTIONS,
    plugins: BASE_PLUGINS,
    rules: BASE_RULES,
  },
  {
    files: ["tests/**/*.ts"],
    languageOptions: {
      ...BASE_LANGUAGE_OPTIONS,
      globals: TEST_GLOBALS,
    },
    plugins: BASE_PLUGINS,
    rules: BASE_RULES,
  },
  { ignores: ["node_modules", "public", "vendor"] },
];
