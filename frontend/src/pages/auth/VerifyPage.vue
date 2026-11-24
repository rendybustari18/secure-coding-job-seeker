<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4>Email Verification</h4></div>
          <div class="card-body text-center">
            <div v-if="message" class="alert alert-success">
              {{ message }}
              <router-link to="/auth/login" class="btn btn-primary ms-2">Login Now</router-link>
            </div>
            <div v-if="error" class="alert alert-danger">
              {{ error }}
              <router-link to="/auth/register" class="btn btn-secondary ms-2">Register Again</router-link>
            </div>
            <div v-if="loading" class="spinner-border" role="status"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { authApi } from '@/api/auth'

const route = useRoute()
const message = ref('')
const error = ref('')
const loading = ref(true)

onMounted(async () => {
  const token = route.query.token

  if (!token) {
    error.value = 'No verification token provided.'
    loading.value = false
    return
  }

  try {
    await authApi.verify(token)
    message.value = 'Email verified successfully! You can now login.'
  } catch {
    error.value = 'Invalid verification token.'
  } finally {
    loading.value = false
  }
})
</script>

