import api from './index'

export const profileApi = {
  getMemberProfile: () => api.get('/api/profile/member'),
  updateMemberProfile: (id, data) => (id && id !== 'undefined') ? api.put(`/api/profile/member/${id}`, data) : api.put('/api/profile/member', data),
  deleteMemberProfile: (id) => (id && id !== 'undefined') ? api.delete(`/api/profile/member/${id}`) : api.delete('/api/profile/member'),
  uploadPhoto: (formData) =>
    api.post('/api/profile/photo', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  uploadCv: (formData) =>
    api.post('/api/profile/cv', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    }),
  deleteCv: (id) => api.delete(`/api/profile/cv/${id}`),
  getCompanyProfile: () => api.get('/api/profile/company'),
  getSkills: () => api.get('/api/skills'),
  addSkill: (data) => api.post('/api/skills', data),
  updateSkill: (id, data) => api.put(`/api/skills/${id}`, data),
  deleteSkill: (id) => api.delete(`/api/skills/${id}`),
  getEducation: () => api.get('/api/education'),
  addEducation: (data) => api.post('/api/education', data),
  updateEducation: (id, data) => api.put(`/api/education/${id}`, data),
  deleteEducation: (id) => api.delete(`/api/education/${id}`),
}
