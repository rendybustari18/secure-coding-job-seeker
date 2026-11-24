<template>
  <div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><router-link to="/company/applicants">Applicants</router-link></li>
        <li class="breadcrumb-item active">Applicant Details</li>
      </ol>
    </nav>

    <div v-if="application" class="row">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <div class="d-flex align-items-center mb-4">
              <div>
                <h3 v-html="application.full_name || application.username"></h3>
                <p class="text-muted mb-1">{{ application.email }}</p>
                <p v-if="application.phone" class="text-muted mb-0">{{ application.phone }}</p>
                <p v-if="application.address" class="text-muted mb-0" v-html="application.address"></p>
              </div>
            </div>

            <h5>Applied for: {{ application.job_title }}</h5>
            <p class="text-muted">Applied on {{ formatDate(application.applied_at) }}</p>
            <span :class="`badge bg-${statusColor(application.status)} fs-6`">{{ application.status }}</span>

            <h5 class="mt-4">Cover Letter</h5>
            <div class="border p-3 rounded bg-light mb-4" v-html="application.cover_letter?.replace(/\n/g, '<br>')"></div>

            <h5>Skills</h5>
            <div v-if="application.skills?.length > 0" class="mb-4">
              <span v-for="s in application.skills" :key="s.id" class="badge bg-primary me-2 mb-2">
                <span v-html="s.skill_name"></span> ({{ s.level }})
              </span>
            </div>
            <p v-else class="text-muted mb-4">No skills listed.</p>

            <h5>Education</h5>
            <div v-if="application.education?.length > 0">
              <div v-for="edu in application.education" :key="edu.id" class="border rounded p-3 mb-3">
                <h6>{{ edu.degree }} in {{ edu.field_of_study }}</h6>
                <p class="mb-1"><strong>{{ edu.institution }}</strong></p>
                <small class="text-muted">{{ edu.start_date }} - {{ edu.end_date || 'Present' }}</small>
              </div>
            </div>
            <p v-else class="text-muted">No education records.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card">
          <div class="card-body">
            <h6>Actions</h6>
            <a v-if="application.cv_file" :href="`${apiBase}/${application.cv_file}`" target="_blank" class="btn btn-info w-100 mb-2">
              <i class="fas fa-file-pdf"></i> View CV
            </a>

            <div class="mb-3">
              <label class="form-label">Update Status</label>
              <select v-model="newStatus" class="form-control">
                <option value="pending">Pending</option>
                <option value="reviewed">Reviewed</option>
                <option value="accepted">Accepted</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label">Applicant Role</label>
              <input v-model="targetRole" type="text" class="form-control" />
              <small class="text-muted">Peran pengguna pelamar saat status diperbarui.</small>
            </div>

            <div v-if="updateSuccessMessage" class="alert alert-success py-2 mb-3">
              {{ updateSuccessMessage }}
            </div>

            <button class="btn btn-primary w-100 mb-2" @click="handleUpdateStatus">Update Status</button>
            <router-link to="/company/applicants" class="btn btn-secondary w-100">Back to Applicants</router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { applicationApi } from '@/api/applications'

const route = useRoute()
const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8004'
const application = ref(null)
const newStatus = ref('accepted')
const targetRole = ref('company')
const updateSuccessMessage = ref('')

function statusColor(s) {
  return s === 'pending' ? 'warning' : s === 'accepted' ? 'success' : s === 'rejected' ? 'danger' : 'info'
}

function formatDate(d) {
  return new Date(d).toLocaleString('en-US')
}

async function handleUpdateStatus() {
  const payload = {
    status: newStatus.value,
    role: targetRole.value,
  }
  const { data } = await applicationApi.updateStatus(route.params.id, payload)
  application.value.status = newStatus.value
  updateSuccessMessage.value = data?.message || `Status updated to '${newStatus.value}' with role '${targetRole.value}'!`
}

onMounted(async () => {
  const { data } = await applicationApi.getApplicantDetail(route.params.id)
  application.value = data
  if (data?.status) {
    newStatus.value = 'accepted'
  }
})
</script>

