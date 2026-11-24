<template>
  <div>
    <h2>Job Applicants</h2>

    <div class="card">
      <div class="card-body">
        <div v-if="applicants.length > 0" class="table-responsive">
          <table class="table">
            <thead>
              <tr><th>Applicant</th><th>Job</th><th>Applied Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
              <tr v-for="app in applicants" :key="app.id">
                <td>
                  <strong>{{ app.full_name || app.username }}</strong><br />
                  <small class="text-muted">{{ app.email }}</small>
                </td>
                <td>{{ app.job_title }}</td>
                <td>{{ formatDate(app.applied_at) }}</td>
                <td><span :class="`badge bg-${statusColor(app.status)}`">{{ app.status }}</span></td>
                <td>
                  <router-link :to="`/company/applicants/${app.id}`" class="btn btn-sm btn-primary me-1">View Details</router-link>
                  <div class="btn-group">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">Update Status</button>
                    <ul class="dropdown-menu">
                      <li><button class="dropdown-item" @click="updateStatus(app.id, 'reviewed')">Mark as Reviewed</button></li>
                      <li><button class="dropdown-item text-success" @click="updateStatus(app.id, 'accepted')">Accept</button></li>
                      <li><button class="dropdown-item text-danger" @click="updateStatus(app.id, 'rejected')">Reject</button></li>
                    </ul>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div v-else class="text-center py-5">
          <i class="fas fa-users fa-3x text-muted mb-3"></i>
          <h5>No Applications Yet</h5>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { applicationApi } from '@/api/applications'

const applicants = ref([])

function statusColor(s) {
  return s === 'pending' ? 'warning' : s === 'accepted' ? 'success' : s === 'rejected' ? 'danger' : 'info'
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function loadApplicants() {
  const { data } = await applicationApi.getCompanyApplicants()
  applicants.value = data
}

async function updateStatus(id, status) {
  await applicationApi.updateStatus(id, status)
  await loadApplicants()
}

onMounted(loadApplicants)
</script>

