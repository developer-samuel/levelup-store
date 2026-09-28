type ClassName = string | false | null | undefined

export function cn(...classes: ClassName[]): string {
  return classes.filter(Boolean).join(' ')
}
