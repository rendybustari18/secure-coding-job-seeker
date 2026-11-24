<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <router-link class="navbar-brand" to="/">
        <i class="fas fa-briefcase"></i> Job Portal
      </router-link>

      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/jobs">Jobs</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/analytics">Analytics</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/docs">API Docs</router-link>
          </li>
        </ul>

        <ul class="navbar-nav">
          <template v-if="authStore.isLoggedIn">
            <li class="nav-item dropdown">
              <a
                class="nav-link dropdown-toggle"
                href="#"
                data-bs-toggle="dropdown"
              >
                {{ authStore.user?.username }}
              </a>
              <ul class="dropdown-menu">
                <template v-if="authStore.isMember">
                  <li><router-link class="dropdown-item" to="/member/dashboard">Dashboard</router-link></li>
                  <li><router-link class="dropdown-item" to="/member/profile">Profile</router-link></li>
                  <li><router-link class="dropdown-item" to="/member/cv">CV</router-link></li>
                  <li><router-link class="dropdown-item" to="/member/jobs">Jobs</router-link></li>
                  <li><router-link class="dropdown-item" to="/member/history">History</router-link></li>
                </template>
                <template v-else-if="authStore.isCompany">
                  <li><router-link class="dropdown-item" to="/company/dashboard">Dashboard</router-link></li>
                  <li><router-link class="dropdown-item" to="/company/jobs">Manage Jobs</router-link></li>
                  <li><router-link class="dropdown-item" to="/company/applicants">Applicants</router-link></li>
                </template>
                <li><hr class="dropdown-divider" /></li>
                <li>
                  <a class="dropdown-item" href="#" @click.prevent="logout">Logout</a>
                </li>
              </ul>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <router-link class="nav-link" to="/auth/login">Login</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/auth/register">Register</router-link>
            </li>
          </template>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Search bar -->
  <div class="container mt-3">
    <div class="row">
      <div class="col-md-8 mx-auto">
        <form class="d-flex" @submit.prevent="search">
          <input
            v-model="searchQuery"
            class="form-control me-2"
            type="search"
            placeholder="Search jobs..."
          />
          <button class="btn btn-outline-success" type="submit">Search</button>
        </form>
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
const searchQuery = ref('')

function search() {
  router.push({ path: '/jobs', query: { search: searchQuery.value } })
}

function logout() {
  authStore.logout()
  router.push('/')
}
</script>

