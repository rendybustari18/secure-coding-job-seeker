import { defineStore } from 'pinia'
import { jobApi } from '@/api/jobs'

export const useJobStore = defineStore('jobs', {
  state: () => ({
    jobs: [],
    currentJob: null,
    myJobs: [],
    loading: false,
    error: null,
  }),

  actions: {
    async fetchJobs(params = {}) {
      this.loading = true
      try {
        const { data } = await jobApi.getJobs(params)
        this.jobs = data
      } catch (e) {
        this.error = e.message
      } finally {
        this.loading = false
      }
    },

    async fetchJob(id) {
      const { data } = await jobApi.getJob(id)
      this.currentJob = data
      return data
    },

    async fetchMyJobs() {
      const { data } = await jobApi.getMyJobs()
      this.myJobs = data
    },

    async createJob(jobData) {
      const { data } = await jobApi.createJob(jobData)
      return data
    },

    async updateJob(id, jobData) {
      const { data } = await jobApi.updateJob(id, jobData)
      return data
    },

    async deleteJob(id) {
      const { data } = await jobApi.deleteJob(id)
      return data
    },
  },
})

