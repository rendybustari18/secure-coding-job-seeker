<?php
require_once __DIR__ . '/../../config/env.php';

class EmailService
{
    private string $host;
    private int    $port;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->host     = MAILTRAP_HOST;
        $this->port     = MAILTRAP_PORT;
        $this->username = MAILTRAP_USERNAME;
        $this->password = MAILTRAP_PASSWORD;
    }

    public function sendRegistrationEmail(string $toEmail, string $username, string $token): bool
    {
        $subject         = 'Welcome to Job Portal - Please Verify Your Email';
        $verificationUrl = BASE_URL . '/api/auth/verify?token=' . $token;
        $htmlBody        = $this->getRegistrationTemplate($username, $verificationUrl);
        $textBody        = "Verify your email: {$verificationUrl}";

        return $this->sendEmail($toEmail, $subject, $htmlBody, $textBody);
    }

    private function sendEmail(string $toEmail, string $subject, string $htmlBody, string $textBody = ''): bool
    {
        try {
            $boundary = md5((string) time());
            $headers  = [
                'MIME-Version: 1.0',
                "Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
                'From: Job Portal <noreply@jobportal.com>',
                'Reply-To: noreply@jobportal.com',
                'X-Mailer: PHP/' . phpversion(),
            ];

            $body  = "--{$boundary}\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n{$textBody}\r\n\r\n";
            $body .= "--{$boundary}\r\n";
            $body .= "Content-Type: text/html; charset=UTF-8\r\n\r\n{$htmlBody}\r\n\r\n";
            $body .= "--{$boundary}--";

            return $this->sendSMTP($toEmail, $subject, $body, $headers);
        } catch (Exception $e) {
            error_log('Email error: ' . $e->getMessage());
            return false;
        }
    }

    private function sendSMTP(string $toEmail, string $subject, string $body, array $headers): bool
    {
        $socket = fsockopen($this->host, $this->port, $errno, $errstr, 30);
        if (!$socket) {
            error_log("SMTP connection failed: {$errno} - {$errstr}");
            return false;
        }

        fgets($socket, 512);

        $commands = [
            "EHLO localhost\r\n",
            "AUTH LOGIN\r\n",
            base64_encode($this->username) . "\r\n",
            base64_encode($this->password) . "\r\n",
            "MAIL FROM: <noreply@jobportal.com>\r\n",
            "RCPT TO: <{$toEmail}>\r\n",
            "DATA\r\n",
            "Subject: {$subject}\r\n" . implode("\r\n", $headers) . "\r\n\r\n{$body}\r\n.\r\n",
            "QUIT\r\n",
        ];

        foreach ($commands as $command) {
            fputs($socket, $command);
            $response = fgets($socket, 512);
            if (substr($response, 0, 1) === '5') {
                fclose($socket);
                return false;
            }
        }

        fclose($socket);
        return true;
    }

    private function getRegistrationTemplate(string $username, string $verificationUrl): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <body>
            <h2>Hello {$username}!</h2>
            <p>Verify your email: <a href='{$verificationUrl}'>{$verificationUrl}</a></p>
        </body>
        </html>";
    }
}

