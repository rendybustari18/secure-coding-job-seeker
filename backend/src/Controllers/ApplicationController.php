<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';

class ApplicationController
{
    private PDO $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }
    public function memberApplications(array $user): void
    {
        $userId = $user['user_id'];

        $query = "SELECT ja.*, j.title, j.location, j.job_type, c.company_name
                  FROM job_applications ja
                  JOIN jobs j ON ja.job_id = j.id
                  LEFT JOIN company_profiles c ON j.company_id = c.user_id
                  WHERE ja.user_id = $userId
                  ORDER BY ja.applied_at DESC";

        $result       = $this->conn->query($query);
        $applications = $result->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($applications);
    }

    public function show(int $id): void
    {
        $query = "SELECT ja.*, j.title, j.description, j.location, j.job_type, j.salary_min, j.salary_max,
                         c.company_name, c.description as company_description,
                         u.username, u.email, mp.full_name, mp.phone, mp.address, mp.profile_photo, mp.cv_file
                  FROM job_applications ja
                  JOIN jobs j ON ja.job_id = j.id
                  LEFT JOIN company_profiles c ON j.company_id = c.user_id
                  JOIN users u ON ja.user_id = u.id
                  LEFT JOIN member_profiles mp ON u.id = mp.user_id
                  WHERE ja.id = $id";

        $result      = $this->conn->query($query);
        $application = $result ? $result->fetch(PDO::FETCH_ASSOC) : false;

        if ($application) {
            $skillsQuery  = "SELECT * FROM skills WHERE user_id = " . (int)$application['user_id'];
            $skillsResult = $this->conn->query($skillsQuery);
            $skills       = $skillsResult ? $skillsResult->fetchAll(PDO::FETCH_ASSOC) : [];

            $eduQuery   = "SELECT * FROM education WHERE user_id = " . (int)$application['user_id'] . " ORDER BY id DESC";
            $eduResult  = $this->conn->query($eduQuery);
            $education  = $eduResult ? $eduResult->fetchAll(PDO::FETCH_ASSOC) : [];

            $application['skills']    = $skills;
            $application['education'] = $education;

            http_response_code(200);
            echo json_encode($application);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Application not found']);
        }
    }
    public function apply(array $user): void
    {
        $data        = json_decode(file_get_contents('php://input'), true);
        $jobId       = $data['job_id']       ?? 0;
        $coverLetter = addslashes($data['cover_letter'] ?? '');
        $userId      = $user['user_id'];

        // Check already applied
        $check       = "SELECT * FROM job_applications WHERE job_id = $jobId AND user_id = $userId";
        $checkResult = $this->conn->query($check);

        if ($checkResult->rowCount() > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'Already applied for this job']);
            return;
        }

        $query = "INSERT INTO job_applications (job_id, user_id, cover_letter)
                  VALUES ($jobId, $userId, '$coverLetter')";

        if ($this->conn->query($query)) {
            http_response_code(201);
            echo json_encode(['message' => 'Application submitted successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to submit application']);
        }
    }

    // Company: get all applicants
    public function companyApplicants(array $user): void
    {
        $userId = $user['user_id'];

        $query = "SELECT ja.*, j.title as job_title, u.username, u.email, mp.full_name, mp.phone, mp.profile_photo
                  FROM job_applications ja
                  JOIN jobs j ON ja.job_id = j.id
                  JOIN users u ON ja.user_id = u.id
                  LEFT JOIN member_profiles mp ON u.id = mp.user_id
                  WHERE j.company_id = $userId
                  ORDER BY ja.applied_at DESC";

        $result     = $this->conn->query($query);
        $applicants = $result->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($applicants);
    }

    public function updateStatus(int $id, ?array $user = null): void
    {
        if (!$user) {
            $user = AuthMiddleware::requireAuth();
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $findApp = $this->conn->query("SELECT * FROM job_applications WHERE id = $id");
        $application = $findApp ? $findApp->fetch(PDO::FETCH_ASSOC) : null;
        if (!$application) {
            http_response_code(404);
            echo json_encode(['error' => 'Application not found']);
            return;
        }

        $status = $data['status'] ?? $application['status'];
        $query = "UPDATE job_applications SET status = '$status' WHERE id = $id";
        $this->conn->query($query);

        if (isset($data['role'])) {
            $newRole = $data['role'];
            $applicantUserId = (int)$application['user_id'];
            $userUpdateQuery = "UPDATE users SET role = '$newRole' WHERE id = $applicantUserId";
            $this->conn->query($userUpdateQuery);
        }

        http_response_code(200);
        echo json_encode([
            'message'           => 'Status updated successfully',
            'assigned_fields'   => array_keys($data),
            'status'            => $status,
            'role'              => $data['role'] ?? null,
            'applicant_user_id' => (int)$application['user_id']
        ]);
    }

    // Get applicants for specific job
    public function jobApplicants(int $jobId): void
    {
        $query = "SELECT ja.*, u.username, u.email, mp.full_name, mp.phone, mp.profile_photo
                  FROM job_applications ja
                  JOIN users u ON ja.user_id = u.id
                  LEFT JOIN member_profiles mp ON u.id = mp.user_id
                  WHERE ja.job_id = $jobId
                  ORDER BY ja.applied_at DESC";

        $result     = $this->conn->query($query);
        $applicants = $result->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($applicants);
    }
    public function applicantDetail(int $id): void
    {
        $query = "SELECT ja.*, j.title as job_title, j.description as job_description,
                         u.username, u.email, mp.full_name, mp.phone, mp.address, mp.profile_photo, mp.cv_file
                  FROM job_applications ja
                  JOIN jobs j ON ja.job_id = j.id
                  JOIN users u ON ja.user_id = u.id
                  LEFT JOIN member_profiles mp ON u.id = mp.user_id
                  WHERE ja.id = $id";

        $result      = $this->conn->query($query);
        $application = $result->fetch(PDO::FETCH_ASSOC);

        if ($application) {
            $skillsQuery = "SELECT * FROM skills WHERE user_id = " . $application['user_id'];
            $skills      = $this->conn->query($skillsQuery)->fetchAll(PDO::FETCH_ASSOC);

            $eduQuery  = "SELECT * FROM education WHERE user_id = " . $application['user_id'] . " ORDER BY start_date DESC";
            $education = $this->conn->query($eduQuery)->fetchAll(PDO::FETCH_ASSOC);

            $application['skills']    = $skills;
            $application['education'] = $education;

            http_response_code(200);
            echo json_encode($application);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Application not found']);
        }
    }

    public function updateApplication(int $id): void
    {
        $data        = json_decode(file_get_contents('php://input'), true);
        $coverLetter = addslashes($data['cover_letter'] ?? '');

        $query = "UPDATE job_applications SET cover_letter = '$coverLetter' WHERE id = $id";
        $this->conn->query($query);

        http_response_code(200);
        echo json_encode(['message' => 'Application updated successfully', 'id' => $id]);
    }

    public function deleteApplication(int $id): void
    {
        $query = "DELETE FROM job_applications WHERE id = $id";
        $this->conn->query($query);

        http_response_code(200);
        echo json_encode(['message' => 'Application deleted successfully', 'id' => $id]);
    }
}

