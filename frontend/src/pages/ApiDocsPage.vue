<template>
  <div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
      <div>
        <h1 class="h2 mb-1"><i class="fas fa-book-open text-primary me-2"></i>JobSeeker Portal - Public API Documentation</h1>
        <p class="text-muted mb-0">
          Dokumentasi REST API resmi untuk modul <strong>Member</strong>, <strong>Company</strong>, dan <strong>Admin</strong>.
        </p>
      </div>
      <div>
        <span class="badge bg-success fs-6"><i class="fas fa-server me-1"></i>Base URL: http://localhost:8004/api</span>
      </div>
    </div>

    <!-- Category Tabs -->
    <ul class="nav nav-pills mb-4">
      <li class="nav-item">
        <button
          class="nav-link"
          :class="{ active: activeCategory === 'member' }"
          @click="activeCategory = 'member'"
        >
          <i class="fas fa-user me-1"></i> Member API ({{ memberEndpoints.length }})
        </button>
      </li>
      <li class="nav-item">
        <button
          class="nav-link"
          :class="{ active: activeCategory === 'company' }"
          @click="activeCategory = 'company'"
        >
          <i class="fas fa-building me-1"></i> Company API ({{ companyEndpoints.length }})
        </button>
      </li>
      <li class="nav-item">
        <button
          class="nav-link"
          :class="{ active: activeCategory === 'admin' }"
          @click="activeCategory = 'admin'"
        >
          <i class="fas fa-user-shield me-1"></i> Admin API ({{ adminEndpoints.length }})
        </button>
      </li>
    </ul>

    <!-- Filter input -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-search"></i></span>
          <input
            v-model="searchTerm"
            type="text"
            class="form-control"
            placeholder="Cari endpoint atau kata kunci (contoh: /jobs, status, profile)..."
          />
        </div>
      </div>
    </div>

    <!-- Endpoint Cards List -->
    <div class="accordion" id="apiDocsAccordion">
      <div
        v-for="(item, idx) in filteredEndpoints"
        :key="idx"
        class="card mb-3 shadow-sm border-0"
      >
        <div class="card-header bg-white d-flex align-items-center justify-content-between py-3">
          <div class="d-flex align-items-center gap-3">
            <span :class="methodBadgeClass(item.method)" class="badge px-3 py-2 fs-6 text-uppercase">
              {{ item.method }}
            </span>
            <code class="fs-5 text-dark font-monospace">{{ item.path }}</code>
          </div>
          <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary">{{ item.auth ? 'Bearer Auth' : 'Public' }}</span>
            <span class="badge bg-info text-dark">Role: {{ item.role }}</span>
            <button
              class="btn btn-sm btn-outline-primary"
              type="button"
              data-bs-toggle="collapse"
              :data-bs-target="'#collapse-' + idx"
              aria-expanded="false"
            >
              Detail <i class="fas fa-chevron-down ms-1"></i>
            </button>
          </div>
        </div>

        <div :id="'collapse-' + idx" class="collapse" :class="{ show: idx === 0 }">
          <div class="card-body bg-light border-top">
            <p class="mb-3"><strong>Deskripsi:</strong> {{ item.description }}</p>

            <div class="row g-3">
              <!-- Request Spec -->
              <div class="col-lg-6">
                <div class="card h-100 border">
                  <div class="card-header bg-white py-2">
                    <strong><i class="fas fa-arrow-circle-right text-primary me-1"></i> Request Specification</strong>
                  </div>
                  <div class="card-body">
                    <p class="mb-1 text-muted small"><strong>Headers:</strong></p>
                    <pre class="bg-dark text-light p-2 rounded small"><code>{{ formatJson(item.headers) }}</code></pre>

                    <div v-if="item.requestBody">
                      <p class="mb-1 text-muted small"><strong>Request Body:</strong></p>
                      <pre class="bg-dark text-light p-2 rounded small"><code>{{ formatJson(item.requestBody) }}</code></pre>
                    </div>
                    <div v-else class="text-muted small">
                      <em>Tidak membutuhkan request body.</em>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Response Spec -->
              <div class="col-lg-6">
                <div class="card h-100 border">
                  <div class="card-header bg-white py-2">
                    <strong><i class="fas fa-arrow-circle-left text-success me-1"></i> Response Specification</strong>
                  </div>
                  <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                      <span class="badge bg-success">Success Code: {{ item.statusCode }}</span>
                      <span class="badge bg-danger">Error Code: {{ item.errorCode }}</span>
                    </div>

                    <p class="mb-1 text-muted small"><strong>Contoh Success Response:</strong></p>
                    <pre class="bg-dark text-light p-2 rounded small"><code>{{ formatJson(item.successResponse) }}</code></pre>

                    <p class="mb-1 text-muted small"><strong>Contoh Error Response:</strong></p>
                    <pre class="bg-dark text-light p-2 rounded small"><code>{{ formatJson(item.errorResponse) }}</code></pre>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="filteredEndpoints.length === 0" class="text-center py-5 text-muted">
        <i class="fas fa-inbox fa-3x mb-3"></i>
        <p>Tidak ada endpoint yang sesuai dengan pencarian Anda.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const activeCategory = ref('member')
