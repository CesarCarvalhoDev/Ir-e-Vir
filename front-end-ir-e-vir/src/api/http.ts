import axios, { AxiosError } from 'axios'

export const http = axios.create({
  baseURL: import.meta.env.VITE_API_URL ?? 'http://localhost:8000/api',
  headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
})

const token = sessionStorage.getItem('irevir_token')
if (token) {
  http.defaults.headers.common.Authorization = `Bearer ${token}`
}

http.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error instanceof AxiosError && error.response?.status === 401) {
      setAuthToken(null)
      window.dispatchEvent(new CustomEvent('irevir:session-expired'))
    }

    return Promise.reject(error)
  },
)

export function setAuthToken(token: string | null) {
  if (token) {
    sessionStorage.setItem('irevir_token', token)
    http.defaults.headers.common.Authorization = `Bearer ${token}`
    return
  }

  sessionStorage.removeItem('irevir_token')
  delete http.defaults.headers.common.Authorization
}

export function getApiError(error: unknown): string {
  if (!(error instanceof AxiosError)) return 'Não foi possível concluir a operação.'
  const data = error.response?.data as { message?: string; error?: string; errors?: string | Record<string, string[]> } | undefined
  if (typeof data?.errors === 'string') return data.errors
  if (data?.errors && typeof data.errors === 'object') return Object.values(data.errors).flat().join(' ')
  return data?.error ?? data?.message ?? error.message
}
