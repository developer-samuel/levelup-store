export function calculatePosition(position: number, target: number, smoothness: number): number {
  return position + (target - position) * smoothness
}
