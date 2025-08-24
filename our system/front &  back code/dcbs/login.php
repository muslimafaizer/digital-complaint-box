<?php
session_start();
$active = 'login';
require_once 'config.php'; // make sure this exists for DB ($conn)

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $user_type = $_POST['user_type'] ?? 'complainer';

    if ($email === '' || $password === '') {
        $err = "Please enter email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, user_type FROM users WHERE email = ? AND user_type = ? LIMIT 1");
        $stmt->bind_param('ss', $email, $user_type);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'user_type' => $user['user_type']
                ];
                if ($user['user_type'] === 'handler') {
                    header('Location: handler_dashboard.php');
                    exit;
                } else {
                    header('Location: complainer_dashboard.php');
                    exit;
                }
            } else {
                $err = "Incorrect password.";
            }
        } else {
            $err = "No user found with that email and user type.";
        }
        $stmt->close();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Login | DCBS</title>

  <style>
    :root{--logo:#ffbd59;--muted:#4b5563;--white:#fff}
    *{box-sizing:border-box;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial}
    body{margin:0;background:var(--white);color:#111;min-height:100vh}
    main{max-width:900px;margin:36px auto;padding:18px}
    .card{background:#fff;border-radius:10px;box-shadow:0 8px 30px rgba(10,10,10,0.06);padding:24px;display:flex;gap:30px;align-items:flex-start}
    .left{flex:1}
    .right{width:320px;text-align:center}
    h1{margin:0 0 8px 0}
    p.lead{color:var(--muted);margin:0 0 14px 0}
    label{display:block;margin-top:12px;font-weight:600}
    input[type=email],input[type=password]{width:100%;padding:10px;border:1px solid #e5e7eb;border-radius:8px;margin-top:6px}
    .btn-submit{margin-top:16px;padding:10px 18px;border-radius:8px;border:none;background:var(--logo);color:#000;font-weight:700;cursor:pointer}
    .error{background:#fee2e2;color:#991b1b;padding:10px;border-radius:6px;margin-bottom:12px}
    .small{color:var(--muted);font-size:13px}
    @media(max-width:800px){ .card{flex-direction:column} .right{width:100%} }
  </style>
</head>
<body>

<?php include __DIR__ . '/topbar.php'; ?>

<main>
  <div class="card" role="region" aria-label="Login card">
    <div class="left">
      <h1>Welcome Back</h1>
      <p class="lead">Login to your account and manage complaints.</p>

      <?php if($err !== ''): ?>
        <div class="error"><?= htmlspecialchars($err) ?></div>
      <?php endif; ?>

      <form method="post" action="#" id="loginForm" novalidate>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="you@example.com" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Your password" required>

        <label for="user_type">Login as</label>
        <div style="display:flex;gap:12px;margin-top:6px">
          <label style="font-weight:500"><input type="radio" name="user_type" value="complainer" checked> Complainer</label>
          <label style="font-weight:500"><input type="radio" name="user_type" value="handler"> Handler</label>
        </div>

        <div>
          <button class="btn-submit" type="submit">Login</button>
        </div>

        <div style="margin-top:12px">
          <a href="forgot_password.php">Forgot password?</a> • <a href="register.php">Create account</a>
        </div>
      </form>
    </div>

    
</main>
<?php include 'footer.php'; ?>
</body>
</html>
