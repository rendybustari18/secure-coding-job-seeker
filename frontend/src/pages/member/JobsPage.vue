<template>
  <div>
    <h2>Available Jobs</h2>

    <div class="card mb-4">
      <div class="card-body">
        <form class="row g-3" @submit.prevent="searchJobs">
          <div class="col-md-5">
            <input v-model="searchQuery" type="text" class="form-control" placeholder="Search jobs..." />
          </div>
          <div class="col-md-5">
            <input v-model="locationQuery" type="text" class="form-control" placeholder="Location..." />
          </div>
          <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Search</button>
          </div>
        </form>
      </div>
    </div>
    <div v-if="searchQuery || locationQuery" class="alert alert-info">
      Search results for: <strong v-html="searchQuery"></strong>
      <span v-if="locationQuery"> in <strong v-html="locationQuery"></strong></span>
    </div>

    <div class="row">
      <div v-for="job in jobs" :key="job.id" class="col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">{{ job.title }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ job.company_name }}</h6>
            <p class="card-text">{{ job.description?.substring(0, 150) }}...</p>
            <p class="card-text"><small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ job.location }}</small></p>
            <p class="card-text"><small class="text-muted"><i class="fas fa-briefcase"></i> {{ job.job_type }}</small></p>
            <p v-if="job.salary_min && job.salary_max" class="card-text">
              <small class="text-success">Rp {{ formatCurrency(job.salary_min) }} - Rp {{ formatCurrency(job.salary_max) }}</small>
            </p>
          </div>
          <div class="card-footer">
            <router-link :to="`/member/jobs/${job.id}`" class="btn btn-primary">View Details</router-link>
            <router-link :to="`/member/apply/${job.id}`" class="btn btn-success ms-2">Apply Now</router-link>
          </div>
        </div>
      </div>
      <div v-if="jobs.length === 0" class="col-12">
        <div class="alert alert-warning">No jobs found matching your criteria.</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { jobApi } from '@/api/jobs'

const jobs = ref([])
const searchQuery = ref('')
const locationQuery = ref('')

async function searchJobs() {
  const { data } = await jobApi.getJobs({ search: searchQuery.value, location: locationQuery.value })
  jobs.value = data
}

function formatCurrency(val) {
  return Number(val).toLocaleString('id-ID')
}

onMounted(searchJobs)
</script>

