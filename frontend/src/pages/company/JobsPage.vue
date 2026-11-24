<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2>Manage Jobs</h2>
      <button class="btn btn-primary" @click="showModal = true">
        <i class="fas fa-plus"></i> Post New Job
      </button>
    </div>

    <div v-if="message" class="alert alert-success">{{ message }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="card">
      <div class="card-body">
        <div v-if="jobs.length > 0" class="table-responsive">
          <table class="table">
            <thead>
              <tr><th>Title</th><th>Location</th><th>Type</th><th>Status</th><th>Posted</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="job in jobs" :key="job.id">
                <td>{{ job.title }}</td>
                <td>{{ job.location }}</td>
                <td>{{ job.job_type }}</td>
                <td><span :class="`badge bg-${job.status === 'active' ? 'success' : 'secondary'}`">{{ job.status }}</span></td>
                <td>{{ formatDate(job.created_at) }}</td>
                <td>
                  <button class="btn btn-sm btn-warning me-1" @click="editJob(job)">Edit</button>
                  <router-link :to="`/company/jobs/${job.id}/applicants`" class="btn btn-sm btn-info me-1">Applicants</router-link>
                  <button class="btn btn-sm btn-danger" @click="deleteJob(job.id)">Delete</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-5">
          <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
          <h5>No Jobs Posted Yet</h5>
          <button class="btn btn-primary" @click="showModal = true">Post Your First Job</button>
        </div>
      </div>
    </div>

    <!-- Job Modal -->
    <div v-if="showModal" class="modal d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ editingJob ? 'Edit Job' : 'Post New Job' }}</h5>
            <button class="btn-close" @click="closeModal"></button>
          </div>
          <form @submit.prevent="handleSubmit">
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label">Job Title</label>
                <input v-model="form.title" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Job Description</label>
                <textarea v-model="form.description" class="form-control" rows="5" required></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Requirements</label>
                <textarea v-model="form.requirements" class="form-control" rows="4" required></textarea>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Min Salary</label>
                  <input v-model="form.salary_min" type="number" class="form-control" />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Max Salary</label>
                  <input v-model="form.salary_max" type="number" class="form-control" />
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">Location</label>
                  <input v-model="form.location" type="text" class="form-control" required />
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label">Job Type</label>
                  <select v-model="form.job_type" class="form-control" required>
                    <option value="full-time">Full Time</option>
                    <option value="part-time">Part Time</option>
                    <option value="contract">Contract</option>
                    <option value="internship">Internship</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" @click="closeModal">Cancel</button>
              <button type="submit" class="btn btn-primary">{{ editingJob ? 'Update Job' : 'Post Job' }}</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { jobApi } from '@/api/jobs'

const jobs = ref([])
const message = ref('')
const error = ref('')
const showModal = ref(false)
const editingJob = ref(null)

const emptyForm = { title: '', description: '', requirements: '', salary_min: '', salary_max: '', location: '', job_type: 'full-time' }
const form = ref({ ...emptyForm })

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function loadJobs() {
  const { data } = await jobApi.getMyJobs()
  jobs.value = data
}

function editJob(job) {
  editingJob.value = job
  form.value = { ...job }
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingJob.value = null
  form.value = { ...emptyForm }
}

async function handleSubmit() {
  try {
    if (editingJob.value) {
      await jobApi.updateJob(editingJob.value.id, form.value)
      message.value = 'Job updated successfully!'
    } else {
      await jobApi.createJob(form.value)
      message.value = 'Job created successfully!'
    }
    closeModal()
    await loadJobs()
  } catch {
    error.value = 'Failed to save job.'
  }
}

async function deleteJob(id) {
  if (!confirm('Delete this job?')) return
  await jobApi.deleteJob(id)
  message.value = 'Job deleted successfully!'
  await loadJobs()
}

onMounted(loadJobs)
</script>

