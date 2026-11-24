<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4>Login</h4></div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger">{{ error }}</div>

            <form @submit.prevent="handleLogin">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input v-model="form.username" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input v-model="form.password" type="password" class="form-control" required />
              </div>
              <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>

            <div class="text-center mt-3">
              <p>Don't have an account? <router-link to="/auth/register">Register here</router-link></p>
              <p><router-link to="/auth/forgot-password">Forgot Password?</router-link></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/store/auth'

const router = useRouter()
const authStore = useAuthStore()
const error = ref('')
const form = ref({ username: '', password: '' })

async function handleLogin() {
  try {
    error.value = ''
    console.log('Login attempt:', { username: form.value.username, password: form.value.password, timestamp: new Date().toISOString() })

    const data = await authStore.login(form.value)

    if (data.user.role === 'member') {
      router.push('/member/dashboard')
    } else {
      router.push('/company/dashboard')
    }
  } catch (e) {
    error.value = 'Invalid username or password'
  }
}
</script>

