type QueryParams = Record<string, string | number | undefined | null>
type ParsedParams = Record<string, string | undefined>

export function getQueryParams(param: string): string | null {
  return new URLSearchParams(window.location.search).get(param)
}

/**
 * Builds a URL query string from a params object.
 *
 * String values are trimmed, lowercased, and have spaces/plus signs replaced
 * with dashes. Undefined, null, and empty-string values are omitted.
 */
export function buildQueryString(params: QueryParams): string {
  const qs = new URLSearchParams()

  Object.entries(params).forEach(([key, value]) => {
    if (value === null || value === undefined || value === '') return

    const normalized =
      typeof value === 'string'
        ? value
            .trim()
            .toLowerCase()
            .replace(/\s+|\+/g, '-')
        : String(value)

    qs.append(key, normalized)
  })

  return qs.toString()
}

/**
 * Parses the current URL's query string into a key-value record.
 *
 * URI-decodes values and normalises spaces/plus signs to dashes.
 * Parameters without values are stored as 'undefined'.
 */
export function parseQueryParams(): ParsedParams {
  const search = window.location.search.substring(1)
  if (!search) return {}

  const entries: [string, string | undefined][] = search.split('&').map((param) => {
    const eqIndex = param.indexOf('=')
    if (eqIndex === -1) return [param, undefined]

    const key = param.slice(0, eqIndex)
    const decoded = decodeURIComponent(param.slice(eqIndex + 1))
    return [key, decoded.replace(/[ +]/g, '-')]
  })

  return Object.fromEntries(entries)
}
