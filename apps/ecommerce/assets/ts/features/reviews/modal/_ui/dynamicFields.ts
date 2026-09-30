import type { DynamicField } from '@/ts/features/reviews/modal/types'

const DEFAULT_MAX_LENGTH = 80

export function createDynamicField(type: string, index = 1): DynamicField {
  const wrapper = document.createElement('div')
  wrapper.className = 'reviews-modal__body-form-group-item'

  const input = document.createElement('input')
  input.type = 'text'
  input.name = `${type}s[]`
  input.id = `${type}-detail-${index}`
  input.placeholder = type === 'positive' ? 'Positive' : 'Negative'
  input.className = 'reviews-modal__body-form-group-input input'

  const counter = document.createElement('div')
  counter.id = `review-${type}-chars-${index}`
  counter.className = 'reviews-modal__body-form-group-chars'
  counter.textContent = '0/80'

  wrapper.appendChild(input)
  wrapper.appendChild(counter)

  return { wrapper, input, counter }
}

export function updateDynamicField(
  input: HTMLInputElement,
  counter: HTMLElement,
  maxLength = DEFAULT_MAX_LENGTH,
): void {
  const length = input.value.length
  counter.textContent = `${length}/${maxLength}`

  const exceeded = length > maxLength
  counter.classList.toggle('text-red', exceeded)
  input.style.boxShadow = exceeded ? '0 0 8px 0 #FF0000' : ''
}
