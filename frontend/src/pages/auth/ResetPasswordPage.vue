<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4>Reset Password</h4></div>
          <div class="card-body">
            <div v-if="message" class="alert alert-success">
              {{ message }}
              <router-link to="/auth/login" class="btn btn-primary ms-2">Login Now</router-link>
            </div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>

            <form v-if="!message" @submit.prevent="handleReset">
              <input type="hidden" v-model="form.token" />
              <div class="mb-3">
                <label class="form-label">New Password</label>
                <input v-model="form.password" type="password" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input v-model="form.confirm_password" type="password" class="form-control" required />
              </div>
              <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
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
const form = ref({ token: '', password: '', confirm_password: '' })

onMounted(() => {
  form.value.token = route.query.token || ''
})

async function handleReset() {
  if (form.value.password !== form.value.confirm_password) {
    error.value = 'Passwords do not match.'
    return
  }

  try {
    await authApi.resetPassword(form.value)
    message.value = 'Password reset successfully! You can now login.'
  } catch {
    error.value = 'Invalid reset token.'
  }
}
</script>

