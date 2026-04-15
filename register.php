<?php 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $hashed = password_hash($password, PASSWORD_DEFAULT);
  
    require_once "includes/dbh.inc.php";
  
    $query = "INSERT INTO users (username, email, password_hash) 
              VALUES (:username, :email, :password_hash);";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":username",$username);
    $stmt->bindParam(":email",$email);
    $stmt->bindParam(":password_hash", $hashed);

    $stmt->execute();

    header("Location: enter_room_data.php");
    exit(); 

    //$_SESSION["username"] = $username;
    //$_SESSION["email"] = $email;
    //$_SESSION["password"] = $password;
    //$_SESSION["hashed"] = $hashed;

} 


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Create Account</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

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

    /* ── Container ── */
    body > div {
      width: 100%;
      max-width: 460px;
      text-align: center;
    }

    /* ── Brand pill ── */
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

    /* ── Heading ── */
    body > div > h1 {
      font-size: 36px;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 12px;
    }

    body > div > h1 > span { color: #2563eb; }

    /* ── Subtitle ── */
    body > div > p {
      font-size: 15px;
      color: #666;
      line-height: 1.6;
      margin-bottom: 40px;
    }

    /* ── Card ── */
    body > div > div {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 32px 28px;
      text-align: left;
      margin-bottom: 24px;
    }

    /* ── Form groups ── */
    body > div > div > form > div {
      margin-bottom: 18px;
    }

    body > div > div > form > div > label {
      display: block;
      font-size: 13px;
      font-weight: 600;
      color: #374151;
      margin-bottom: 6px;
    }

    body > div > div > form > div > input {
      width: 100%;
      padding: 11px 14px;
      border: 1.5px solid #d1d5db;
      border-radius: 8px;
      font-size: 15px;
      font-family: inherit;
      color: #1a1a1a;
      background: #fff;
      transition: border-color .15s, box-shadow .15s;
      outline: none;
    }

    body > div > div > form > div > input:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

    body > div > div > form > div > input::placeholder { color: #9ca3af; }

    /* ── Password strength label ── */
    #strengthLabel {
      font-size: 12px;
      color: #9ca3af;
      margin-top: 5px;
      min-height: 16px;
    }

    /* ── Submit button ── */
    body > div > div > form > button {
      width: 100%;
      padding: 13px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      font-family: inherit;
      background: #2563eb;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: background .18s, transform .1s, box-shadow .18s;
      margin-top: 4px;
    }

    body > div > div > form > button:hover {
      background: #1d4ed8;
      box-shadow: 0 4px 16px rgba(37,99,235,.35);
    }

    body > div > div > form > button:active { transform: scale(.97); }

    /* ── Divider  (first direct div child of card, after the form) ── */
    body > div > div > div:first-of-type {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #bbb;
      font-size: 13px;
      margin: 24px 0;
    }

    body > div > div > div:first-of-type::before,
    body > div > div > div:first-of-type::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    /* ── Login row (last direct div child of card) ── */
    body > div > div > div:last-of-type {
      text-align: center;
      font-size: 14px;
      color: #666;
    }

    body > div > div > div:last-of-type > a {
      color: #2563eb;
      font-weight: 600;
      text-decoration: none;
    }

    body > div > div > div:last-of-type > a:hover { text-decoration: underline; }

    /* ── Error / Success messages (toggled via data-status attr in PHP/JS) ── */
    [data-status] {
      font-size: 13px;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 18px;
    }

    [data-status="error"]   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    [data-status="success"] { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

    /* ── Footer ── */
    footer {
      margin-top: 32px;
      font-size: 12px;
      color: #aaa;
      text-align: center;
    }

    @media (max-width: 360px) {
      body > div > h1       { font-size: 28px; }
      body > div > div      { padding: 24px 18px; }
    }
  </style>
</head>
<body>

  <div>

    <span>DevsEntity</span>

    <h1>Create your<br><span>account.</span></h1>
    <p>
      Join SmartHome Manager today.<br>
      It only takes a few seconds.
    </p>

    <div>

      <form method="post" action="register.php" id="registerForm">

        <div>
          <label for="username">Username</label>
          <input
            type="text"
            id="username"
            name="username"
            placeholder="e.g. raiyan"
            required
            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
          >
        </div>

        <div>
          <label for="email">Email address</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="you@example.com"
            required
            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          >
        </div>

        <div>
          <label for="password">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="Minimum 8 characters"
            required
          >
          <div id="strengthLabel"></div>
        </div>

        <button type="submit" value="submit">Create account</button>

      </form>

      <div>or</div>

      <div>
        Already have an account? <a href="login.php">Log in</a>
      </div>

    </div>

  </div>

</body>
</html>