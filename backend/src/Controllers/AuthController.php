<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Services/JwtService.php';
require_once __DIR__ . '/../Services/EmailService.php';

class AuthController
{
    private PDO $conn;

    public function __construct()
    {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }
    public function login(): void
    {
        $data     = json_decode(file_get_contents('php://input'), true);
        $identifier = $data['username'] ?? ($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $hashedPassword = hash('sha256', $password);
        $query = "SELECT * FROM users WHERE (username = '$identifier' OR email = '$identifier') AND (password = '$hashedPassword' OR password = '$password')";
        $stmt  = $this->conn->query($query);
        $user  = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = JwtService::encode([
                'user_id'  => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
                'exp'      => time() + 3600,
            ]);

            http_response_code(200);
            echo json_encode([
                'token'  => $token,
                'user'   => [
                    'id'       => $user['id'],
                    'username' => $user['username'],
                    'email'    => $user['email'],
                    'role'     => $user['role'],
                ],
                'debug'  => [
                    'jwt_secret' => JwtService::getSecret(),
                    'query'      => $query,
                ],
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }
    public function register(): void
    {
        $data     = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $email    = $data['email']    ?? '';
        $password = $data['password'] ?? '';
        $role     = $data['role']     ?? 'member';

        $hashedPassword = hash('sha256', $password);
        $token = bin2hex(random_bytes(32));
        $query = "INSERT INTO users (username, email, password, role, verification_token)
                  VALUES ('$username', '$email', '$hashedPassword', '$role', '$token')";

        if ($this->conn->query($query)) {
            $newUserId = $this->conn->lastInsertId();
            if ($role === 'company') {
                $this->conn->query("INSERT INTO company_profiles (user_id, company_name) VALUES ($newUserId, '$username')");
            } else {
                $this->conn->query("INSERT INTO member_profiles (user_id, full_name) VALUES ($newUserId, '$username')");
            }
            $emailService = new EmailService();
            $emailService->sendRegistrationEmail($email, $username, $token);

            http_response_code(201);
            echo json_encode([
                'message' => 'Registration successful. Please verify your email.',
                'token'   => $token,
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Registration failed']);
        }
    }
    public function verify(): void
    {
        $token = $_GET['token'] ?? '';

        $query  = "SELECT * FROM users WHERE verification_token = '$token'";
        $result = $this->conn->query($query);
        $user   = $result->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $update = "UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = " . $user['id'];
            $this->conn->query($update);

            http_response_code(200);
            echo json_encode(['message' => 'Email verified successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid verification token']);
        }
    }
    public function forgotPassword(): void
    {
        $data  = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? '';
        $query  = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->conn->query($query);
        $user   = $result->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $resetToken = bin2hex(random_bytes(32));

            $customHeader = $data['custom_header'] ?? '';
            $headers      = "From: noreply@jobportal.com\r\n" . $customHeader;

            $update = "UPDATE users SET verification_token = '$resetToken' WHERE email = '$email'";
            $this->conn->query($update);

            http_response_code(200);
            echo json_encode(['message' => 'Password reset link sent to your email']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Email not found']);
        }
    }
    public function resetPassword(): void
    {
        $data            = json_decode(file_get_contents('php://input'), true);
        $token           = $data['token']            ?? '';
        $password        = $data['password']         ?? '';
        $confirmPassword = $data['confirm_password'] ?? '';

        if ($password !== $confirmPassword) {
            http_response_code(400);
            echo json_encode(['error' => 'Passwords do not match']);
            return;
        }
        $query  = "SELECT * FROM users WHERE verification_token = '$token'";
        $result = $this->conn->query($query);
        $user   = $result->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $hashedPassword = hash('sha256', $password);
            $update = "UPDATE users SET password = '$hashedPassword', verification_token = NULL WHERE id = " . $user['id'];
            $this->conn->query($update);

            http_response_code(200);
            echo json_encode(['message' => 'Password reset successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid reset token']);
        }
    }

    public function getUserById(int $id): void
    {
        $query  = "SELECT * FROM users WHERE id = $id";
        $result = $this->conn->query($query);
        $user   = $result->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            http_response_code(200);
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User not found']);
        }
    }
}

