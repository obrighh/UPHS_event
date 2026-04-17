<?php
/**
 * Email Configuration Diagnostic Tool
 * Run this to test if the SMTP connection is working
 * Access: http://localhost/UPHS_event/test_email.php
 */

// Configuration
$emailPassword = '';
$configFile = __DIR__ . '/.env.local';

if (file_exists($configFile)) {
    include $configFile;
    $emailPassword = $_ENV['SMTP_PASSWORD'] ?? '';
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>Email Diagnostic Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .test-box { background: #f9f9f9; padding: 1.5rem; margin: 1rem 0; border-radius: 4px; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; color: #155724; background: #d4edda; }
        .error { border-left-color: #dc3545; color: #721c24; background: #f8d7da; }
        .warning { border-left-color: #ffc107; color: #856404; background: #fff3cd; }
        code { background: #f4f4f4; padding: 0.2rem 0.4rem; border-radius: 3px; font-family: 'Courier New', monospace; font-size: 0.9rem; }
        .test-result { margin: 1rem 0; padding: 0.5rem; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>📧 Email Configuration Diagnostic</h1>

        <?php
        echo '<div class="test-box warning">';
        echo '<strong>Step 1: Check Configuration File</strong>';
        if (file_exists($configFile)) {
            echo '<div class="test-result success">✓ .env.local file found</div>';
            if (empty($emailPassword)) {
                echo '<div class="test-result error">✗ ERROR: Password is empty. Check .env.local file</div>';
            } else {
                echo '<div class="test-result success">✓ Password loaded: ' . htmlspecialchars(substr($emailPassword, 0, 3) . '***' . substr($emailPassword, -2)) . '</div>';
            }
        } else {
            echo '<div class="test-result error">✗ ERROR: .env.local file not found</div>';
        }
        echo '</div>';

        echo '<div class="test-box warning">';
        echo '<strong>Step 2: Check PHPMailer Installation</strong>';
        if (file_exists(__DIR__ . '/includes/PHPMailer.php')) {
            echo '<div class="test-result success">✓ PHPMailer.php found</div>';
        } else {
            echo '<div class="test-result error">✗ ERROR: PHPMailer.php not found</div>';
        }
        if (file_exists(__DIR__ . '/includes/SMTP.php')) {
            echo '<div class="test-result success">✓ SMTP.php found</div>';
        } else {
            echo '<div class="test-result error">✗ ERROR: SMTP.php not found</div>';
        }
        echo '</div>';

        echo '<div class="test-box warning">';
        echo '<strong>Step 3: Test SMTP Connection</strong>';
        if (!empty($emailPassword)) {
            try {
                require_once __DIR__ . '/includes/PHPMailer.php';
                require_once __DIR__ . '/includes/SMTP.php';
                require_once __DIR__ . '/includes/Exception.php';

                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'g22-0501-919@gma.uphsl.edu.ph';
                $mail->Password = $emailPassword;
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPDebug = 0;

                // Try to connect
                if ($mail->smtpConnect()) {
                    echo '<div class="test-result success">✓ SMTP Connection Successful!</div>';
                    echo '<div class="test-result success">✓ Gmail credentials are correct</div>';
                    $mail->smtpClose();
                } else {
                    echo '<div class="test-result error">✗ SMTP Connection Failed</div>';
                }
            } catch (\Exception $e) {
                echo '<div class="test-result error">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
        } else {
            echo '<div class="test-result error">✗ No password to test</div>';
        }
        echo '</div>';

        echo '<div class="test-box warning">';
        echo '<strong>Step 4: Check Contact Form Submissions</strong>';
        try {
            require __DIR__ . '/actions/conn.php';
            $result = $conn->query('SELECT COUNT(*) as total FROM contact');
            if ($result) {
                $row = $result->fetch_assoc();
                $total = $row['total'];
                echo '<div class="test-result success">✓ Database connection successful</div>';
                echo '<div class="test-result">Total contact inquiries in database: <strong>' . $total . '</strong></div>';

                $hasCreatedAt = false;
                $columnCheck = $conn->query("SHOW COLUMNS FROM `contact` LIKE 'created_at'");
                if ($columnCheck && $columnCheck->num_rows > 0) {
                    $hasCreatedAt = true;
                }

                // Show last 5 submissions
                $columns = $hasCreatedAt ? 'c_id, name, email, created_at' : 'c_id, name, email';
                $lastResult = $conn->query("SELECT {$columns} FROM contact ORDER BY c_id DESC LIMIT 5");
                if ($lastResult && $lastResult->num_rows > 0) {
                    echo '<div style="margin-top: 1rem;"><strong>Last 5 submissions:</strong></div>';
                    echo '<table style="width: 100%; border-collapse: collapse; margin-top: 0.5rem;">';
                    echo '<tr style="border-bottom: 1px solid #ddd;">';
                    echo '<th style="padding: 0.5rem; text-align:left;">Name</th>';
                    echo '<th style="padding: 0.5rem; text-align:left;">Email</th>';
                    if ($hasCreatedAt) {
                        echo '<th style="padding: 0.5rem; text-align:left;">Submitted</th>';
                    }
                    echo '</tr>';

                    while ($last = $lastResult->fetch_assoc()) {
                        echo '<tr style="border-bottom: 1px solid #ddd;">';
                        echo '<td style="padding: 0.5rem;">' . htmlspecialchars($last['name']) . '</td>';
                        echo '<td style="padding: 0.5rem;">' . htmlspecialchars($last['email']) . '</td>';
                        if ($hasCreatedAt) {
                            echo '<td style="padding: 0.5rem;">' . htmlspecialchars($last['created_at']) . '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</table>';
                }
            } else {
                echo '<div class="test-result error">✗ Database error: ' . htmlspecialchars($conn->error) . '</div>';
            }
        } catch (\Exception $e) {
            echo '<div class="test-result error">✗ Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        echo '</div>';

        echo '<div class="test-box warning">';
        echo '<strong>Step 5: Possible Issues & Solutions</strong>';
        echo '<ul>';
        echo '<li><strong>Email in Spam:</strong> Check your spam/junk folder in Gmail</li>';
        echo '<li><strong>Google Security:</strong> Gmail may block the connection. Check: <a href="https://myaccount.google.com/security" target="_blank">https://myaccount.google.com/security</a></li>';
        echo '<li><strong>Less Secure Apps:</strong> Log into Gmail and check for security alerts</li>';
        echo '<li><strong>Two-Factor Auth:</strong> If enabled, you may need an App Password instead</li>';
        echo '<li><strong>No Contact Submissions:</strong> Make sure you submitted a test contact form first</li>';
        echo '</ul>';
        echo '</div>';

        echo '<div class="test-box" style="background: #d1ecf1; border-left-color: #0c5460;">';
        echo '<strong>📝 Next Steps:</strong>';
        echo '<ol>';
        echo '<li>Make sure all tests above show ✓ (green)</li>';
        echo '<li>Submit a test contact message using the contact form</li>';
        echo '<li>Wait 1-2 minutes for the email to arrive</li>';
        echo '<li>Check your Gmail inbox and spam folder</li>';
        echo '<li>If still not working, share the error messages above</li>';
        echo '</ol>';
        echo '</div>';
        ?>
    </div>
</body>
</html>
