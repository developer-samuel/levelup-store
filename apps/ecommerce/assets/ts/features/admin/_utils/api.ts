export function adminListUrl(path: string, id: number | string): string {
  return `/api/admin/${path}/list/${id}`
}
