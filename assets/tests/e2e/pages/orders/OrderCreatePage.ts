import type { Locator } from '@playwright/test'
import { expect } from '@playwright/test'

import { BasePage } from '@/tests/e2e/pages/abstracts/BasePage'

export class OrderCreatePage extends BasePage {
  // Root
  readonly root: Locator
  readonly form: Locator

  // Alert
  readonly alert: Locator

  // Personal section
  readonly personalSection: Locator
  readonly emailInput: Locator
  readonly firstNameInput: Locator
  readonly lastNameInput: Locator

  // Billing section
  readonly billingSection: Locator
  readonly billingStreetInput: Locator
  readonly billingPostalCodeInput: Locator
  readonly billingCountrySelect: Locator
  readonly billingCityInput: Locator

  // Shipping toggle
  readonly sendShippingCheckbox: Locator

  // Shipping section
  readonly shippingSection: Locator
  readonly shippingStreetInput: Locator
  readonly shippingPostalCodeInput: Locator
  readonly shippingCountrySelect: Locator
  readonly shippingCityInput: Locator

  // Payment
  readonly paymentSection: Locator
  readonly cardRadio: Locator
  readonly cashRadio: Locator

  // Summary
  readonly totalPrice: Locator
  readonly submitBtn: Locator

  constructor(page: ConstructorParameters<typeof BasePage>[0]) {
    super(page)

    this.root = page.locator('.order')
    this.form = page.locator('#order-form')
    this.alert = page.locator('#order-page .alert.alert--visible')

    this.personalSection = page.locator('#personal-data')
    this.emailInput = page.locator('input[name="email"]')
    this.firstNameInput = page.locator('input[name="first_name"]')
    this.lastNameInput = page.locator('input[name="last_name"]')

    this.billingSection = page.locator('#order-billing-data')
    this.billingStreetInput = page.locator('input[name="billing_street"]')
    this.billingPostalCodeInput = page.locator('input[name="billing_postal_code"]')
    this.billingCountrySelect = page.locator('select[name="billing_country"]')
    this.billingCityInput = page.locator('input[name="billing_city"]')

    this.sendShippingCheckbox = page.locator('input[name="send_shipping"]')

    this.shippingSection = page.locator('#order-shipping-data')
    this.shippingStreetInput = page.locator('input[name="shipping_street"]')
    this.shippingPostalCodeInput = page.locator('input[name="shipping_postal_code"]')
    this.shippingCountrySelect = page.locator('select[name="shipping_country"]')
    this.shippingCityInput = page.locator('input[name="shipping_city"]')

    this.paymentSection = page.locator('#payment-method-data')
    this.cardRadio = page.locator('input[name="payment_method"][value="card"]')
    this.cashRadio = page.locator('input[name="payment_method"][value="cash"]')

    this.totalPrice = page.locator('#order-total-price')
    this.submitBtn = page.locator('.order__card-form-btn[type="submit"]')
  }

  async goto(): Promise<void> {
    await super.goto('/orders/create')

    await expect(this.root).toBeVisible({ timeout: 15_000 })
  }

  async fillPersonal(email: string, firstName: string, lastName: string): Promise<void> {
    await this.emailInput.fill(email)
    await this.firstNameInput.fill(firstName)
    await this.lastNameInput.fill(lastName)
  }

  async fillBilling(street: string, postalCode: string, city: string, countryValue: string): Promise<void> {
    await this.billingStreetInput.fill(street)
    await this.billingPostalCodeInput.fill(postalCode)
    await this.billingCityInput.fill(city)
    await this.billingCountrySelect.selectOption(countryValue)
  }

  async fillShipping(street: string, postalCode: string, city: string, countryValue: string): Promise<void> {
    await this.shippingStreetInput.fill(street)
    await this.shippingPostalCodeInput.fill(postalCode)
    await this.shippingCityInput.fill(city)
    await this.shippingCountrySelect.selectOption(countryValue)
  }

  async toggleSendShipping(enable: boolean): Promise<void> {
    const checked = await this.sendShippingCheckbox.isChecked()

    if (enable === checked) return

    await this.sendShippingCheckbox.evaluate((el, en) => {
      const input = el as HTMLInputElement
      input.checked = en
      input.dispatchEvent(new Event('change', { bubbles: true }))
    }, enable)

    await this.page.waitForFunction(
      (en) => {
        const input = document.querySelector<HTMLInputElement>('input[name="shipping_street"]')
        return en ? !input?.disabled : input?.disabled === true
      },
      enable,
      { timeout: 5_000 },
    )
  }

  async submit(): Promise<void> {
    await this.disableNativeValidation(this.form)
    await this.submitBtn.click()
  }

  fieldError(fieldName: string): Locator {
    return this.form.locator(`.order__card-form-group:has([name="${fieldName}"]) .error`)
  }
}
