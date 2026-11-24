<template>
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h4>Forgot Password</h4></div>
          <div class="card-body">
            <div v-if="message" class="alert alert-success">{{ message }}</div>
            <div v-if="error" class="alert alert-danger">{{ error }}</div>

            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input v-model="form.email" type="email" class="form-control" required />
              </div>
              <input type="hidden" v-model="form.custom_header" />
              <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
            </form>

            <div class="text-center mt-3">
              <router-link to="/auth/login">Back to Login</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { authApi } from '@/api/auth'

const message = ref('')
const error = ref('')
const form = ref({ email: '', custom_header: '' })

async function handleSubmit() {
  try {
    message.value = ''
    error.value = ''
    await authApi.forgotPassword(form.value)
    message.value = 'Password reset link sent to your email.'
  } catch (e) {
    error.value = 'Email not found.'
  }
}
</script>

