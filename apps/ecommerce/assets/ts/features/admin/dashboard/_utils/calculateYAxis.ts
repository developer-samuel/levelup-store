import { STEP_VALUES } from '@/ts/features/admin/dashboard/constants'

type YAxisOptions = {
  maxTicks?: number
  allowDynamicScale?: boolean
}

type YAxisResult = {
  max: number
  tickAmount: number
  tickValues: number[]
}

export function calculateYAxis(
  maxValue: number,
  { maxTicks = 5, allowDynamicScale = false }: YAxisOptions = {},
): YAxisResult {
  if (maxValue === 0) {
    return { max: 1, tickAmount: 1, tickValues: [0] }
  }

  const stepValues: number[] = [...STEP_VALUES]

  if (allowDynamicScale) {
    while (true) {
      const last = stepValues[stepValues.length - 1]
      if (last === undefined || maxValue <= last) break
      stepValues.push(last * 10)
    }
  }

  let step = stepValues[0] ?? 1
  for (const s of stepValues) {
    if (Math.ceil(maxValue / s) <= maxTicks) {
      step = s
      break
    }
  }

  const tickAmount = Math.min(Math.ceil(maxValue / step), maxTicks)
  const tickValues = Array.from({ length: tickAmount + 1 }, (_, i) => i * step)
  const max = tickValues[tickValues.length - 1] ?? 0

  return { max, tickAmount, tickValues }
}
