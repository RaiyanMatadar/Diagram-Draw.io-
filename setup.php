<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Setup</title>
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

    input[type="text"] {
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
      margin-top: 10px;
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
    }

    form + div + div a {
      display: inline-block;
      width: 100%;
      padding: 13px;
      border-radius: 8px;
      font-size: 15px;
      font-weight: 600;
      text-align: center;
      text-decoration: none;
      background: #111827;
      color: #fff;
    }

    form + div + div a:hover {
      background: #000;
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

    <h1>Set up<br><span>your space.</span></h1>

    <p>
      Add a room and connect your device.<br>
      You can change this later.
    </p>

    <div>

      <form method="POST" action="setup.php">

        <div>
          <label for="room">Room name</label>
          <input
            type="text"
            id="room"
            name="room"
            placeholder="e.g. Living Room"
            required
          >
        </div>

        <div>
          <label for="device">Device name</label>
          <input
            type="text"
            id="device"
            name="device"
            placeholder="e.g. Smart Light"
            required
          >
        </div>

        <button type="submit">Save & Continue</button>

      </form>

      <div>or</div>

      <div>
        <a href="dashboard.php">Go to dashboard</a>
      </div>

    </div>

    <footer>© 2026 DevsEntity · SmartHome Manager</footer>

  </div>

</body>
</html>