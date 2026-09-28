import { HEADER_TOGGLE, dispatchHeaderToggle } from '@/ts/presentation/layout/header/_events/toggle'

describe('HEADER_TOGGLE', () => {
  it('should equal header:toggle', () => {
    expect(HEADER_TOGGLE).toBe('header:toggle')
  })
})

describe('dispatchHeaderToggle()', () => {
  it('should dispatch header:toggle event with hidden true', () => {
    const header = document.createElement('div')
    document.body.appendChild(header)

    let received: boolean | null = null
    header.addEventListener(HEADER_TOGGLE, (e) => {
      received = (e as CustomEvent<{ hidden: boolean }>).detail.hidden
    })

    dispatchHeaderToggle(header, true)

    expect(received).toBe(true)
  })

  it('should dispatch header:toggle event with hidden false', () => {
    const header = document.createElement('div')
    document.body.appendChild(header)

    let received: boolean | null = null
    header.addEventListener(HEADER_TOGGLE, (e) => {
      received = (e as CustomEvent<{ hidden: boolean }>).detail.hidden
    })

    dispatchHeaderToggle(header, false)

    expect(received).toBe(false)
  })
})
