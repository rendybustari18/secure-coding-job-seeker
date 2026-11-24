import api from './index'

export const jobApi = {
  getJobs: (params) => api.get('/api/jobs', { params }),
  getJob: (id) => api.get(`/api/jobs/${id}`),
  createJob: (data) => api.post('/api/jobs', data),
  updateJob: (id, data) => api.put(`/api/jobs/${id}`, data),
  deleteJob: (id) => api.delete(`/api/jobs/${id}`),
  getMyJobs: () => api.get('/api/jobs/my'),
}

