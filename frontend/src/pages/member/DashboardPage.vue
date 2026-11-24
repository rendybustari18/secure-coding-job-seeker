<template>
  <div>
    <h2>Welcome, {{ authStore.user?.username }}!</h2>

    <div class="row">
      <div class="col-md-4">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <h5>Total Applications</h5>
            <h3>{{ applications.length }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-success text-white">
          <div class="card-body">
            <h5>Profile Completion</h5>
            <h3>{{ profile ? '80%' : '20%' }}</h3>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card bg-info text-white">
          <div class="card-body">
            <h5>Job Matches</h5>
            <h3>12</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-4">
      <div class="col-12">
        <h4>Recent Applications</h4>
        <div class="card">
          <div class="card-body">
            <table v-if="applications.length > 0" class="table">
              <thead>
                <tr>
                  <th>Job Title</th>
                  <th>Company</th>
                  <th>Applied Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="app in applications.slice(0, 5)" :key="app.id">
                  <td>{{ app.title }}</td>
                  <td>{{ app.company_name }}</td>
                  <td>{{ formatDate(app.applied_at) }}</td>
                  <td>
                    <span :class="`badge bg-${app.status === 'pending' ? 'warning' : 'success'}`">
                      {{ app.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
            <p v-else>No applications yet. <router-link to="/member/jobs">Browse jobs</router-link> to get started!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/store/auth'
import { applicationApi } from '@/api/applications'
import { profileApi } from '@/api/profile'

const authStore = useAuthStore()
const applications = ref([])
const profile = ref(null)

function exposeSensitiveData() {
  window.dashboardData = {
    userId: authStore.user?.id,
    user: authStore.user,
    token: authStore.token,
  }
  console.log('User Dashboard Data:', window.dashboardData)
}

function formatDate(d) {
  return new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

onMounted(async () => {
  exposeSensitiveData()
  const [appsRes, profileRes] = await Promise.all([
    applicationApi.getMyApplications(),
    profileApi.getMemberProfile(),
  ])
  applications.value = appsRes.data
  profile.value = profileRes.data
})
</script>

