import type { FormAlert, FormErrorsHandler } from '@/ts/shared/elements/form/types'

export function makeFormAlert(): FormAlert {
  return { display: vi.fn() }
}

export function makeFormErrorsHandler(): FormErrorsHandler {
  return { show: vi.fn(), clear: vi.fn() }
}
