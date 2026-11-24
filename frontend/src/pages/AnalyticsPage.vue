<template>
  <div class="container mt-4">
    <h2>Job Market Analytics</h2>
    <p class="text-muted">Overview of the current job market</p>

    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-primary text-white">
          <div class="card-body text-center">
            <i class="fas fa-briefcase fa-2x mb-2"></i>
            <h3>{{ stats.total_jobs }}</h3>
            <p>Active Jobs</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-success text-white">
          <div class="card-body text-center">
            <i class="fas fa-building fa-2x mb-2"></i>
            <h3>{{ stats.total_companies }}</h3>
            <p>Companies</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-info text-white">
          <div class="card-body text-center">
            <i class="fas fa-users fa-2x mb-2"></i>
            <h3>{{ stats.total_members }}</h3>
            <p>Job Seekers</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-warning text-white">
          <div class="card-body text-center">
            <i class="fas fa-paper-plane fa-2x mb-2"></i>
            <h3>{{ stats.total_applications }}</h3>
            <p>Applications</p>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header"><h5>Recent Job Postings</h5></div>
          <div class="card-body">
            <div
              v-for="job in stats.recent_jobs"
              :key="job.id"
              class="d-flex justify-content-between align-items-center mb-3 p-2 border rounded"
            >
              <div>
                <h6 class="mb-1">{{ job.title }}</h6>
                <small class="text-muted">{{ job.company_name }} • {{ job.location }}</small>
              </div>
              <small class="text-muted">{{ formatDate(job.created_at) }}</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card">
          <div class="card-header"><h5>Job Types Distribution</h5></div>
          <div class="card-body">
            <div
              v-for="type in stats.job_types"
              :key="type.job_type"
              class="d-flex justify-content-between mb-2"
            >
              <span>{{ type.job_type }}</span>
              <span class="badge bg-primary">{{ type.count }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { analyticsApi } from '@/api/analytics'

const stats = ref({ total_jobs: 0, total_companies: 0, total_members: 0, total_applications: 0, recent_jobs: [], job_types: [] })

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

onMounted(async () => {
  const { data } = await analyticsApi.getOverview()
  stats.value = data
})
</script>

