import api from './index'

export const applicationApi = {
  apply: (data) => api.post('/api/applications', data),
  getMyApplications: () => api.get('/api/applications/my'),
  getApplication: (id) => api.get(`/api/applications/${id}`),
  updateApplication: (id, data) => api.put(`/api/applications/${id}`, data),
  deleteApplication: (id) => api.delete(`/api/applications/${id}`),
  getCompanyApplicants: () => api.get('/api/applications/company'),
  getJobApplicants: (jobId) => api.get('/api/applications/job', { params: { job_id: jobId } }),
  updateStatus: (id, data) => api.put(`/api/applications/${id}/status`, typeof data === 'object' ? data : { status: data }),
  getApplicantDetail: (id) => api.get(`/api/applications/${id}/applicant`),
}
