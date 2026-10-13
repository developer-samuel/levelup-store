import { useCallback, useEffect, useState } from 'react'

import { API_BASE_URL } from '@/app/api.config'

type ModelState = { model: string; ollama: boolean }

export function useModel(): [ModelState, (model: string) => void] {
  const [state, setState] = useState<ModelState>({ model: '', ollama: false })

  useEffect(() => {
    fetch(`${API_BASE_URL}/info`)
      .then((r) => r.json())
      .then((res) => setState({ model: res?.data?.model ?? '', ollama: res?.data?.ollama ?? false }))
      .catch(() => {})
  }, [])

  const setModel = useCallback((model: string) => setState((prev) => ({ ...prev, model })), [])

  return [state, setModel]
}
