<template>
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link to="/member/jobs">Jobs</router-link></li>
        <li class="breadcrumb-item"><router-link :to="`/member/jobs/${jobId}`">{{ job?.title }}</router-link></li>
        <li class="breadcrumb-item active">Apply</li>
      </ol>
    </nav>

    <h2>Apply for Job</h2>

    <div v-if="message" class="alert alert-success">
      {{ message }}
      <router-link :to="`/member/jobs/${jobId}`" class="btn btn-primary mt-2 me-2">Back to Job</router-link>
      <router-link to="/member/history" class="btn btn-info mt-2">View Applications</router-link>
    </div>

    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div v-if="!message" class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h5>{{ job?.title }}</h5>
            <p class="text-muted">{{ job?.company_name }}</p>

            <form @submit.prevent="handleApply">
              <div class="mb-3">
                <label class="form-label">Cover Letter</label>
                <textarea v-model="coverLetter" class="form-control" rows="8" placeholder="Write your cover letter here..." required></textarea>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Submit Application</button>
                <router-link :to="`/member/jobs/${jobId}`" class="btn btn-secondary">Cancel</router-link>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-light">
          <div class="card-body">
            <h6>Application Tips</h6>
            <ul class="small">
              <li>Make sure your profile is complete</li>
              <li>Upload your latest CV</li>
              <li>Write a compelling cover letter</li>
              <li>Highlight relevant skills and experience</li>
            </ul>
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
const jobId = route.params.jobId
const job = ref(null)
const message = ref('')
const error = ref('')
const coverLetter = ref('')

async function handleApply() {
  try {
    await applicationApi.apply({ job_id: jobId, cover_letter: coverLetter.value })
    message.value = 'Application submitted successfully!'
  } catch {
    error.value = 'Failed to submit application.'
  }
}

onMounted(async () => {
  const { data } = await jobApi.getJob(jobId)
  job.value = data
})
</script>

