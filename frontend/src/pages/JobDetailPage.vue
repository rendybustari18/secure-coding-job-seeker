<template>
  <div class="container mt-4">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link to="/jobs">Jobs</router-link></li>
        <li class="breadcrumb-item active" v-html="job?.title"></li>
      </ol>
    </nav>

    <div v-if="job" class="card">
      <div class="card-body">
        <div class="row">
          <div class="col-md-8">
            <h2 v-html="job.title"></h2>
            <h5 class="text-muted mb-3">{{ job.company_name }}</h5>

            <div class="mb-3">
              <span class="badge bg-primary me-2">{{ job.location }}</span>
              <span class="badge bg-secondary me-2">{{ job.job_type }}</span>
              <span v-if="job.salary_min && job.salary_max" class="badge bg-success">
                Rp {{ formatCurrency(job.salary_min) }} - Rp {{ formatCurrency(job.salary_max) }}
              </span>
            </div>

            <h4>Job Description</h4>
            <div class="mb-4" v-html="job.description"></div>

            <h4>Requirements</h4>
            <div class="mb-4" v-html="job.requirements"></div>

            <h4>About Company</h4>
            <p v-html="job.company_description"></p>

            <p v-if="job.website">
              <strong>Website:</strong>
              <a :href="job.website" target="_blank">{{ job.website }}</a>
            </p>
          </div>

          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <template v-if="authStore.isMember">
                  <div v-if="alreadyApplied" class="alert alert-success">
                    <i class="fas fa-check-circle"></i> You have already applied
                  </div>
                  <router-link v-else :to="`/member/apply/${job.id}`" class="btn btn-success btn-lg">
                    <i class="fas fa-paper-plane"></i> Apply Now
                  </router-link>
                </template>
                <template v-else>
                  <h5>Want to Apply?</h5>
                  <p class="text-muted">Please login to apply</p>
                  <router-link to="/auth/login" class="btn btn-primary btn-lg">Login to Apply</router-link>
                </template>
                <hr />
                <small class="text-muted">Posted on {{ formatDate(job.created_at) }}</small>
              </div>
            </div>
          </div>
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
import { useAuthStore } from '@/store/auth'

const route = useRoute()
const authStore = useAuthStore()
const job = ref(null)
const alreadyApplied = ref(false)

function formatCurrency(val) {
  return Number(val).toLocaleString('id-ID')
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(async () => {
  const { data } = await jobApi.getJob(route.params.id)
  job.value = data

  if (authStore.isMember) {
    try {
      const apps = await applicationApi.getMyApplications()
      alreadyApplied.value = apps.data.some((a) => a.job_id == route.params.id)
    } catch {}
  }
})
</script>