const searchTerm = ref('')

function methodBadgeClass(method) {
  switch (method) {
    case 'GET': return 'bg-success'
    case 'POST': return 'bg-primary'
    case 'PUT': return 'bg-warning text-dark'
    case 'DELETE': return 'bg-danger'
    default: return 'bg-secondary'
  }
}

function formatJson(val) {
  if (typeof val === 'string') return val
  return JSON.stringify(val, null, 2)
}

// ─────────────────────────────────────────────────────────────
// Member Endpoints
// ─────────────────────────────────────────────────────────────
const memberEndpoints = [
  {
    path: '/api/auth/register',
    method: 'POST',
    description: 'Registrasi akun baru untuk member atau pengguna umum',
    role: 'Public / Unrestricted',
    auth: false,
    headers: { 'Content-Type': 'application/json' },
    requestBody: {
      username: 'johndoe',
      email: 'johndoe@example.com',
      password: 'password123',
      role: 'member'
    },
    statusCode: 201,
    successResponse: {
      message: 'User registered successfully. Please verify your email.',
      user_id: 5
    },
    errorCode: 400,
    errorResponse: { error: 'Username or email already exists' }
  },
  {
    path: '/api/auth/login',
    method: 'POST',
    description: 'Autentikasi kredensial pengguna dan mengembalikan token JWT serta data user',
    role: 'Public / Member',
    auth: false,
    headers: { 'Content-Type': 'application/json' },
    requestBody: {
      username: 'member1',
      password: 'password123'
    },
    statusCode: 200,
    successResponse: {
      message: 'Login successful',
      token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...',
      user: {
        id: 2,
        username: 'member1',
        email: 'member1@example.com',
        role: 'member'
      }
    },
    errorCode: 401,
    errorResponse: { error: 'Invalid username or password' }
  },
  {
    path: '/api/jobs',
    method: 'GET',
    description: 'Menampilkan katalog lowongan pekerjaan publik dengan filter search & location',
    role: 'Public / Member',
    auth: false,
    headers: { 'Accept': 'application/json' },
    requestBody: null,
    statusCode: 200,
    successResponse: [
      {
        id: 1,
        title: 'Senior PHP Developer',
        company_name: 'Admin Company',
        location: 'Jakarta',
        job_type: 'full-time',
        salary_min: '8000000.00',
        salary_max: '12000000.00'
      }
    ],
    errorCode: 500,
    errorResponse: { error: 'Failed to fetch jobs' }
  },
  {
    path: '/api/jobs/{id}',
    method: 'GET',
    description: 'Mengambil detail lengkap lowongan pekerjaan berdasarkan Job ID',
    role: 'Public / Member',
    auth: false,
    headers: { 'Accept': 'application/json' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      id: 2,
      company_id: 3,
      title: 'Frontend Developer',
      description: 'Join our frontend team',
      requirements: 'HTML, CSS, JavaScript, React',
      salary_min: '6000000.00',
      salary_max: '9000000.00',
      location: 'Bandung',
      company_name: 'Tech Solutions'
    },
    errorCode: 404,
    errorResponse: { error: 'Job not found' }
  },
  {
    path: '/api/applications',
    method: 'POST',
    description: 'Mengajukan lamaran baru untuk lowongan pekerjaan tertentu',
    role: 'member',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      job_id: 2,
      cover_letter: 'Saya sangat tertarik dengan lowongan frontend developer di perusahaan Anda.'
    },
    statusCode: 201,
    successResponse: { message: 'Application submitted successfully' },
    errorCode: 409,
    errorResponse: { error: 'Already applied for this job' }
  },
  {
    path: '/api/applications/my',
    method: 'GET',
    description: 'Mengambil daftar riwayat seluruh lamaran yang telah diajukan oleh member login',
    role: 'member',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: [
      {
        id: 2,
        job_id: 2,
        title: 'Frontend Developer',
        location: 'Bandung',
        company_name: 'Tech Solutions',
        status: 'reviewed',
        applied_at: '2026-11-21 11:30:00'
      }
    ],
    errorCode: 401,
    errorResponse: { error: 'Unauthorized: Missing or invalid token' }
  },
  {
    path: '/api/applications/{id}',
    method: 'GET',
    description: 'Mengambil rincian lamaran pekerjaan milik member',
    role: 'member',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      id: 2,
      job_id: 2,
      title: 'Frontend Developer',
      cover_letter: 'Halo, saya John Doe ingin mengajukan lamaran...',
      status: 'reviewed',
      applied_at: '2026-11-21 11:30:00'
    },
    errorCode: 404,
    errorResponse: { error: 'Application not found' }
  },
  {
    path: '/api/applications/{id}',
    method: 'PUT',
    description: 'Memperbarui cover letter pada lamaran yang telah diajukan',
    role: 'member',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      cover_letter: 'Pembaruan surat lamaran kerja dengan portofolio terbaru.'
    },
    statusCode: 200,
    successResponse: { message: 'Application updated successfully', id: 2 },
    errorCode: 404,
    errorResponse: { error: 'Application not found' }
  },
  {
    path: '/api/applications/{id}',
    method: 'DELETE',
    description: 'Menghapus atau menarik berkas lamaran pekerjaan',
    role: 'member',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: { message: 'Application deleted successfully', id: 2 },
    errorCode: 404,
    errorResponse: { error: 'Application not found' }
  },
  {
    path: '/api/profile/member',
    method: 'GET',
    description: 'Mendapatkan data profil pribadi member (nama, nomor telepon, alamat, cv)',
    role: 'member',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      id: 1,
      user_id: 2,
      full_name: 'John Doe',
      phone: '081234567890',
      address: 'Jakarta, Indonesia',
      profile_photo: 'uploads/profiles/john.png',
      cv_file: 'uploads/cvs/cv_john.pdf'
    },
    errorCode: 401,
    errorResponse: { error: 'Unauthorized' }
  },
  {
    path: '/api/profile/member',
    method: 'PUT',
    description: 'Memperbarui informasi profil member',
    role: 'member',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      full_name: 'John Doe Updated',
      phone: '081299999999',
      address: 'Bandung, Indonesia'
    },
    statusCode: 200,
    successResponse: { message: 'Profile updated successfully' },
    errorCode: 500,
    errorResponse: { error: 'Failed to update profile' }
  },
  {
    path: '/api/skills',
    method: 'GET',
    description: 'Melihat seluruh daftar keahlian yang terdaftar pada profil member',
    role: 'member',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: [
      { id: 1, user_id: 2, skill_name: 'Vue.js', level: 'expert' },
      { id: 2, user_id: 2, skill_name: 'TypeScript', level: 'advanced' }
    ],
    errorCode: 401,
    errorResponse: { error: 'Unauthorized' }
  },
  {
    path: '/api/skills',
    method: 'POST',
    description: 'Menambahkan item keahlian baru ke profil',
    role: 'member',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      skill_name: 'Docker & Kubernetes',
      level: 'intermediate'
    },
    statusCode: 201,
    successResponse: { message: 'Skill added successfully' },
    errorCode: 400,
    errorResponse: { error: 'Skill name is required' }
  },
  {
    path: '/api/education',
    method: 'GET',
    description: 'Mengambil riwayat pendidikan formal member',
    role: 'member',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: [
      {
        id: 1,
        institution: 'Universitas Indonesia',
        degree: 'Sarjana Komputer (S1)',
        field_of_study: 'Ilmu Komputer',
        start_date: '2019-08-01',
        end_date: '2023-07-15'
      }
    ],
    errorCode: 401,
    errorResponse: { error: 'Unauthorized' }
  }
]

