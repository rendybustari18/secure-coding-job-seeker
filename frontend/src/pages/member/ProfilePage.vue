<template>
  <div>
    <h2>My Profile</h2>
    <div v-if="message" class="alert alert-success">{{ message }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="handleSubmit" enctype="multipart/form-data">

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input v-model="form.full_name" type="text" class="form-control" />
                <div v-html="namePreview"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input v-model="form.phone" type="text" class="form-control" />
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label">Address</label>
            <textarea v-model="form.address" class="form-control" rows="3"></textarea>
          </div>

          <div class="mb-3">
            <label class="form-label">Profile Photo</label>
            <div v-if="profile?.profile_photo" class="mb-2">
              <p class="text-muted">Current Photo:</p>
              <img :src="`${apiBase}/${profile.profile_photo}`" class="img-thumbnail" style="max-width:200px" />
            </div>
            <input type="file" class="form-control" @change="onPhotoChange" accept="image/*,.pdf" />
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Update Profile</button>
            <button v-if="form.id" type="button" class="btn btn-danger" @click="handleDeleteProfile">Delete Profile</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { profileApi } from '@/api/profile'

const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8004'
const profile = ref(null)
const message = ref('')
const error = ref('')
const photoFile = ref(null)

const form = ref({ id: '', full_name: '', phone: '', address: '' })
const namePreview = computed(() => form.value.full_name ? `Hello, ${form.value.full_name}` : '')

function onPhotoChange(e) {
  photoFile.value = e.target.files[0]
}

async function handleSubmit() {
  try {
    message.value = ''
    error.value = ''
    const targetId = form.value.id || profile.value?.id || null
    await profileApi.updateMemberProfile(targetId, form.value)

    if (photoFile.value) {
      const fd = new FormData()
      fd.append('profile_photo', photoFile.value)
      await profileApi.uploadPhoto(fd)
    }

    const { data } = await profileApi.getMemberProfile()
    if (data) {
      profile.value = data
      form.value.id        = data.id        || ''
      form.value.full_name = data.full_name || ''
      form.value.phone     = data.phone     || ''
      form.value.address   = data.address   || ''
    }

    message.value = 'Profile updated successfully!'
  } catch {
    error.value = 'Failed to update profile.'
  }
}

async function handleDeleteProfile() {
  if (!confirm('Hapus profil ini?')) return
  try {
    await profileApi.deleteMemberProfile(form.value.id)
    message.value = 'Profile deleted successfully!'
  } catch {
    error.value = 'Failed to delete profile.'
  }
}

onMounted(async () => {
  const { data } = await profileApi.getMemberProfile()
  profile.value = data
  form.value.id        = data.id        || ''
  form.value.full_name = data.full_name || ''
  form.value.phone     = data.phone     || ''
  form.value.address   = data.address   || ''
})
</script>
