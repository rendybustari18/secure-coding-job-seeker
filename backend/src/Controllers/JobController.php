<?php
require_once __DIR__ . '/../../config/database.php';

class JobController
{
    private PDO $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }
    public function index(): void
    {
        $search   = $_GET['search'] ?? $_GET['q'] ?? '';
        $location = $_GET['location'] ?? '';
        $sort     = $_GET['sort']     ?? 'j.created_at DESC';

        $query = "SELECT j.*, c.company_name FROM jobs j
                  LEFT JOIN company_profiles c ON j.company_id = c.user_id
                  WHERE j.title LIKE '%$search%'";

        if ($location) {
            $query .= " AND j.location LIKE '%$location%'";
        }

        $query .= " ORDER BY $sort";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($jobs);
    }
    public function show(int $id): void
    {
        $query  = "SELECT j.*, c.company_name, c.description as company_description, c.website
                  FROM jobs j
                  LEFT JOIN company_profiles c ON j.company_id = c.user_id
                  WHERE j.id = $id";
        $result = $this->conn->query($query);
        $job    = $result->fetch(PDO::FETCH_ASSOC);

        if ($job) {
            http_response_code(200);
            echo json_encode($job);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Job not found']);
        }
    }
    public function store(array $user): void
    {
        $data         = json_decode(file_get_contents('php://input'), true);
        $title        = addslashes($data['title']        ?? '');
        $description  = addslashes($data['description']  ?? '');
        $requirements = addslashes($data['requirements'] ?? '');
        $salaryMin    = $data['salary_min'] ?? 0;
        $salaryMax    = $data['salary_max'] ?? 0;
        $location     = addslashes($data['location']     ?? '');
        $jobType      = addslashes($data['job_type']     ?? 'full-time');
        $companyId    = $user['user_id'];
        $query = "INSERT INTO jobs (company_id, title, description, requirements, salary_min, salary_max, location, job_type)
                  VALUES ($companyId, '$title', '$description', '$requirements', $salaryMin, $salaryMax, '$location', '$jobType')";

        if ($this->conn->query($query)) {
            http_response_code(201);
            echo json_encode(['message' => 'Job created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create job']);
        }
    }

    public function update(int $jobId, array $user): void
    {
        $data         = json_decode(file_get_contents('php://input'), true);
        $title        = addslashes($data['title']        ?? '');
        $description  = addslashes($data['description']  ?? '');
        $requirements = addslashes($data['requirements'] ?? '');
        $salaryMin    = $data['salary_min'] ?? 0;
        $salaryMax    = $data['salary_max'] ?? 0;
        $location     = addslashes($data['location']     ?? '');
        $jobType      = addslashes($data['job_type']     ?? 'full-time');
        $companyId    = $user['user_id'];

        $check       = "SELECT company_id FROM jobs WHERE id = '$jobId'";
        $checkResult = $this->conn->query($check);
        $jobOwner    = $checkResult->fetch(PDO::FETCH_ASSOC);

        if ($jobOwner && $jobOwner['company_id'] == $companyId) {
            $query = "UPDATE jobs SET title='$title', description='$description', requirements='$requirements',
                      salary_min=$salaryMin, salary_max=$salaryMax, location='$location', job_type='$jobType'
                      WHERE id='$jobId' AND company_id=$companyId";

            $this->conn->query($query);
            http_response_code(200);
            echo json_encode(['message' => 'Job updated successfully']);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
        }
    }

    public function destroy(int $jobId, array $user): void
    {
        $companyId   = $user['user_id'];
        $jobIdEsc    = addslashes((string) $jobId);
        $check       = "SELECT company_id FROM jobs WHERE id = '$jobIdEsc'";
        $checkResult = $this->conn->query($check);
        $jobOwner    = $checkResult->fetch(PDO::FETCH_ASSOC);

        if ($jobOwner && $jobOwner['company_id'] == $companyId) {
            $query = "DELETE FROM jobs WHERE id = '$jobIdEsc' AND company_id = $companyId";
            $this->conn->query($query);

            http_response_code(200);
            echo json_encode(['message' => 'Job deleted successfully']);
        } else {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
        }
    }
    public function companyJobs(array $user): void
    {
        $userId = $user['user_id'];
        $query  = "SELECT * FROM jobs WHERE company_id = $userId ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        $jobs   = $result->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($jobs);
    }
}

