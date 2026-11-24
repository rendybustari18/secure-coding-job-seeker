<template>
  <div>
    <h2>My Skills</h2>
    <div v-if="message" class="alert alert-success">{{ message }}</div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <div class="row">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h5>{{ editingId ? 'Edit Skill' : 'Add New Skill' }}</h5>
          </div>
          <div class="card-body">
            <form @submit.prevent="handleSubmit">
              <div v-if="editingId" class="mb-3">
                <label class="form-label">Skill ID</label>
                <input v-model="editingId" type="number" class="form-control" />
                <small class="text-muted">Target ID skill yang diupdate</small>
              </div>
              <div class="mb-3">
                <label class="form-label">Skill Name</label>
                <input v-model="form.skill_name" type="text" class="form-control" required />
              </div>
              <div class="mb-3">
                <label class="form-label">Level</label>
                <select v-model="form.level" class="form-control" required>
                  <option value="beginner">Beginner</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                  <option value="expert">Expert</option>
                </select>
              </div>
              <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                  {{ editingId ? 'Update Skill' : 'Add Skill' }}
                </button>
                <button v-if="editingId" type="button" class="btn btn-secondary" @click="cancelEdit">Cancel</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card">
          <div class="card-header"><h5>My Skills</h5></div>
          <div class="card-body">
            <div v-for="skill in skills" :key="skill.id" class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
              <div>
                <strong>{{ skill.skill_name }}</strong> <small class="text-muted">(ID: {{ skill.id }})</small><br />
                <small class="text-muted">{{ skill.level }}</small>
              </div>
              <div>
                <button class="btn btn-sm btn-warning me-1" @click="startEdit(skill)">Edit</button>
                <button class="btn btn-sm btn-danger" @click="deleteSkill(skill.id)">Delete</button>
              </div>
            </div>
            <p v-if="skills.length === 0">No skills added yet.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { profileApi } from '@/api/profile'

const skills = ref([])
const message = ref('')
const error = ref('')
const editingId = ref(null)
const form = ref({ skill_name: '', level: 'beginner' })

async function loadSkills() {
  const { data } = await profileApi.getSkills()
  skills.value = data
}

function startEdit(skill) {
  editingId.value = skill.id
  form.value = { skill_name: skill.skill_name, level: skill.level }
}

function cancelEdit() {
  editingId.value = null
  form.value = { skill_name: '', level: 'beginner' }
}

async function handleSubmit() {
  try {
    message.value = ''
    error.value = ''
    if (editingId.value) {
      await profileApi.updateSkill(editingId.value, form.value)
      message.value = 'Skill updated successfully!'
    } else {
      await profileApi.addSkill(form.value)
      message.value = 'Skill added successfully!'
    }
    cancelEdit()
    await loadSkills()
  } catch {
    error.value = 'Failed to save skill.'
  }
}

async function deleteSkill(id) {
  if (!confirm('Delete skill?')) return
  try {
    await profileApi.deleteSkill(id)
    message.value = 'Skill deleted successfully!'
    await loadSkills()
  } catch {
    error.value = 'Failed to delete skill.'
  }
}

onMounted(loadSkills)
</script>
