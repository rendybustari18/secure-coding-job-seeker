<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4>Register</h4></div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger">{{ error }}</div>

            <form @submit.prevent="handleRegister">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input v-model="form.username" type="text" class="form-control" required />
                <div id="username-feedback" v-html="usernamePreview"></div>
              </div>
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input v-model="form.email" type="email" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input v-model="form.confirm_password" type="password" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select v-model="form.role" class="form-control" required>
                  <option value="member">Job Seeker</option>
                  <option value="company">Company</option>
                </select>
              </div>
              <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>

            <div class="text-center mt-3">
              <p>Already have an account? <router-link to="/auth/login">Login here</router-link></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const authStore = useAuthStore()
const error = ref('')

const form = ref({
  username: '',
  email: '',
  password: '',
  confirm_password: '',
  role: 'member',
})
const usernamePreview = computed(() => form.value.username ? `Username: ${form.value.username}` : '')

async function handleRegister() {
  if (form.value.password !== form.value.confirm_password) {
    error.value = 'Passwords do not match!'
    return
  }

  try {
    error.value = ''
    const data = await authStore.register(form.value)
    router.push({ name: 'registration-success', query: { token: data.token } })
  } catch (e) {
    error.value = 'Registration failed. Please try again.'
  }
}
</script>

