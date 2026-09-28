import type { ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { createDynamicField } from '@/ts/features/reviews/modal/_ui/dynamicFields'
import { attachDynamicFieldListener } from '@/ts/features/reviews/modal/_listeners/dynamicFieldsListener'

export function setupDynamicFields(modalInstance: ReviewModalInstance, containerSelector: string, type: string): void {
  const container = modalInstance.modal.querySelector<HTMLElement>(containerSelector)
  if (!container) return

  container.innerHTML = ''

  const addField = (): void => {
    const { wrapper, input, counter } = createDynamicField(type, container.children.length + 1)

    attachDynamicFieldListener(input, counter, wrapper, container, addField)
    container.appendChild(wrapper)
  }

  addField()
}
