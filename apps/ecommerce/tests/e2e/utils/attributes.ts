import type { Locator } from '@playwright/test'

export async function getNumericAttribute(locator: Locator, attr: string, fallback = 1): Promise<number> {
  const val = await locator.getAttribute(attr)

  return Number(val ?? fallback)
}
