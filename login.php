
<?php

require_once "includes/config_session.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    
    require_once "includes/dbh.inc.php";
    
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user["password_hash"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["email"] = $user["email"];
        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Invalid email or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Log In</title>

  <style>
    *, *::before, *::after {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: #f5f5f7;
      color: #1a1a1a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
    }

    body > div {
      width: 100%;
      max-width: 460px;
      text-align: center;
    }

    body > div > span {
      display: inline-block;
      font-size: 12px;
      letter-spacing: .12em;
      text-transform: uppercase;
      color: #888;
      border: 1px solid #ddd;
      border-radius: 20px;
      padding: 5px 16px;
      margin-bottom: 32px;
    }

    h1 {
      font-size: 36px;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 12px;
    }

    h1 span {
      color: #2563eb;
    }

    body > div > p {
      font-size: 15px;
      color: #666;
      line-height: 1.6;
      margin-bottom: 40px;
    }

    body > div > div {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 32px 28px;
      text-align: left;
      margin-bottom: 24px;
    }

    form > div {
      margin-bottom: 18px;
    }

    label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
    }

    form > div > div {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      margin-bottom: 6px;
    }

    form a {
      font-size: 12px;
      color: #2563eb;
      text-decoration: none;
      font-weight: 500;
    }

    form a:hover {
      text-decoration: underline;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #d1d5db;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      transition: border-color .15s, box-shadow .15s;
      outline: none;
    }

    input:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    input::placeholder {
      color: #9ca3af;
    }

    form > div > div:last-child {
      position: relative;
    }

    form button[type="button"] {
      position: absolute;
      right: 13px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      color: #9ca3af;
    }

    form button[type="button"]:hover {
      color: #2563eb;
    }

    form > div:last-of-type {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
    }

    input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: #2563eb;
      cursor: pointer;
    }

    form > button {
      width: 100%;
      padding: 13px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      background: #2563eb;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    form > button:hover {
      background: #1d4ed8;
      box-shadow: 0 4px 16px rgba(37,99,235,.35);
    }

    form + div {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #bbb;
      font-size: 13px;
      margin: 24px 0;
    }

    form + div::before,
    form + div::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    form + div + div {
      text-align: center;
      font-size: 14px;
      color: #666;
    }

    footer {
      margin-top: 32px;
      font-size: 12px;
      color: #aaa;
      text-align: center;
    }

    @media (max-width: 360px) {
      h1 { font-size: 28px; }
      body > div > div { padding: 24px 18px; }
    }
  </style>
</head>

<body>

  <div>

    <span>DevsEntity</span>

    <h1>Welcome<br><span>back.</span></h1>

    <p>
      Log in to your SmartHome Manager.<br>
      Good to see you again.
    </p>

    <div>

      <?php if (isset($error)): ?>
        <div data-status="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="login.php">

        <div>
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <div>
            <div>
              <label for="password">Password</label>
              <a href="forgot-password.php">Forgot password?</a>
            </div>
          
            <div>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <button type="button">👁</button>
            </div>
        </div>

        <div>
          <input type="checkbox" id="remember">
          <label for="remember">Keep me logged in</label>
        </div>

        <button type="submit" value="submit">Log in</button>

      </form>

      <div>or</div>

      <div>
        Don't have an account? <a href="register.php">Create one</a>
      </div>

    </div>

    <footer>© 2026 DevsEntity · SmartHome Manager</footer>

  </div>

</body>
</html>