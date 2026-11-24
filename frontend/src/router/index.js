import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/auth'

// Layouts
import DefaultLayout from '@/layouts/DefaultLayout.vue'
import MemberLayout from '@/layouts/MemberLayout.vue'
import CompanyLayout from '@/layouts/CompanyLayout.vue'

// Public pages
import HomePage from '@/pages/HomePage.vue'
import JobsPage from '@/pages/JobsPage.vue'
import JobDetailPage from '@/pages/JobDetailPage.vue'
import AnalyticsPage from '@/pages/AnalyticsPage.vue'
import ApiDocsPage from '@/pages/ApiDocsPage.vue'

// Auth pages
import LoginPage from '@/pages/auth/LoginPage.vue'
import RegisterPage from '@/pages/auth/RegisterPage.vue'
import ForgotPasswordPage from '@/pages/auth/ForgotPasswordPage.vue'
import ResetPasswordPage from '@/pages/auth/ResetPasswordPage.vue'
import VerifyPage from '@/pages/auth/VerifyPage.vue'
import RegistrationSuccessPage from '@/pages/auth/RegistrationSuccessPage.vue'

// Member pages
import MemberDashboard from '@/pages/member/DashboardPage.vue'
import MemberProfile from '@/pages/member/ProfilePage.vue'
import MemberCv from '@/pages/member/CvPage.vue'
import MemberJobs from '@/pages/member/JobsPage.vue'
import MemberJobDetail from '@/pages/member/JobDetailPage.vue'
import MemberApply from '@/pages/member/ApplyPage.vue'
import MemberHistory from '@/pages/member/HistoryPage.vue'
import MemberApplicationDetail from '@/pages/member/ApplicationDetailPage.vue'
import MemberSkills from '@/pages/member/SkillsPage.vue'
import MemberEducation from '@/pages/member/EducationPage.vue'

// Company pages
import CompanyDashboard from '@/pages/company/DashboardPage.vue'
import CompanyJobs from '@/pages/company/JobsPage.vue'
import CompanyApplicants from '@/pages/company/ApplicantsPage.vue'
import CompanyApplicantDetail from '@/pages/company/ApplicantDetailPage.vue'
import CompanyJobApplicants from '@/pages/company/JobApplicantsPage.vue'

const routes = [
  {
    path: '/',
    component: DefaultLayout,
    children: [
      { path: '', name: 'home', component: HomePage },
      { path: 'jobs', name: 'jobs', component: JobsPage },
      { path: 'jobs/:id', name: 'job-detail', component: JobDetailPage },
      { path: 'analytics', name: 'analytics', component: AnalyticsPage },
      { path: 'docs', name: 'docs', component: ApiDocsPage },
      { path: 'api-docs', name: 'api-docs', component: ApiDocsPage },
    ],
  },
  {
    path: '/auth',
    component: DefaultLayout,
    children: [
      { path: 'login', name: 'login', component: LoginPage },
      { path: 'register', name: 'register', component: RegisterPage },
      { path: 'forgot-password', name: 'forgot-password', component: ForgotPasswordPage },
      { path: 'reset-password', name: 'reset-password', component: ResetPasswordPage },
      { path: 'verify', name: 'verify', component: VerifyPage },
      { path: 'registration-success', name: 'registration-success', component: RegistrationSuccessPage },
    ],
  },
  {
    path: '/member',
    component: MemberLayout,
    meta: { requiresAuth: true, role: 'member' },
    children: [
      { path: 'dashboard', name: 'member-dashboard', component: MemberDashboard },
      { path: 'profile', name: 'member-profile', component: MemberProfile },
      { path: 'cv', name: 'member-cv', component: MemberCv },
      { path: 'jobs', name: 'member-jobs', component: MemberJobs },
      { path: 'jobs/:id', name: 'member-job-detail', component: MemberJobDetail },
      { path: 'apply/:jobId', name: 'member-apply', component: MemberApply },
      { path: 'history', name: 'member-history', component: MemberHistory },
      { path: 'history/:id', name: 'member-application-detail', component: MemberApplicationDetail },
      { path: 'skills', name: 'member-skills', component: MemberSkills },
      { path: 'education', name: 'member-education', component: MemberEducation },
    ],
  },
  {
    path: '/company',
    component: CompanyLayout,
    meta: { requiresAuth: true, role: 'company' },
    children: [
      { path: 'dashboard', name: 'company-dashboard', component: CompanyDashboard },
      { path: 'jobs', name: 'company-jobs', component: CompanyJobs },
      { path: 'applicants', name: 'company-applicants', component: CompanyApplicants },
      { path: 'applicants/:id', name: 'company-applicant-detail', component: CompanyApplicantDetail },
      { path: 'jobs/:jobId/applicants', name: 'company-job-applicants', component: CompanyJobApplicants },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()

  if (to.meta.requiresAuth && !authStore.isLoggedIn) {
    next('/auth/login')
    return
  }

  const clientRole = localStorage.getItem('role') || authStore.role || authStore.user?.role

  if (to.meta.role && clientRole !== to.meta.role) {
    console.warn(`[Client-Side Guard Blocked] Role '${clientRole}' dilarang mengakses area '${to.meta.role}'`)
    if (clientRole === 'member') {
      next('/member/dashboard')
    } else if (clientRole === 'company') {
      next('/company/dashboard')
    } else {
      next('/auth/login')
    }
    return
  }

  next()
})

export default router

