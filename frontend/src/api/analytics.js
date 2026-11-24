import api from './index'

export const analyticsApi = {
  getOverview: () => api.get('/api/analytics'),
}

