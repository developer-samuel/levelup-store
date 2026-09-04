import { createActionButton, renderActionButtons } from '@/ts/shared/elements/table/_ui/actionButton'

describe('createActionButton()', () => {
  it('should return a td element', () => {
    expect(createActionButton({ className: 'btn', text: 'Edit' }).tagName).toBe('TD')
  })

  it('should contain an anchor element', () => {
    const td = createActionButton({ className: 'btn', text: 'Edit' })
    expect(td.querySelector('a')).not.toBeNull()
  })

  it('should set className on the anchor', () => {
    const td = createActionButton({ className: 'btn-danger', text: 'Delete' })
    expect(td.querySelector('a')?.className).toBe('btn-danger')
  })

  it('should set text on the anchor', () => {
    const td = createActionButton({ className: 'btn', text: 'View' })
    expect(td.querySelector('a')?.textContent).toBe('View')
  })

  it('should set data-id from numeric id', () => {
    const td = createActionButton({ className: 'btn', text: 'Edit', id: 42 })
    expect(td.querySelector('a')?.dataset.id).toBe('42')
  })

  it('should set data-id from string id', () => {
    const td = createActionButton({ className: 'btn', text: 'Edit', id: 'abc-123' })
    expect(td.querySelector('a')?.dataset.id).toBe('abc-123')
  })

  it('should set data-id to empty string when id is not provided', () => {
    const td = createActionButton({ className: 'btn', text: 'Edit' })
    expect(td.querySelector('a')?.dataset.id).toBe('')
  })

  it('should set href to empty string', () => {
    const td = createActionButton({ className: 'btn', text: 'Edit' })
    expect(td.querySelector('a')?.getAttribute('href')).toBe('')
  })
})

describe('renderActionButtons()', () => {
  it('should append anchors into the td', () => {
    const td = document.createElement('td')
    renderActionButtons(td, [{ className: 'btn', text: 'Edit', href: '/edit/1', attrs: {} }], 1)
    expect(td.querySelectorAll('a')).toHaveLength(1)
  })

  it('should set href on each anchor', () => {
    const td = document.createElement('td')
    renderActionButtons(td, [{ className: 'btn', text: 'Edit', href: '/edit/5', attrs: {} }], 5)
    expect(td.querySelector('a')?.getAttribute('href')).toBe('/edit/5')
  })

  it('should set custom attrs on each anchor', () => {
    const td = document.createElement('td')
    renderActionButtons(td, [{ className: 'btn', text: 'Delete', href: '/delete/3', attrs: { 'data-confirm': 'true' } }], 3)
    expect(td.querySelector('a')?.getAttribute('data-confirm')).toBe('true')
  })

  it('should render multiple buttons', () => {
    const td = document.createElement('td')
    renderActionButtons(
      td,
      [
        { className: 'btn-edit', text: 'Edit', href: '/edit/1', attrs: {} },
        { className: 'btn-delete', text: 'Delete', href: '/delete/1', attrs: {} },
      ],
      1,
    )
    expect(td.querySelectorAll('a')).toHaveLength(2)
  })

  it('should do nothing when buttons array is empty', () => {
    const td = document.createElement('td')
    renderActionButtons(td, [], 1)
    expect(td.querySelectorAll('a')).toHaveLength(0)
  })

  it('should skip button when querySelector returns null', () => {
    const td = document.createElement('td')
    vi.spyOn(HTMLTableCellElement.prototype, 'querySelector').mockReturnValueOnce(null)
    renderActionButtons(td, [{ className: 'btn', text: 'Edit', href: '/edit/1', attrs: {} }], 1)
    expect(td.querySelectorAll('a')).toHaveLength(0)
  })
})
