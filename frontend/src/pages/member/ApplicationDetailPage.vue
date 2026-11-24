<template>
  <div class="container-fluid py-2">
    <!-- Breadcrumb & Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><router-link to="/member/history">History Lamaran</router-link></li>
          <li class="breadcrumb-item active">Detail Lamaran #{{ route.params.id }}</li>
        </ol>
      </nav>
      <div class="d-flex align-items-center gap-2">
        <div class="input-group input-group-sm" style="width: 220px;">
          <span class="input-group-text bg-white">ID</span>
          <input
            v-model.number="targetDetailId"
            type="number"
            class="form-control"
            placeholder="Cari ID lain..."
            @keyup.enter="goToDetail(targetDetailId)"
          />
          <button class="btn btn-outline-secondary" type="button" @click="goToDetail(targetDetailId)">
            Buka
          </button>
        </div>
      </div>
    </div>

    <!-- Alert Notifikasi -->
    <div v-if="alertMessage" :class="`alert alert-${alertType} alert-dismissible fade show`" role="alert">
      {{ alertMessage }}
      <button type="button" class="btn-close" @click="alertMessage = ''"></button>
    </div>

    <!-- Loading & Error States -->
    <div v-if="loading" class="text-center py-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2 text-muted">Memuat detail lamaran...</p>
    </div>

    <div v-else-if="error" class="card shadow-sm border-0 my-4 text-center py-5">
      <div class="card-body">
        <i class="bi bi-exclamation-triangle text-warning display-4"></i>
        <h4 class="mt-3 text-danger">{{ error }}</h4>
        <p class="text-muted">Lamaran dengan ID #{{ route.params.id }} tidak ditemukan atau gagal dimuat.</p>
        <div class="d-flex justify-content-center gap-2 mt-3">
          <router-link to="/member/history" class="btn btn-secondary">Kembali ke History</router-link>
          <button class="btn btn-outline-primary" @click="goToDetail(1)">Lihat ID 1</button>
          <button class="btn btn-outline-primary" @click="goToDetail(2)">Lihat ID 2</button>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else-if="application">
      <!-- Header Banner & Action Bar -->
      <div class="card shadow-sm mb-4 border-0">
        <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
          <div>
            <div class="d-flex align-items-center gap-2">
              <h3 class="mb-0">{{ application.title }}</h3>
              <span :class="`badge bg-${statusColor(application.status)} fs-6`">
                {{ application.status?.toUpperCase() }}
              </span>
            </div>
            <div class="text-muted mt-1">
              <i class="bi bi-building me-1"></i><strong>{{ application.company_name || 'Perusahaan' }}</strong>
              <span class="mx-2">•</span>
              <i class="bi bi-geo-alt me-1"></i>{{ application.location || '-' }}
              <span class="mx-2">•</span>
              <i class="bi bi-clock me-1"></i>Diajukan pada: {{ formatDate(application.applied_at) }}
            </div>
          </div>

          <!-- Fitur Pembatalan Applied Berdasarkan ID -->
          <div class="d-flex flex-wrap align-items-center gap-2">
            <button
              class="btn btn-danger btn-sm px-3"
              :disabled="cancelLoading"
              @click="cancelApplication(application.id)"
            >
              <i class="bi bi-x-circle me-1"></i>
              Batalkan Lamaran Ini (ID: {{ application.id }})
            </button>

          </div>
        </div>
      </div>

      <div class="row g-4">
        <!-- Kolom Kiri: Data Lamaran & Pekerjaan -->
        <div class="col-lg-7">
          <!-- Cover Letter -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
              <i class="bi bi-file-earmark-text text-primary me-2"></i>Cover Letter Pelamar
            </div>
            <div class="card-body">
              <div
                v-if="application.cover_letter"
                class="p-3 bg-light rounded border text-break"
                v-html="application.cover_letter?.replace(/\n/g, '<br>')"
              ></div>
              <div v-else class="text-muted fst-italic">Tidak ada cover letter yang dilampirkan.</div>
            </div>
          </div>

          <!-- Deskripsi Pekerjaan -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
              <i class="bi bi-briefcase text-primary me-2"></i>Deskripsi Lowongan
            </div>
            <div class="card-body">
              <div v-if="application.description" class="text-break mb-3" v-html="application.description"></div>
              <div v-else class="text-muted">Tidak ada deskripsi pekerjaan.</div>

              <div v-if="application.requirements" class="mt-3">
                <h6 class="fw-bold">Persyaratan:</h6>
                <div class="text-break" v-html="application.requirements"></div>
              </div>

              <hr />
              <div class="row g-2 text-muted small">
                <div class="col-sm-6">
                  <strong>Tipe Pekerjaan:</strong> {{ application.job_type || 'Full-time' }}
                </div>
                <div class="col-sm-6">
                  <strong>Estimasi Gaji:</strong>
                  {{ formatCurrency(application.salary_min) }} - {{ formatCurrency(application.salary_max) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Kolom Kanan: Informasi Pelamar Lengkap (Profil, CV, Skills, Education) -->
        <div class="col-lg-5">
          <!-- Profil Pelamar -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
              <span><i class="bi bi-person-badge text-primary me-2"></i>Profil Pelamar</span>
              <span class="badge bg-secondary">User ID: {{ application.user_id }}</span>
            </div>
            <div class="card-body">
              <div class="d-flex align-items-center gap-3 mb-3">
                <img
                  :src="applicantPhotoUrl"
                  alt="Foto Pelamar"
                  class="rounded-circle border"
                  style="width: 64px; height: 64px; object-fit: cover;"
                  @error="onPhotoError"
                />
                <div>
                  <h5 class="mb-0">{{ application.full_name || application.username || 'Pengguna' }}</h5>
                  <div class="text-muted small">@{{ application.username }}</div>
                  <div class="text-muted small">{{ application.email }}</div>
                </div>
              </div>

              <ul class="list-group list-group-flush small">
                <li class="list-group-item px-0 d-flex justify-content-between">
                  <span class="text-muted">Nomor Telepon:</span>
                  <span class="fw-semibold">{{ application.phone || '-' }}</span>
                </li>
                <li class="list-group-item px-0 d-flex justify-content-between">
                  <span class="text-muted">Alamat:</span>
                  <span class="fw-semibold text-end" style="max-width: 60%;">{{ application.address || '-' }}</span>
                </li>
                <li class="list-group-item px-0" v-if="application.bio">
                  <div class="text-muted mb-1">Bio:</div>
                  <div class="bg-light p-2 rounded text-break">{{ application.bio }}</div>
                </li>
              </ul>
            </div>
          </div>

          <!-- Dokumen CV -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3">
              <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Berkas CV Pelamar
            </div>
            <div class="card-body">
              <div v-if="application.cv_file" class="d-flex align-items-center justify-content-between p-2 border rounded bg-light">
                <div class="d-flex align-items-center gap-2 text-truncate me-2">
                  <i class="bi bi-file-earmark-arrow-down fs-4 text-primary"></i>
                  <div class="text-truncate">
                    <div class="fw-semibold text-truncate small">{{ cvFileName }}</div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle small">Terlampir</span>
                  </div>
                </div>
                <a
                  :href="cvDownloadUrl"
                  target="_blank"
                  rel="noopener noreferrer"
                  class="btn btn-primary btn-sm text-nowrap"
                >
                  <i class="bi bi-box-arrow-up-right me-1"></i>Unduh CV
                </a>
              </div>
              <div v-else class="text-muted fst-italic small">
                <i class="bi bi-info-circle me-1"></i>Pelamar belum mengunggah dokumen CV.
              </div>
            </div>
          </div>

          <!-- Keahlian (Skills) -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
              <span><i class="bi bi-tools text-primary me-2"></i>Keahlian (Skills)</span>
              <span class="badge bg-light text-dark border">{{ application.skills?.length || 0 }} Keahlian</span>
            </div>
            <div class="card-body">
              <div v-if="application.skills && application.skills.length > 0" class="d-flex flex-wrap gap-2">
                <span
                  v-for="skill in application.skills"
                  :key="skill.id"
                  class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2"
                >
                  <span class="fw-bold">{{ skill.skill_name }}</span>
                  <span class="badge bg-secondary text-uppercase" style="font-size: 0.7rem;">
                    {{ skill.level || skill.proficiency || 'intermediate' }}
                  </span>
                </span>
              </div>
              <div v-else class="text-muted fst-italic small">
                <i class="bi bi-info-circle me-1"></i>Tidak ada data keahlian yang tercatat.
              </div>
            </div>
          </div>

          <!-- Riwayat Pendidikan (Education) -->
          <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white fw-bold py-3 d-flex justify-content-between align-items-center">
              <span><i class="bi bi-mortarboard text-primary me-2"></i>Riwayat Pendidikan</span>
              <span class="badge bg-light text-dark border">{{ application.education?.length || 0 }} Riwayat</span>
            </div>
            <div class="card-body p-0">
              <ul v-if="application.education && application.education.length > 0" class="list-group list-group-flush">
                <li
                  v-for="edu in application.education"
                  :key="edu.id"
                  class="list-group-item p-3"
                >
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <h6 class="mb-0 fw-bold">{{ edu.institution }}</h6>
                      <div class="text-primary small fw-semibold">
                        {{ edu.degree }} {{ edu.field_of_study ? `- ${edu.field_of_study}` : '' }}
                      </div>
                    </div>
                    <span class="badge bg-light text-muted border small">
                      {{ formatYear(edu.start_date || edu.year_start) }} - {{ formatYear(edu.end_date || edu.year_end) || 'Sekarang' }}
                    </span>
                  </div>
                </li>
              </ul>
              <div v-else class="p-3 text-muted fst-italic small">
                <i class="bi bi-info-circle me-1"></i>Tidak ada riwayat pendidikan yang tercatat.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { applicationApi } from '@/api/applications'

const route = useRoute()
const router = useRouter()

const application = ref(null)
const loading = ref(true)
const cancelLoading = ref(false)
const error = ref('')
const alertMessage = ref('')
const alertType = ref('success')

const targetDetailId = ref(Number(route.params.id) || null)
const customCancelId = ref(null)

const API_BASE = 'http://localhost:8004'

const applicantPhotoUrl = computed(() => {
  if (application.value?.profile_photo) {
    const photo = application.value.profile_photo
    return photo.startsWith('http') ? photo : `${API_BASE}/${photo.replace(/^\/+/, '')}`
  }
  return 'https://ui-avatars.com/api/?name=' + encodeURIComponent(application.value?.full_name || application.value?.username || 'User') + '&background=0D8ABC&color=fff'
})

function onPhotoError(e) {
  e.target.src = 'https://ui-avatars.com/api/?name=User&background=6c757d&color=fff'
}

const cvFileName = computed(() => {
  if (!application.value?.cv_file) return ''
  const parts = application.value.cv_file.split('/')
  return parts[parts.length - 1]
})

const cvDownloadUrl = computed(() => {
  if (!application.value?.cv_file) return '#'
  const path = application.value.cv_file
  return path.startsWith('http') ? path : `${API_BASE}/${path.replace(/^\/+/, '')}`
})

function statusColor(s) {
  const map = {
    pending: 'warning',
    reviewed: 'info',
    reviewing: 'info',
    interviewed: 'primary',
    accepted: 'success',
    rejected: 'danger'
  }
  return map[s] || 'secondary'
}

function formatDate(d) {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function formatYear(val) {
  if (!val) return ''
  if (typeof val === 'number') return val
  return val.split('-')[0] || val
}

function formatCurrency(val) {
  if (!val) return 'Rp 0'
  const num = Number(val)
  return isNaN(num) ? val : 'Rp ' + num.toLocaleString('id-ID')
}

async function loadDetail(id) {
  loading.value = true
  error.value = ''
  try {
    const { data } = await applicationApi.getApplication(id)
    application.value = data
    targetDetailId.value = Number(id)
  } catch (err) {
    application.value = null
    error.value = err.response?.data?.error || 'Gagal memuat detail lamaran.'
  } finally {
    loading.value = false
  }
}

function goToDetail(id) {
  if (!id) return
  router.push(`/member/history/${id}`)
}

async function cancelApplication(id) {
  if (!id) return
  const confirmCancel = confirm(`Apakah Anda yakin ingin membatalkan lamaran dengan ID #${id}?`)
  if (!confirmCancel) return

  cancelLoading.value = true
  try {
    const { data } = await applicationApi.deleteApplication(id)
    alertType.value = 'success'
    alertMessage.value = data.message || `Lamaran #${id} berhasil dibatalkan.`
    
    // Jika membatalkan lamaran yang sedang dibuka, arahkan kembali ke history
    if (Number(id) === Number(route.params.id)) {
      setTimeout(() => {
        router.push('/member/history')
      }, 1200)
    } else {
      // Refresh current detail
      await loadDetail(route.params.id)
    }
  } catch (err) {
    alertType.value = 'danger'
    alertMessage.value = err.response?.data?.error || `Gagal membatalkan lamaran #${id}.`
  } finally {
    cancelLoading.value = false
  }
}

watch(
  () => route.params.id,
  (newId) => {
    if (newId) {
      loadDetail(newId)
    }
  }
)

onMounted(() => {
  if (route.params.id) {
    loadDetail(route.params.id)
  }
})
</script>