// ─────────────────────────────────────────────────────────────
// Company Endpoints
// ─────────────────────────────────────────────────────────────
const companyEndpoints = [
  {
    path: '/api/auth/login',
    method: 'POST',
    description: 'Autentikasi akun perusahaan untuk mengelola lowongan dan rekrutmen',
    role: 'company',
    auth: false,
    headers: { 'Content-Type': 'application/json' },
    requestBody: {
      username: 'company1',
      password: 'company123'
    },
    statusCode: 200,
    successResponse: {
      message: 'Login successful',
      token: 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...',
      user: {
        id: 3,
        username: 'company1',
        email: 'company1@example.com',
        role: 'company'
      }
    },
    errorCode: 401,
    errorResponse: { error: 'Invalid username or password' }
  },
  {
    path: '/api/profile/company',
    method: 'GET',
    description: 'Mengambil data profil perusahaan login',
    role: 'company',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      id: 2,
      user_id: 3,
      company_name: 'Tech Solutions',
      description: 'Leading technology company',
      website: 'https://techsolutions.com',
      phone: '021555555'
    },
    errorCode: 401,
    errorResponse: { error: 'Unauthorized' }
  },
  {
    path: '/api/jobs/my',
    method: 'GET',
    description: 'Mengambil seluruh lowongan kerja yang telah dipublikasikan oleh perusahaan login',
    role: 'company',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: [
      {
        id: 2,
        company_id: 3,
        title: 'Frontend Developer',
        job_type: 'full-time',
        status: 'active',
        created_at: '2025-07-13 12:07:05'
      }
    ],
    errorCode: 401,
    errorResponse: { error: 'Unauthorized' }
  },
  {
    path: '/api/jobs',
    method: 'POST',
    description: 'Membuat dan mempublikasikan lowongan kerja baru',
    role: 'company',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      title: 'Senior DevOps Engineer',
      description: 'Mencari DevOps Engineer berpengalaman dengan CI/CD dan Kubernetes.',
      requirements: 'Docker, Kubernetes, Linux, Terraform',
      salary_min: 15000000,
      salary_max: 22000000,
      location: 'Jakarta',
      job_type: 'full-time'
    },
    statusCode: 201,
    successResponse: { message: 'Job created successfully', job_id: 4 },
    errorCode: 400,
    errorResponse: { error: 'Title and description are required' }
  },
  {
    path: '/api/jobs/{id}',
    method: 'PUT',
    description: 'Memperbarui informasi lowongan kerja yang dimiliki',
    role: 'company',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      title: 'Senior DevOps Engineer (Lead)',
      salary_min: 18000000,
      salary_max: 25000000,
      status: 'active'
    },
    statusCode: 200,
    successResponse: { message: 'Job updated successfully' },
    errorCode: 404,
    errorResponse: { error: 'Job not found' }
  },
  {
    path: '/api/jobs/{id}',
    method: 'DELETE',
    description: 'Menutup atau menghapus lowongan kerja',
    role: 'company',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: { message: 'Job deleted successfully' },
    errorCode: 404,
    errorResponse: { error: 'Job not found' }
  },
  {
    path: '/api/applications/company',
    method: 'GET',
    description: 'Mengambil daftar semua pelamar yang melamar pada lowongan-lowongan perusahaan',
    role: 'company',
    auth: true,
    headers: { 'Authorization': 'Bearer <token>' },
    requestBody: null,
    statusCode: 200,
    successResponse: [
      {
        id: 2,
        job_id: 2,
        job_title: 'Frontend Developer',
        username: 'member1',
        email: 'member1@example.com',
        full_name: 'John Doe',
        status: 'reviewed',
        applied_at: '2026-11-21 11:30:00'
      }
    ],
    errorCode: 401,
    errorResponse: { error: 'Unauthorized' }
  },
  {
    path: '/api/applications/{id}/applicant',
    method: 'GET',
    description: 'Mengambil data lengkap profil pelamar (pendidikan, skills, kontak, cv)',
    role: 'company',
    auth: false,
    headers: { 'Accept': 'application/json' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      id: 2,
      job_id: 2,
      job_title: 'Frontend Developer',
      full_name: 'John Doe',
      email: 'member1@example.com',
      phone: '081234567890',
      status: 'reviewed',
      skills: [{ skill_name: 'Vue.js', level: 'expert' }],
      education: [{ institution: 'Universitas Indonesia', degree: 'Sarjana Komputer (S1)' }]
    },
    errorCode: 404,
    errorResponse: { error: 'Application not found' }
  },
  {
    path: '/api/applications/{id}/status',
    method: 'PUT',
    description: 'Memperbarui status tahapan lamaran (pending/reviewed/accepted/rejected). Wajib login (memerlukan Bearer token).',
    role: 'Authenticated User (Member / Company)',
    auth: true,
    headers: {
      'Authorization': 'Bearer <token>',
      'Content-Type': 'application/json'
    },
    requestBody: {
      status: 'accepted',
      role: 'company'
    },
    statusCode: 200,
    successResponse: {
      message: 'Status updated successfully',
      assigned_fields: ['status', 'role'],
      status: 'accepted',
      role: 'company',
      applicant_user_id: 2
    },
    errorCode: 401,
    errorResponse: { error: 'No token provided' }
  }
]

