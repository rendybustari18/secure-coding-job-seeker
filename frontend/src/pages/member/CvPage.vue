<template>
  <div>
    <h2>My CV</h2>
    <div v-if="message" class="alert alert-success">{{ message }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="card">
      <div class="card-body">
        <form @submit.prevent="handleUpload" enctype="multipart/form-data">
          <div class="mb-3">
            <label class="form-label">Upload CV</label>
            <input type="file" class="form-control" @change="onFileChange" accept=".pdf,application/pdf" />
            <div class="form-text text-muted">Hanya file PDF (.pdf) yang diperbolehkan.</div>
          </div>

          <div v-if="profile?.cv_file" class="mb-3">
            <label class="form-label">Current CV</label>
            <div class="border p-3 rounded">
              <i class="fas fa-file-pdf text-danger"></i>
              <a :href="`${apiBase}/${profile.cv_file}`" target="_blank" class="ms-2 btn btn-outline-info">
                <i class="fas fa-external-link-alt"></i> View Public CV Link
              </a>
              <button type="button" class="btn btn-sm btn-danger ms-2" @click="deleteCv">Delete</button>
            </div>
          </div>

          <button type="submit" class="btn btn-primary" :disabled="!cvFile">Upload CV</button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { profileApi } from '@/api/profile'

const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8004'
const profile = ref(null)
const message = ref('')
const error = ref('')
const cvFile = ref(null)

function onFileChange(e) {
  const file = e.target.files[0]
  if (!file) {
    cvFile.value = null
    return
  }
  if (!file.name.toLowerCase().endsWith('.pdf') && file.type !== 'application/pdf') {
    error.value = 'Validasi gagal: Hanya file berekstensi .pdf yang diperbolehkan!'
    cvFile.value = null
    e.target.value = ''
    return
  }
  error.value = ''
  cvFile.value = file
}

async function handleUpload() {
  if (!cvFile.value) return
  try {
    const fd = new FormData()
    fd.append('cv_file', cvFile.value)
    await profileApi.uploadCv(fd)
    message.value = 'CV uploaded successfully!'
    await loadProfile()
  } catch {
    error.value = 'Failed to upload CV.'
  }
}

async function deleteCv() {
  if (!confirm('Delete CV?')) return
  try {
    const targetId = profile.value?.id || profile.value?.user_id
    await profileApi.deleteCv(targetId)
    profile.value.cv_file = null
    message.value = 'CV deleted successfully!'
  } catch {
    error.value = 'Failed to delete CV.'
  }
}

async function loadProfile() {
  const { data } = await profileApi.getMemberProfile()
  profile.value = data
}

onMounted(loadProfile)
</script>
