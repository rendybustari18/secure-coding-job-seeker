<?php
require_once __DIR__ . '/../Middleware/CorsMiddleware.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Controllers/AuthController.php';
require_once __DIR__ . '/../Controllers/JobController.php';
require_once __DIR__ . '/../Controllers/ApplicationController.php';
require_once __DIR__ . '/../Controllers/ProfileController.php';
require_once __DIR__ . '/../Controllers/AnalyticsController.php';

class Router
{
    private string $method;
    private string $path;

    public function __construct()
    {
        CorsMiddleware::handle();
        header('Content-Type: application/json');

        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->path   = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->path   = rtrim($this->path, '/');
    }

    public function dispatch(): void
    {
        $segments = explode('/', ltrim($this->path, '/'));
        // URI format: api/{resource}/{id_or_action?}/{sub?}
        $resource = $segments[1] ?? '';
        $action   = $segments[2] ?? null;
        $id       = (isset($segments[2]) && is_numeric($segments[2])) ? (int) $segments[2] : null;
        $sub      = $segments[3] ?? null;

        match (true) {
            // ── Auth ──────────────────────────────────────────────
            $resource === 'auth' && $action === 'login'           && $this->method === 'POST'  => (new AuthController())->login(),
            $resource === 'auth' && $action === 'register'        && $this->method === 'POST'  => (new AuthController())->register(),
            $resource === 'auth' && $action === 'verify'          && $this->method === 'GET'   => (new AuthController())->verify(),
            $resource === 'auth' && $action === 'forgot-password' && $this->method === 'POST'  => (new AuthController())->forgotPassword(),
            $resource === 'auth' && $action === 'reset-password'  && $this->method === 'POST'  => (new AuthController())->resetPassword(),
            $resource === 'auth' && $action === 'user'            && $this->method === 'GET'   => (new AuthController())->getUserById((int) ($_GET['id'] ?? 0)),

            // ── Jobs (public) ─────────────────────────────────────
            $resource === 'jobs' && $id === null && $action === null && $this->method === 'GET'   => (new JobController())->index(),
            $resource === 'jobs' && $id !== null && $this->method === 'GET'                        => (new JobController())->show($id),

            // ── Jobs (company protected) ──────────────────────────
            $resource === 'jobs' && $id === null && $action === null && $this->method === 'POST'  => (new JobController())->store(AuthMiddleware::requireAuth()),
            $resource === 'jobs' && $id !== null && $this->method === 'PUT'                        => (new JobController())->update($id, AuthMiddleware::requireAuth()),
            $resource === 'jobs' && $id !== null && $this->method === 'DELETE'                     => (new JobController())->destroy($id, AuthMiddleware::requireAuth()),
            $resource === 'jobs' && $action === 'my'             && $this->method === 'GET'        => (new JobController())->companyJobs(AuthMiddleware::requireAuth()),

            // ── Applications ──────────────────────────────────────
            $resource === 'applications' && $action === 'my'     && $this->method === 'GET'        => (new ApplicationController())->memberApplications(AuthMiddleware::requireAuth()),
            $resource === 'applications' && $id !== null && $sub === 'status' && $this->method === 'PUT' => (new ApplicationController())->updateStatus($id, AuthMiddleware::requireAuth()),
            $resource === 'applications' && $id !== null && $sub === 'applicant' && $this->method === 'GET' => (new ApplicationController())->applicantDetail($id),
            $resource === 'applications' && $id !== null && $sub === null && $this->method === 'GET' => (new ApplicationController())->show($id),
            $resource === 'applications' && $id !== null && $sub === null && $this->method === 'PUT' => (new ApplicationController())->updateApplication($id),
            $resource === 'applications' && $id !== null && $sub === null && $this->method === 'DELETE' => (new ApplicationController())->deleteApplication($id),
            $resource === 'applications' && $id === null && $action === null && $this->method === 'POST' => (new ApplicationController())->apply(AuthMiddleware::requireAuth()),
            $resource === 'applications' && $action === 'company' && $this->method === 'GET'      => (new ApplicationController())->companyApplicants(AuthMiddleware::requireAuth()),
            $resource === 'applications' && $action === 'job'     && $this->method === 'GET'      => (new ApplicationController())->jobApplicants((int) ($_GET['job_id'] ?? 0)),

            // ── Profile ───────────────────────────────────────────
            $resource === 'profile' && $action === 'member'      && $this->method === 'GET'        => (new ProfileController())->getMemberProfile(AuthMiddleware::requireAuth()),
            $resource === 'profile' && $action === 'member'      && ($this->method === 'PUT' || $this->method === 'POST') => (new ProfileController())->updateMemberProfile(is_numeric($sub) ? (int)$sub : null, AuthMiddleware::requireAuth()),
            $resource === 'profile' && $action === 'member'      && is_numeric($sub) && $this->method === 'DELETE' => (new ProfileController())->deleteMemberProfile((int)$sub),
            $resource === 'profile' && $action === 'photo'       && $this->method === 'POST'       => (new ProfileController())->uploadPhoto(AuthMiddleware::requireAuth()),
            $resource === 'profile' && $action === 'cv'          && $this->method === 'POST'       => (new ProfileController())->uploadCv(AuthMiddleware::requireAuth()),
            $resource === 'profile' && $action === 'cv'          && is_numeric($sub) && $this->method === 'DELETE' => (new ProfileController())->deleteCv((int)$sub),
            $resource === 'profile' && $action === 'company'      && $this->method === 'GET'        => (new ProfileController())->getCompanyProfile(AuthMiddleware::requireAuth()),

            // ── Skills ────────────────────────────────────────────
            $resource === 'skills' && $action === null           && $this->method === 'GET'        => (new ProfileController())->getSkills(AuthMiddleware::requireAuth()),
            $resource === 'skills' && $action === null           && $this->method === 'POST'       => (new ProfileController())->addSkill(AuthMiddleware::requireAuth()),
            $resource === 'skills' && $id !== null               && $this->method === 'PUT'        => (new ProfileController())->updateSkill($id),
            $resource === 'skills' && $id !== null               && $this->method === 'DELETE'     => (new ProfileController())->deleteSkill($id),

            // ── Education ─────────────────────────────────────────
            $resource === 'education' && $action === null        && $this->method === 'GET'        => (new ProfileController())->getEducation(AuthMiddleware::requireAuth()),
            $resource === 'education' && $action === null        && $this->method === 'POST'       => (new ProfileController())->addEducation(AuthMiddleware::requireAuth()),
            $resource === 'education' && $id !== null            && $this->method === 'PUT'        => (new ProfileController())->updateEducation($id),
            $resource === 'education' && $id !== null            && $this->method === 'DELETE'     => (new ProfileController())->deleteEducation($id),

            // ── Analytics ─────────────────────────────────────────
            $resource === 'analytics' && $this->method === 'GET'                                   => (new AnalyticsController())->overview(),

            // ── 404 ───────────────────────────────────────────────
            default => $this->notFound(),
        };
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'Route not found', 'path' => $this->path]);
    }
}
