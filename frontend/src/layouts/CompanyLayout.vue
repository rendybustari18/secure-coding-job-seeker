<template>
  <div>
    <TheNavbar />
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">
          <div class="sidebar p-3">
            <h5>Company Panel</h5>
            <ul class="nav flex-column">
              <li class="nav-item">
                <router-link class="nav-link" to="/company/dashboard">Dashboard</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" to="/company/jobs">Manage Jobs</router-link>
              </li>
              <li class="nav-item">
                <router-link class="nav-link" to="/company/applicants">Applicants</router-link>
              </li>
            </ul>
          </div>
        </div>
        <div class="col-md-9">
          <div class="main-content p-4">
            <router-view />
          </div>
        </div>
      </div>
    </div>
    <TheFooter />
  </div>
</template>

<script setup>
import TheNavbar from '@/components/common/TheNavbar.vue'
import TheFooter from '@/components/common/TheFooter.vue'
</script>

<style scoped>
.sidebar {
  min-height: 100vh;
  background-color: #f8f9fa;
}
</style>

