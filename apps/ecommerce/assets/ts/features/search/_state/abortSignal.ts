let controller: AbortController | null = null

export function getAbortSignal(): AbortSignal {
  controller?.abort()
  controller = new AbortController()

  return controller.signal
}
