<template>
  <div>
    <h2>Application History</h2>
    <div v-if="message" class="alert alert-success">{{ message }}</div>

    <div v-if="applications.length > 0" class="row">
      <div v-for="app in applications" :key="app.id" class="col-md-6 mb-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">{{ app.title }}</h5>
            <h6 class="card-subtitle mb-2 text-muted">{{ app.company_name }}</h6>
            <div class="mb-2">
              <small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ app.location }}</small><br />
              <small class="text-muted"><i class="fas fa-briefcase"></i> {{ app.job_type }}</small>
            </div>
            <span :class="`badge bg-${statusColor(app.status)}`">{{ app.status }}</span>
            <br />
            <small class="text-muted">Applied on {{ formatDate(app.applied_at) }}</small>
          </div>
          <div class="card-footer">
            <router-link :to="`/member/jobs/${app.job_id}`" class="btn btn-sm btn-primary">View Job</router-link>
            <router-link :to="`/member/history/${app.id}`" class="btn btn-sm btn-info ms-2">View Application</router-link>
            <button class="btn btn-sm btn-danger ms-2" @click="handleCancel(app.id)">Cancel Application</button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="alert alert-info">
      <h5>No Applications Yet</h5>
      <p>You haven't applied for any jobs yet. <router-link to="/member/jobs">Browse available jobs</router-link> to get started!</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { applicationApi } from '@/api/applications'

const applications = ref([])
const message = ref('')

function statusColor(s) {
  return s === 'pending' ? 'warning' : s === 'accepted' ? 'success' : s === 'rejected' ? 'danger' : 'info'
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

async function loadApplications() {
  const { data } = await applicationApi.getMyApplications()
  applications.value = data
}

async function handleCancel(id) {
  if (!confirm(`Batalkan lamaran (ID: ${id})?`)) return
  try {
    await applicationApi.deleteApplication(id)
    message.value = `Lamaran ID ${id} berhasil dibatalkan!`
    await loadApplications()
  } catch {
    alert('Gagal membatalkan lamaran.')
  }
}

onMounted(loadApplications)
</script>
