import globals from "globals";

const TEST_NAMES = [
  "vi",
  "describe",
  "it",
  "test",
  "beforeEach",
  "afterEach",
  "beforeAll",
  "afterAll",
  "expect",
];

export const TEST_GLOBALS = {
  ...globals.browser,
  ...Object.fromEntries(TEST_NAMES.map(name => [name, "readonly"])),
};
