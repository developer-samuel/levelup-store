import { updateDynamicField } from '@/ts/features/reviews/modal/_ui/dynamicFields'
import { handleDynamicFieldChange } from '@/ts/features/reviews/modal/_handlers/dynamicFieldsHandler'

export function attachDynamicFieldListener(
  input: HTMLInputElement,
  counter: HTMLElement,
  wrapper: HTMLElement,
  container: HTMLElement,
  addFieldCallback: () => void,
): void {
  input.addEventListener('input', () => {
    updateDynamicField(input, counter)
    handleDynamicFieldChange(input, wrapper, container, addFieldCallback)
  })
}
