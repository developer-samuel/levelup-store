import { query, queryAll } from '@/ts/shared/utils/dom/query'

import { attachBackdropListener } from '@/ts/shared/elements/modal/_listeners/backdropListener'

import type { ReviewModalOptions, ReviewModalInstance } from '@/ts/features/reviews/modal/types'
import { SELECTORS } from '@/ts/features/reviews/modal/constants'
import { resetModal } from '@/ts/features/reviews/modal/_ui/modal'
import { toggleReviewMode } from '@/ts/features/reviews/modal/_ui/toggle'
import { attachBodyCounterListener } from '@/ts/features/reviews/modal/_listeners/bodyCounterListener'
import { attachTriggerListener } from '@/ts/features/reviews/modal/_listeners/triggerListener'
import { setupDynamicFields } from '@/ts/features/reviews/modal/_interactions/dynamicFields'
import { setupStars } from '@/ts/features/reviews/modal/_interactions/stars'
import { closeModal, toggleModal } from '@/ts/features/reviews/modal/_interactions/visibility'

const FOCUSABLE_SELECTOR = [
  'a[href]',
  'area[href]',
  'input:not([disabled]):not([type="hidden"])',
  'select:not([disabled])',
  'textarea:not([disabled])',
  'button:not([disabled])',
  '[tabindex]:not([tabindex="-1"])',
].join(', ')

export default class ReviewModal implements ReviewModalInstance {
  readonly modal: HTMLElement
  readonly visibleClass: string
  readonly focusableSelector: string
  keydownHandler: ((e: KeyboardEvent) => void) | null = null
  readonly ratingValue: number = 0
  readonly title: HTMLElement | null
  readonly fields: HTMLElement | null
  readonly justRate: HTMLElement | null
  readonly writeReview: HTMLElement | null
  readonly actionBtn: HTMLElement | null
  readonly ratingContainer: HTMLElement | null
  isReviewMode: boolean = false
  lastActiveElement: HTMLElement | Element | null = null

  constructor({
    triggerSelector = '#open-create-review',
    modalSelector = '#reviews-modal',
    visibleClass = 'visible',
  }: ReviewModalOptions = {}) {
    this.visibleClass = visibleClass
    this.focusableSelector = FOCUSABLE_SELECTOR

    const triggers = Array.from(queryAll<HTMLElement>(triggerSelector))
    const modal = query<HTMLElement>(modalSelector)
    if (!modal) throw new Error(`ReviewModal: "${modalSelector}" not found`)

    this.modal = modal
    this.title = this.modal.querySelector<HTMLElement>(SELECTORS.TITLE)
    this.fields = this.modal.querySelector<HTMLElement>(SELECTORS.FIELDS)
    this.justRate = this.modal.querySelector<HTMLElement>(SELECTORS.JUST_RATE)
    this.writeReview = this.modal.querySelector<HTMLElement>(SELECTORS.WRITE_REVIEW)
    this.actionBtn = this.modal.querySelector<HTMLElement>(SELECTORS.ACTION_BTN)
    this.ratingContainer = this.modal.querySelector<HTMLElement>('#review-modal-rating')

    this.init(triggers)
  }

  private init(triggers: HTMLElement[]): void {
    resetModal(this)

    setupStars(this)
    setupDynamicFields(this, '#reviews-positive-fields', 'positive')
    setupDynamicFields(this, '#reviews-negative-fields', 'negative')

    this.initListeners(triggers)
  }

  private initListeners(triggers: HTMLElement[]): void {
    const closeBtn = this.modal.querySelector<HTMLElement>('#review-modal-close')

    attachBodyCounterListener(this)
    attachTriggerListener(triggers, (el) => toggleModal(this, el))
    attachBackdropListener(this.modal, '.reviews-modal__body', () => closeModal(this))
    closeBtn?.addEventListener('click', () => closeModal(this))
    this.writeReview?.addEventListener('click', () => toggleReviewMode(this, true))
    this.justRate?.addEventListener('click', () => toggleReviewMode(this, false))
  }
}
