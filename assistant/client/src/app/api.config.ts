if (!import.meta.env.VITE_API_URL) {
  throw new Error('VITE_API_URL is not defined')
}

export const API_BASE_URL = import.meta.env.VITE_API_URL
