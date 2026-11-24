<template>
  <div>
    <h2>Company Dashboard</h2>

    <div class="row">
      <div class="col-md-4">
        <div class="card bg-primary text-white">
          <div class="card-body"><h5>Total Jobs</h5><h3>{{ stats.total_jobs }}</h3></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
          <div class="card-body"><h5>Total Applications</h5><h3>{{ stats.total_applications }}</h3></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-info text-white">
          <div class="card-body"><h5>Active Jobs</h5><h3>{{ stats.total_jobs }}</h3></div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h5>Quick Actions</h5></div>
          <div class="card-body">
            <router-link to="/company/jobs" class="btn btn-primary mb-2 w-100">Post New Job</router-link>
            <router-link to="/company/applicants" class="btn btn-success mb-2 w-100">View Applications</router-link>
            <router-link to="/company/jobs" class="btn btn-info w-100">Manage Jobs</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h5>Recent Applications</h5></div>
          <div class="card-body">
            <div v-for="app in recentApps" :key="app.id" class="mb-2">
              <strong>{{ app.username }}</strong> applied for <em>{{ app.job_title }}</em>
              <br /><small class="text-muted">{{ formatDate(app.applied_at) }}</small>
            </div>
            <p v-if="recentApps.length === 0">No recent applications.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { jobApi } from '@/api/jobs'
import { applicationApi } from '@/api/applications'

const stats = ref({ total_jobs: 0, total_applications: 0 })
const recentApps = ref([])

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(async () => {
  const [jobsRes, appsRes] = await Promise.all([
    jobApi.getMyJobs(),
    applicationApi.getCompanyApplicants(),
  ])
  stats.value.total_jobs         = jobsRes.data.length
  stats.value.total_applications = appsRes.data.length
  recentApps.value               = appsRes.data.slice(0, 5)
})
</script>

