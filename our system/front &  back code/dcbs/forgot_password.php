<?php
session_start();
require_once __DIR__ . '/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $message = '<p style="color:red;">Please enter your email address.</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<p style="color:red;">Invalid email format.</p>';
    } else {
        // Check if email exists and user is handler
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ? AND user_type = 'handler' LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            // Generate a password reset token (random string)
            $token = bin2hex(random_bytes(16));
            $user_id = $user['id'];

            // Save token and expiration (e.g., 1 hour later) to DB (you need a reset_tokens table or fields)
            $expires_at = date('Y-m-d H:i:s', time() + 3600);

            // For example, create a reset_tokens table:
            // CREATE TABLE reset_tokens (
            //   user_id INT,
            //   token VARCHAR(64),
            //   expires_at DATETIME,
            //   PRIMARY KEY(user_id)
            // );

            // Upsert token
            $stmt2 = $conn->prepare("REPLACE INTO reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt2->bind_param('iss', $user_id, $token, $expires_at);
            $stmt2->execute();
            $stmt2->close();

            // Send email with reset link (replace with actual mail function/config)
            $reset_link = "http://yourdomain.com/reset_password.php?token=$token";

            // Simple mail placeholder (use proper mail function in production)
            // mail($email, "Password Reset Request", "Hi {$user['name']},\n\nClick this link to reset your password:\n$reset_link\n\nThis link will expire in 1 hour.");

            $message = '<p style="color:green;">If that email exists in our system, a reset link has been sent.</p>';
        } else {
            $message = '<p style="color:green;">If that email exists in our system, a reset link has been sent.</p>';
            // Do not reveal if email exists or not for security
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Forgot Password | DCBS</title>
<style>
  body {
    font-family: Arial, sans-serif;
    background: #f4f6f8;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }
  form {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    width: 320px;
  }
  h2 {
    margin-bottom: 20px;
    color: #ffbd59;
    text-align: center;
  }
  input[type="email"] {
    width: 100%;
    padding: 12px;
    margin: 12px 0 20px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 15px;
  }
  button {
    background: #ffbd59;
    border: none;
    padding: 12px;
    width: 100%;
    font-weight: bold;
    border-radius: 6px;
    cursor: pointer;
  }
  button:hover {
    background: #e6ac4c;
  }
  .message {
    margin-bottom: 15px;
    text-align: center;
  }
  a {
    display: block;
    text-align: center;
    margin-top: 12px;
    text-decoration: none;
    color: #555;
    font-size: 14px;
  }
  a:hover {
    color: #ffbd59;
  }
</style>
</head>
<body>

<form method="post" action="">
  <h2>Forgot Password</h2>

  <div class="message"><?= $message ?></div>

  <label for="email">Enter your email address</label>
  <input type="email" name="email" id="email" required placeholder="handler@example.com" />

  <button type="submit">Send Reset Link</button>

  <a href="login.php">Back to Login</a>
</form>

</body>
</html>
