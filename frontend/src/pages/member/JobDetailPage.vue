<template>
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link to="/member/jobs">Jobs</router-link></li>
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
            </div>
            <h4>Job Description</h4>
            <div class="mb-4" v-html="job.description"></div>
            <h4>Requirements</h4>
            <div class="mb-4" v-html="job.requirements"></div>
            <h4>About Company</h4>
            <p v-html="job.company_description"></p>
          </div>
          <div class="col-md-4">
            <div class="card bg-light">
              <div class="card-body text-center">
                <div v-if="alreadyApplied" class="alert alert-success">
                  <i class="fas fa-check-circle"></i> You have already applied
                  <router-link to="/member/history" class="btn btn-info btn-sm mt-2 d-block">View Application Status</router-link>
                </div>
                <template v-else>
                  <h5>Ready to Apply?</h5>
                  <router-link :to="`/member/apply/${job.id}`" class="btn btn-success btn-lg">
                    <i class="fas fa-paper-plane"></i> Apply Now
                  </router-link>
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

const route = useRoute()
const job = ref(null)
const alreadyApplied = ref(false)

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(async () => {
  const { data } = await jobApi.getJob(route.params.id)
  job.value = data
  try {
    const apps = await applicationApi.getMyApplications()
    alreadyApplied.value = apps.data.some((a) => String(a.job_id) === String(route.params.id))
  } catch {}
})
</script>

