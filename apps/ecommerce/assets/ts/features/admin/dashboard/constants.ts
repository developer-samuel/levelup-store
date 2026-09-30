export const STEP_VALUES: readonly number[] = [
  1, 2, 4, 10, 20, 50, 100, 200, 500, 1_000, 2_000, 5_000, 10_000, 50_000, 100_000, 1_000_000,
]

export const CHART_HEIGHTS = {
  line: 500,
  bar: 350,
  donut: 350,
} as const

export const COLORS = {
  ordersRatio: ['#00C853', '#FF3F3F'],
} as const

export const MARKERS = {
  default: { size: 3, radius: 3 },
} as const

export const STROKE = {
  smooth: { curve: 'smooth', width: 3 },
} as const
