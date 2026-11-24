<template>
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link to="/company/jobs">Jobs</router-link></li>
        <li class="breadcrumb-item active">Applicants for "{{ job?.title }}"</li>
      </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h2>Applicants for "{{ job?.title }}"</h2>
        <p class="text-muted">Total Applications: {{ applicants.length }}</p>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="applicants.length > 0" class="row">
          <div v-for="app in applicants" :key="app.id" class="col-md-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <div>
                    <h6 class="mb-1">{{ app.full_name || app.username }}</h6>
                    <small class="text-muted">{{ app.email }}</small>
                  </div>
                </div>
                <span :class="`badge bg-${statusColor(app.status)}`">{{ app.status }}</span><br />
                <small class="text-muted">Applied on {{ formatDate(app.applied_at) }}</small>
              </div>
              <div class="card-footer">
                <router-link :to="`/company/applicants/${app.id}`" class="btn btn-sm btn-primary">View Details</router-link>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="text-center py-5">
          <i class="fas fa-users fa-3x text-muted mb-3"></i>
          <h5>No Applications Yet</h5>
          <router-link to="/company/jobs" class="btn btn-primary">Back to Jobs</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { jobApi } from '@/api/jobs'
import { applicationApi } from '@/api/applications'

const route = useRoute()
const job = ref(null)
const applicants = ref([])

function statusColor(s) {
  return s === 'pending' ? 'warning' : s === 'accepted' ? 'success' : s === 'rejected' ? 'danger' : 'info'
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(async () => {
  const jobId = route.params.jobId
  const [jobRes, appsRes] = await Promise.all([
    jobApi.getJob(jobId),
    applicationApi.getJobApplicants(jobId),
  ])
  job.value = jobRes.data
  applicants.value = appsRes.data
})
</script>

