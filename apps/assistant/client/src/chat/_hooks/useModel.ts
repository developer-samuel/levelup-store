import { useEffect, useState } from 'react'

import { API_BASE_URL } from '@/app/api.config'

export function useModel(): string {
  const [model, setModel] = useState('')

  useEffect(() => {
    fetch(`${API_BASE_URL}/info`)
      .then((r) => r.json())
      .then((data) => setModel(data?.data?.model ?? ''))
      .catch(() => {})
  }, [])

  return model
}
