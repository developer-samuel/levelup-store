export function getCurrentUserId(): number | null {
  const raw = document.body.dataset.userId
  const id = raw ? parseInt(raw, 10) : NaN

  return isNaN(id) ? null : id
}

export function isAuthenticated(): boolean {
  return document.body.dataset.authenticated === 'true'
}
