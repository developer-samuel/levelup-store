import { type ReactNode } from 'react'

import { inlineParse } from '@/chat/message/renderer/InlineParse'

import s from '@/chat/message/renderer/table/RenderTable.module.css'

type OnImageClick = (url: string) => void

const capitalize = (s: string) => (s.length > 0 ? s.charAt(0).toUpperCase() + s.slice(1) : s)
const isLinkRow = (row: string[]) => row.slice(1).every((c) => /^\[.+\]\(.+\)$/.test(c.trim()))

export function renderTable(lines: string[], i: number, onImageClick?: OnImageClick): [ReactNode, number] {
  const rows: string[][] = []

  while (i < lines.length) {
    const cur = (lines[i] ?? '').trim()

    if (!cur.startsWith('|') || !cur.endsWith('|')) break
    if (/^\|[-| :]+\|$/.test(cur)) {
      i++
      continue
    }

    rows.push(
      cur
        .slice(1, -1)
        .split('|')
        .map((c) => c.trim()),
    )

    i++
  }

  const [head, ...rest] = rows
  const lastRow = rest[rest.length - 1]
  const hasFooter = lastRow !== undefined && isLinkRow(lastRow)
  const body = hasFooter ? rest.slice(0, -1) : rest
  const foot = hasFooter ? lastRow : undefined

  const node = (
    <div className={s.wrapper}>
      <table className={s.table}>
        <thead>
          <tr>
            {head?.map((cell, j) => (
              <th key={j} className={`${s.th} ${s.thBorderBottom} ${j < head.length - 1 ? s.thBorderRight : ''}`}>
                {inlineParse(cell, onImageClick)}
              </th>
            ))}
          </tr>
        </thead>
        <tbody>
          {body.map((row, j) => (
            <tr key={j}>
              {row.map((cell, k) => (
                <td key={k} className={`${s.td} ${s.tdBorderBottom} ${k < row.length - 1 ? s.tdBorderRight : ''}`}>
                  {inlineParse(k > 0 ? capitalize(cell) : cell, onImageClick)}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
        {foot && (
          <tfoot>
            <tr>
              {foot.map((cell, k) => (
                <td key={k} className={`${s.td} ${s.tfootBorderTop} ${k < foot.length - 1 ? s.tdBorderRight : ''}`}>
                  {inlineParse(cell, onImageClick)}
                </td>
              ))}
            </tr>
          </tfoot>
        )}
      </table>
    </div>
  )

  return [node, i]
}
