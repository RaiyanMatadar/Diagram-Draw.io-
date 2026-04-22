<?php 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room = $_POST["room"];
    $device = $_POST["device"];
    
    require_once "includes/dbh.inc.php";

    $query = "INSERT INTO userdata (room, device) 
            VALUES (:room, :device);";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":room",$room);
    $stmt->bindParam(":device",$device);
    $stmt->execute();


}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Add Room</title>
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

    /* ── Divider ── */
    body > div > div > div {
      display: flex;
      align-items: center;
      gap: 12px;
      color: #bbb;
      font-size: 13px;
      margin: 24px 0 0;
    }

    body > div > div > div::before,
    body > div > div > div::after {
      content: '';
      flex: 1;
      height: 1px;
      background: #e5e7eb;
    }

    /* ── Dashboard link ── */
    body > div > a {
      display: block;
      margin-top: 20px;
      font-size: 14px;
      color: #2563eb;
      font-weight: 600;
      text-decoration: none;
    }

    body > div > a:hover { text-decoration: underline; }

    @media (max-width: 360px) {
      body > div > h1  { font-size: 28px; }
      body > div > div { padding: 24px 18px; }
    }
  </style>
</head>
<body>

  <div>

    <span>DevsEntity</span>

    <h1>Add a new<br><span>room.</span></h1>
    <p>
      Configure your smart room below.<br>
      Takes just a moment.
    </p>

    <div>

      <form action="enter_room_data.php" method="post">

        <div>
          <label for="room">room</label>
          <input type="text" id="room" name="room" placeholder="e.g. Master room">
        </div>

        <div>
          <label for="device">Device</label>
          <input type="text" id="device" name="device" placeholder="e.g. Smart Light">
        </div>

        <button type="submit">Submit</button>

      </form>

    </div>

    <a href="dashboard.php">← View Dashboard</a>

  </div>

</body>
</html>