import api from './index'

export const authApi = {
  login: (credentials) => api.post('/api/auth/login', credentials),
  register: (data) => api.post('/api/auth/register', data),
  verify: (token) => api.get(`/api/auth/verify?token=${token}`),
  forgotPassword: (data) => api.post('/api/auth/forgot-password', data),
  resetPassword: (data) => api.post('/api/auth/reset-password', data),
  getUserById: (id) => api.get(`/api/auth/user?id=${id}`),
}

