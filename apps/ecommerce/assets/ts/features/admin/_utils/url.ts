/** Returns the last non-empty segment of the current URL path */
export function getLastPathSegment(): string {
  const pathSegments = window.location.pathname.split('/').filter(Boolean)

  return pathSegments.at(-1) ?? ''
}
