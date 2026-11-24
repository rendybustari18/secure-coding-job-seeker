<template>
  <div class="container mt-4">
    <div class="jumbotron bg-primary text-white p-5 rounded">
      <h1 class="display-4">Welcome to Job Portal</h1>
      <p class="lead">Find your dream job or hire the perfect candidate</p>
      <hr class="my-4" />
      <p>Connect employers with job seekers in a simple and efficient way.</p>
      <div class="mt-4">
        <router-link class="btn btn-light btn-lg me-3" to="/auth/register">Get Started</router-link>
        <router-link class="btn btn-outline-light btn-lg" to="/jobs">Browse Jobs</router-link>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="fas fa-users fa-3x text-primary mb-3"></i>
            <h5 class="card-title">For Job Seekers</h5>
            <p class="card-text">Create your profile, upload your CV, and apply for jobs that match your skills.</p>
            <router-link to="/auth/register?role=member" class="btn btn-primary">Join as Member</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="fas fa-building fa-3x text-success mb-3"></i>
            <h5 class="card-title">For Employers</h5>
            <p class="card-text">Post job openings, manage applications, and find the right talent.</p>
            <router-link to="/auth/register?role=company" class="btn btn-success">Join as Company</router-link>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-body text-center">
            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
            <h5 class="card-title">Analytics</h5>
            <p class="card-text">Track your application progress and get insights about the job market.</p>
            <router-link to="/analytics" class="btn btn-info">View Analytics</router-link>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-12">
        <h3>Recent Jobs</h3>
        <div v-if="loading" class="text-center">
          <div class="spinner-border" role="status"></div>
        </div>
        <div v-for="job in recentJobs" :key="job.id" class="card mb-3">
          <div class="card-body">
            <h5 class="card-title" v-html="job.title"></h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ job.company_name }}</h6>
            <p class="card-text">{{ job.description?.substring(0, 150) }}...</p>
            <p class="card-text"><small class="text-muted">Location: {{ job.location }}</small></p>
            <router-link :to="`/jobs/${job.id}`" class="btn btn-primary">View Details</router-link>
          </div>
        </div>
        <p v-if="!loading && recentJobs.length === 0">No jobs found.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { jobApi } from '@/api/jobs'

const recentJobs = ref([])
const loading = ref(false)

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await jobApi.getJobs()
    recentJobs.value = data.slice(0, 5)
  } finally {
    loading.value = false
  }
})
</script>

