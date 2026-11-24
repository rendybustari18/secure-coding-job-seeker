<?php
require_once __DIR__ . '/../../config/database.php';

class AnalyticsController
{
    private PDO $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }
    public function overview(): void
    {
        $totalJobs         = $this->conn->query("SELECT COUNT(*) as total FROM jobs WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalCompanies    = $this->conn->query("SELECT COUNT(*) as total FROM company_profiles")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalMembers      = $this->conn->query("SELECT COUNT(*) as total FROM member_profiles")->fetch(PDO::FETCH_ASSOC)['total'];
        $totalApplications = $this->conn->query("SELECT COUNT(*) as total FROM job_applications")->fetch(PDO::FETCH_ASSOC)['total'];

        $recentJobsResult = $this->conn->query(
            "SELECT j.*, c.company_name FROM jobs j
             JOIN company_profiles c ON j.company_id = c.user_id
             WHERE j.status = 'active'
             ORDER BY j.created_at DESC LIMIT 5"
        );
        $recentJobs = $recentJobsResult->fetchAll(PDO::FETCH_ASSOC);

        $jobTypesResult = $this->conn->query(
            "SELECT job_type, COUNT(*) as count FROM jobs WHERE status = 'active' GROUP BY job_type"
        );
        $jobTypes = $jobTypesResult->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            'total_jobs'         => $totalJobs,
            'total_companies'    => $totalCompanies,
            'total_members'      => $totalMembers,
            'total_applications' => $totalApplications,
            'recent_jobs'        => $recentJobs,
            'job_types'          => $jobTypes,
        ]);
    }
}

