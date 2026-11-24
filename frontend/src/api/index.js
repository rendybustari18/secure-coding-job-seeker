import axios from 'axios'

const BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8004'

const api = axios.create({
  baseURL: BASE_URL,
  withCredentials: true,
})
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('jwt_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  console.log('[API Request]', config.method?.toUpperCase(), config.url, config.data)
  return config
})
api.interceptors.response.use(
  (response) => {
    console.log('[API Response]', response.config.url, response.data)
    return response
  },
  (error) => {
    console.error('[API Error]', error.response?.data)
    return Promise.reject(error)
  }
)

export default api

