<?php
require_once "includes/config_session.php";

$_SESSION["test"] = "raiyanMatadar";

if (isset($_SESSION["test"])) {
    echo $_SESSION["test"];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Home</title>
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

    .container {
      width: 100%;
      max-width: 460px;
      text-align: center;
    }

    /* ── Logo / Brand ── */
    .brand {
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

    h1 span { color: #2563eb; }

    .subtitle {
      font-size: 15px;
      color: #666;
      line-height: 1.6;
      margin-bottom: 40px;
    }

    /* ── Buttons ── */
    .btn-row {
      display: flex;
      gap: 12px;
      justify-content: center;
      flex-wrap: wrap;
      margin-bottom: 48px;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 13px 32px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: 2px solid transparent;
      transition: background .18s, transform .1s, box-shadow .18s;
    }

    .btn:active { transform: scale(.97); }

    .btn-login {
      background: #2563eb;
      color: #fff;
    }
    .btn-login:hover {
      background: #1d4ed8;
      box-shadow: 0 4px 16px rgba(37,99,235,.35);
    }

    .btn-register {
      background: #fff;
      color: #1a1a1a;
      border-color: #d1d5db;
    }
    .btn-register:hover {
      background: #f3f4f6;
      border-color: #9ca3af;
    }

    /* ── Divider ── */
    .divider {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #bbb;
      font-size: 13px;
      margin-bottom: 40px;
    }
    .divider::before,
    .divider::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    /* ── Feature cards ── */
    .features {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 12px;
    }

    .feat {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 20px 12px;
      font-size: 13px;
      color: #555;
      line-height: 1.5;
    }

    .feat-icon { font-size: 22px; margin-bottom: 10px; display: block; }
    .feat strong { display: block; font-size: 13px; font-weight: 600; color: #1a1a1a; margin-bottom: 4px; }

    /* ── Footer ── */
    footer {
      margin-top: 40px;
      font-size: 12px;
      color: #aaa;
    }

    @media (max-width: 360px) {
      h1 { font-size: 28px; }
      .features { grid-template-columns: 1fr 1fr; }
      .features .feat:last-child { grid-column: span 2; }
    }
  </style>
</head>
<body>

  <div class="container">

    <span class="brand">DevsEntity</span>

    <h1>SmartHome Manager<br><span>Manage it better.</span></h1>
    <p class="subtitle">
      A simple, clean starting point for your digital Home.<br>
      Join today or log in to continue.
    </p>

    <div class="btn-row">
      <a href="login.php" class="btn btn-login">
        Log in
      </a>
      <a href="register.php" class="btn btn-register">
        Create account
      </a>  
    </div>
  </div>

</body>
</html>