// ─────────────────────────────────────────────────────────────
// Admin Endpoints
// ─────────────────────────────────────────────────────────────
const adminEndpoints = [
  {
    path: '/api/analytics',
    method: 'GET',
    description: 'Mengambil ringkasan metrik statistik sistem: total pekerjaan, total pelamar, total perusahaan, dan pembagian jenis pekerjaan',
    role: 'Admin / Public Overview',
    auth: false,
    headers: { 'Accept': 'application/json' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      total_jobs: 2,
      total_companies: 2,
      total_members: 2,
      total_applications: 2,
      recent_jobs: [
        { id: 1, title: 'Senior PHP Developer', company_name: 'Admin Company' }
      ],
      job_types: [
        { job_type: 'full-time', count: 2 }
      ]
    },
    errorCode: 500,
    errorResponse: { error: 'Failed to generate analytics report' }
  },
  {
    path: '/api/auth/user?id={id}',
    method: 'GET',
    description: 'Inspeksi data akun pengguna (username, email, role, tanggal dibuat) berdasarkan ID',
    role: 'Admin / Debug Endpoint',
    auth: false,
    headers: { 'Accept': 'application/json' },
    requestBody: null,
    statusCode: 200,
    successResponse: {
      id: 2,
      username: 'member1',
      email: 'member1@example.com',
      role: 'member',
      is_verified: 1,
      created_at: '2025-07-13 12:07:05'
    },
    errorCode: 404,
    errorResponse: { error: 'User not found' }
  }
]

const currentList = computed(() => {
  if (activeCategory.value === 'member') return memberEndpoints
  if (activeCategory.value === 'company') return companyEndpoints
  return adminEndpoints
})

const filteredEndpoints = computed(() => {
  const q = searchTerm.value.trim().toLowerCase()
  if (!q) return currentList.value
  return currentList.value.filter(item =>
    item.path.toLowerCase().includes(q) ||
    item.description.toLowerCase().includes(q) ||
    item.method.toLowerCase().includes(q) ||
    item.role.toLowerCase().includes(q)
  )
})
</script>

<style scoped>
pre code {
  font-family: 'SFMono-Regular', Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;
}
</style>

