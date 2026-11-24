<template>
  <div>
    <h2>Education</h2>
    <div v-if="message" class="alert alert-success">{{ message }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="card mb-4">
      <div class="card-header">
        <h5>{{ editingId ? 'Edit Education' : 'Add Education' }}</h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="handleSubmit">
          <div v-if="editingId" class="mb-3">
            <label class="form-label">Education ID</label>
            <input v-model="editingId" type="number" class="form-control" />
            <small class="text-muted">Target ID pendidikan yang diupdate</small>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Institution</label>
              <input v-model="form.institution" type="text" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Degree</label>
              <input v-model="form.degree" type="text" class="form-control" required />
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Field of Study</label>
            <input v-model="form.field_of_study" type="text" class="form-control" required />
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Start Date</label>
              <input v-model="form.start_date" type="date" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">End Date</label>
              <input v-model="form.end_date" type="date" class="form-control" />
            </div>
          </div>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              {{ editingId ? 'Update Education' : 'Add Education' }}
            </button>
            <button v-if="editingId" type="button" class="btn btn-secondary" @click="cancelEdit">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h5>My Education</h5></div>
      <div class="card-body">
        <div v-for="edu in education" :key="edu.id" class="border rounded p-3 mb-3">
          <div class="d-flex justify-content-between">
            <div>
              <h6>{{ edu.degree }} in {{ edu.field_of_study }} <small class="text-muted">(ID: {{ edu.id }})</small></h6>
              <p class="mb-1"><strong>{{ edu.institution }}</strong></p>
              <small class="text-muted">{{ edu.start_date }} - {{ edu.end_date || 'Present' }}</small>
            </div>
            <div>
              <button class="btn btn-sm btn-warning me-1" @click="startEdit(edu)">Edit</button>
              <button class="btn btn-sm btn-danger" @click="deleteEdu(edu.id)">Delete</button>
            </div>
          </div>
        </div>
        <p v-if="education.length === 0">No education records added yet.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { profileApi } from '@/api/profile'

const education = ref([])
const message = ref('')
const error = ref('')
const editingId = ref(null)
const emptyForm = { institution: '', degree: '', field_of_study: '', start_date: '', end_date: '' }
const form = ref({ ...emptyForm })

async function loadEducation() {
  const { data } = await profileApi.getEducation()
  education.value = data
}

function startEdit(edu) {
  editingId.value = edu.id
  form.value = {
    institution: edu.institution,
    degree: edu.degree,
    field_of_study: edu.field_of_study,
    start_date: edu.start_date,
    end_date: edu.end_date || '',
  }
}

function cancelEdit() {
  editingId.value = null
  form.value = { ...emptyForm }
}

async function handleSubmit() {
  try {
    message.value = ''
    error.value = ''
    if (editingId.value) {
      await profileApi.updateEducation(editingId.value, form.value)
      message.value = 'Education updated successfully!'
    } else {
      await profileApi.addEducation(form.value)
      message.value = 'Education added successfully!'
    }
    cancelEdit()
    await loadEducation()
  } catch {
    error.value = 'Failed to save education.'
  }
}

async function deleteEdu(id) {
  if (!confirm('Delete education?')) return
  try {
    await profileApi.deleteEducation(id)
    message.value = 'Education deleted successfully!'
    await loadEducation()
  } catch {
    error.value = 'Failed to delete education.'
  }
}

onMounted(loadEducation)
</script>
