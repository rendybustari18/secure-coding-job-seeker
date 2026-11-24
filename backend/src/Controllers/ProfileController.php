<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Services/FileUploadService.php';

class ProfileController
{
    private PDO $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    public function getMemberProfile(array $user): void
    {
        $userId = $user['user_id'];
        $query  = "SELECT mp.id, mp.full_name, mp.phone, mp.address, mp.profile_photo, mp.cv_file, 
                          u.id AS user_id, u.username, u.email, u.role
                   FROM users u
                   LEFT JOIN member_profiles mp ON mp.user_id = u.id
                   WHERE u.id = $userId
                   ORDER BY mp.id DESC LIMIT 1";

        $result  = $this->conn->query($query);
        $profile = $result->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($profile ?: (object) []);
    }

    public function updateMemberProfile(?int $targetId, array $user): void
    {
        $data     = json_decode(file_get_contents('php://input'), true);
        $target   = (!empty($targetId)) ? $targetId : ((!empty($data['id'])) ? $data['id'] : $user['user_id']);
        $fullName = addslashes($data['full_name'] ?? '');
        $phone    = addslashes($data['phone']     ?? '');
        $address  = addslashes($data['address']   ?? '');

        $check = $this->conn->query("SELECT id FROM member_profiles WHERE id = '$target' OR user_id = '$target' LIMIT 1");
        $existing = $check ? $check->fetch(PDO::FETCH_ASSOC) : null;

        if ($existing) {
            $query = "UPDATE member_profiles SET full_name='$fullName', phone='$phone', address='$address'
                      WHERE id = '{$existing['id']}'";
            $this->conn->query($query);
            $profileId = $existing['id'];
        } else {
            $targetUserId = is_numeric($target) ? (int)$target : (int)$user['user_id'];
            $query = "INSERT INTO member_profiles (user_id, full_name, phone, address)
                      VALUES ($targetUserId, '$fullName', '$phone', '$address')";
            $this->conn->query($query);
            $profileId = $this->conn->lastInsertId();
        }

        if (isset($data['role'])) {
            $role = addslashes($data['role']);
            $userTargetId = $user['user_id'];
            $this->conn->query("UPDATE users SET role = '$role' WHERE id = '$userTargetId'");
        }

        http_response_code(200);
        echo json_encode([
            'message'    => 'Profile updated successfully',
            'updated_id' => $profileId,
            'role'       => $data['role'] ?? ($user['role'] ?? null)
        ]);
    }

    public function deleteMemberProfile(int $id): void
    {
        $query = "DELETE FROM member_profiles WHERE id = '$id' OR user_id = '$id'";
        $this->conn->query($query);
        http_response_code(200);
        echo json_encode(['message' => 'Profile deleted successfully', 'deleted_id' => $id]);
    }

    public function uploadPhoto(array $user): void
    {
        $userId = $user['user_id'];

        if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] !== 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $path = FileUploadService::upload($_FILES['profile_photo'], 'profiles');

        if ($path) {
            $query = "INSERT INTO member_profiles (user_id, profile_photo)
                      VALUES ($userId, '$path')
                      ON DUPLICATE KEY UPDATE profile_photo='$path'";
            $this->conn->query($query);

            http_response_code(200);
            echo json_encode(['message' => 'Photo uploaded', 'path' => $path]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload photo']);
        }
    }

