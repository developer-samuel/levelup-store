import { limitPaginationWindow } from '@/ts/plugins/datatables/_ui/pagination'

function makePaginationWrapper(items: { active?: boolean; pager?: boolean }[]): {
  table: HTMLTableElement
  lis: HTMLLIElement[]
} {
  const wrapper = document.createElement('div')
  wrapper.className = 'dataTable-wrapper'

  const table = document.createElement('table')
  wrapper.appendChild(table)

  const ul = document.createElement('ul')
  ul.className = 'dataTable-pagination'

  const lis: HTMLLIElement[] = items.map(({ active, pager }) => {
    const li = document.createElement('li')
    if (active) li.classList.add('active')
    if (pager) li.classList.add('pager')
    ul.appendChild(li)
    return li
  })

  wrapper.appendChild(ul)
  document.body.appendChild(wrapper)

  return { table, lis }
}

function get(lis: HTMLLIElement[], index: number): HTMLLIElement {
  const li = lis[index]
  if (!li) throw new Error(`lis[${index}] is undefined`)
  return li
}

describe('limitPaginationWindow()', () => {
  afterEach(() => {
    document.body.innerHTML = ''
  })

  it('should do nothing when table has no .dataTable-wrapper ancestor', () => {
    const table = document.createElement('table')
    expect(() => limitPaginationWindow(table)).not.toThrow()
  })

  it('should do nothing when there is no active li', () => {
    const { table, lis } = makePaginationWrapper([{}, {}, {}])
    limitPaginationWindow(table)
    lis.forEach((li) => expect(li.style.display).toBe(''))
  })

  it('should show active page and one neighbor on each side', () => {
    const { table, lis } = makePaginationWrapper([{}, {}, { active: true }, {}, {}])
    limitPaginationWindow(table)

    expect(get(lis, 0).style.display).toBe('none')
    expect(get(lis, 1).style.display).toBe('inline-block')
    expect(get(lis, 2).style.display).toBe('inline-block')
    expect(get(lis, 3).style.display).toBe('inline-block')
    expect(get(lis, 4).style.display).toBe('none')
  })

  it('should show first three when active is the first item', () => {
    const { table, lis } = makePaginationWrapper([{ active: true }, {}, {}, {}])
    limitPaginationWindow(table)

    expect(get(lis, 0).style.display).toBe('inline-block')
    expect(get(lis, 1).style.display).toBe('inline-block')
    expect(get(lis, 2).style.display).toBe('none')
    expect(get(lis, 3).style.display).toBe('none')
  })

  it('should show last three when active is the last item', () => {
    const { table, lis } = makePaginationWrapper([{}, {}, {}, { active: true }])
    limitPaginationWindow(table)

    expect(get(lis, 0).style.display).toBe('none')
    expect(get(lis, 1).style.display).toBe('none')
    expect(get(lis, 2).style.display).toBe('inline-block')
    expect(get(lis, 3).style.display).toBe('inline-block')
  })

  it('should exclude pager items from the window calculation', () => {
    const { table, lis } = makePaginationWrapper([{ pager: true }, {}, { active: true }, {}, { pager: true }])
    limitPaginationWindow(table)

    expect(get(lis, 0).style.display).toBe('')
    expect(get(lis, 4).style.display).toBe('')
    expect(get(lis, 2).style.display).toBe('inline-block')
  })

  it('should hide all non-window items', () => {
    const { table, lis } = makePaginationWrapper([{}, {}, {}, { active: true }, {}, {}, {}])
    limitPaginationWindow(table)

    expect(get(lis, 0).style.display).toBe('none')
    expect(get(lis, 1).style.display).toBe('none')
    expect(get(lis, 5).style.display).toBe('none')
    expect(get(lis, 6).style.display).toBe('none')
  })
})