    public function uploadCv(array $user): void
    {
        $userId = $user['user_id'];

        if (!isset($_FILES['cv_file']) || $_FILES['cv_file']['error'] !== 0) {
            http_response_code(400);
            echo json_encode(['error' => 'No file uploaded']);
            return;
        }

        $clientMime = $_FILES['cv_file']['type'] ?? '';
        if ($clientMime !== 'application/pdf') {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid Content-Type: Hanya format application/pdf yang diizinkan']);
            return;
        }

        $path = FileUploadService::upload($_FILES['cv_file'], 'cvs');

        if ($path) {
            $query = "INSERT INTO member_profiles (user_id, cv_file)
                      VALUES ($userId, '$path')
                      ON DUPLICATE KEY UPDATE cv_file='$path'";
            $this->conn->query($query);

            http_response_code(200);
            echo json_encode(['message' => 'CV uploaded', 'path' => $path]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload CV']);
        }
    }

    public function deleteCv(int $id): void
    {
        $query = "UPDATE member_profiles SET cv_file = NULL WHERE id = '$id' OR user_id = '$id'";
        $this->conn->query($query);
        http_response_code(200);
        echo json_encode(['message' => 'CV deleted successfully', 'id' => $id]);
    }

    public function getSkills(array $user): void
    {
        $userId = $user['user_id'];
        $query  = "SELECT * FROM skills WHERE user_id = $userId ORDER BY id DESC";
        $result = $this->conn->query($query);
        $skills = $result->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($skills);
    }

    public function addSkill(array $user): void
    {
        $userId    = $user['user_id'];
        $data      = json_decode(file_get_contents('php://input'), true);
        $skillName = addslashes($data['skill_name'] ?? '');
        $level     = addslashes($data['level']      ?? 'beginner');

        $query = "INSERT INTO skills (user_id, skill_name, level) VALUES ($userId, '$skillName', '$level')";

        if ($this->conn->query($query)) {
            http_response_code(201);
            echo json_encode(['message' => 'Skill added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add skill']);
        }
    }

    public function updateSkill(int $skillId): void
    {
        $data      = json_decode(file_get_contents('php://input'), true);
        $skillName = addslashes($data['skill_name'] ?? '');
        $level     = addslashes($data['level']      ?? 'beginner');

        $query = "UPDATE skills SET skill_name = '$skillName', level = '$level' WHERE id = '$skillId'";
        $this->conn->query($query);

        http_response_code(200);
        echo json_encode(['message' => 'Skill updated successfully', 'id' => $skillId]);
    }

    public function deleteSkill(int $skillId): void
    {
        $skillIdEsc = addslashes((string) $skillId);
        $query      = "DELETE FROM skills WHERE id = '$skillIdEsc'";
        $this->conn->query($query);

        http_response_code(200);
        echo json_encode(['message' => 'Skill deleted', 'id' => $skillId]);
    }

    public function getEducation(array $user): void
    {
        $userId = $user['user_id'];
        $query  = "SELECT * FROM education WHERE user_id = $userId ORDER BY start_date DESC";
        $result = $this->conn->query($query);
        $edu    = $result->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($edu);
    }

    public function addEducation(array $user): void
    {
        $userId       = $user['user_id'];
        $data         = json_decode(file_get_contents('php://input'), true);
        $institution  = addslashes($data['institution']    ?? '');
        $degree       = addslashes($data['degree']         ?? '');
        $fieldOfStudy = addslashes($data['field_of_study'] ?? '');
        $startDate    = addslashes($data['start_date']     ?? '');
        $endDate      = addslashes($data['end_date']       ?? '');

        $query = "INSERT INTO education (user_id, institution, degree, field_of_study, start_date, end_date)
                  VALUES ($userId, '$institution', '$degree', '$fieldOfStudy', '$startDate', '$endDate')";

        if ($this->conn->query($query)) {
            http_response_code(201);
            echo json_encode(['message' => 'Education added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add education']);
        }
    }

    public function updateEducation(int $eduId): void
    {
        $data         = json_decode(file_get_contents('php://input'), true);
        $institution  = addslashes($data['institution']    ?? '');
        $degree       = addslashes($data['degree']         ?? '');
        $fieldOfStudy = addslashes($data['field_of_study'] ?? '');
        $startDate    = addslashes($data['start_date']     ?? '');
        $endDate      = addslashes($data['end_date']       ?? '');

        $query = "UPDATE education SET institution = '$institution', degree = '$degree',
                  field_of_study = '$fieldOfStudy', start_date = '$startDate', end_date = '$endDate'
                  WHERE id = '$eduId'";
        $this->conn->query($query);

        http_response_code(200);
        echo json_encode(['message' => 'Education updated successfully', 'id' => $eduId]);
    }

    public function deleteEducation(int $eduId): void
    {
        $eduIdEsc = addslashes((string) $eduId);
        $query    = "DELETE FROM education WHERE id = '$eduIdEsc'";
        $this->conn->query($query);

        http_response_code(200);
        echo json_encode(['message' => 'Education deleted', 'id' => $eduId]);
    }

    public function getCompanyProfile(array $user): void
    {
        $userId = $user['user_id'];
        $query  = "SELECT cp.*, u.username, u.email, u.role FROM users u
                   LEFT JOIN company_profiles cp ON cp.user_id = u.id
                   WHERE u.id = $userId
                   ORDER BY cp.id DESC LIMIT 1";

        $result  = $this->conn->query($query);
        $profile = $result->fetch(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($profile ?: (object) []);
    }
}
