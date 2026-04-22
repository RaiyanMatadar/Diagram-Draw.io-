<?php 

require_once "includes/config_session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

require_once "includes/dbh.inc.php";

// Get user's rooms
$query = "SELECT * FROM rooms WHERE user_id = :user_id ORDER BY name ASC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room_id = $_POST["room_id"];
    $device_name = trim($_POST["device_name"]);
    $device_type = $_POST["device_type"];
    
    // Build features based on device type
    $features = "";
    if ($device_type === "Thermostat") {
        $features = json_encode(["temp_min" => 18, "temp_max" => 30, "current_temp" => 24]);
    } elseif ($device_type === "Fan") {
        $features = json_encode(["speed" => "Medium", "speeds" => ["Low", "Medium", "High"]]);
    } elseif ($device_type === "Lamp") {
        $features = json_encode(["brightness" => 50, "color" => "Warm"]);
    } elseif ($device_type === "Smart AC") {
        $features = json_encode(["temp" => 22, "mode" => "Cool"]);
    }
    
    $query = "INSERT INTO devices (user_id, room_id, device_name, device_type, features, is_connected) 
              VALUES (:user_id, :room_id, :device_name, :device_type, :features, 0)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":room_id", $room_id);
    $stmt->bindParam(":device_name", $device_name);
    $stmt->bindParam(":device_type", $device_type);
    $stmt->bindParam(":features", $features);
    $stmt->execute();
    
    $success = "Device created! Click 'Connect' on the dashboard to activate it.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Add Device</title>
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

    body > div > h1 {
      font-size: 36px;
      font-weight: 700;
      line-height: 1.2;
      margin-bottom: 12px;
    }

    body > div > h1 > span { color: #2563eb; }

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

    body > div > div > form > div > input,
    body > div > div > form > div > select {
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

    body > div > div > form > div > input:focus,
    body > div > div > form > div > select:focus {
      border-color: #2563eb;
      box-shadow: 0 0 0 3px rgba(37,99,235,.12);
    }

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

    body > div > a {
      display: block;
      margin-top: 20px;
      font-size: 14px;
      color: #2563eb;
      font-weight: 600;
      text-decoration: none;
    }

    body > div > a:hover { text-decoration: underline; }

    [data-status] {
      font-size: 13px;
      padding: 10px 14px;
      border-radius: 8px;
      margin-bottom: 18px;
    }

    [data-status="error"]   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    [data-status="success"] { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

    @media (max-width: 360px) {
      body > div > h1  { font-size: 28px; }
      body > div > div { padding: 24px 18px; }
    }
  </style>
</head>
<body>

  <div>

    <span>DevsEntity</span>

    <h1>Add a new<br><span>device.</span></h1>
    <p>
      Create and configure a smart device.<br>
      Choose the type and features.
    </p>

    <div>

      <?php if (isset($success)): ?>
        <div data-status="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <?php if (count($rooms) === 0): ?>
        <div data-status="error">You need to create a room first before adding devices.</div>
        <a href="enter_room_data.php">Create a Room</a>
      <?php else: ?>
        <form method="post" action="add_device.php">

          <div>
            <label for="room_id">Room</label>
            <select id="room_id" name="room_id" required>
              <option value="">Select a room...</option>
              <?php foreach ($rooms as $room): ?>
                <option value="<?= $room["id"] ?>"><?= htmlspecialchars($room["name"]) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label for="device_name">Device Name</label>
            <input type="text" id="device_name" name="device_name" placeholder="e.g. Smart Lamp" required>
          </div>

          <div>
            <label for="device_type">Device Type</label>
            <select id="device_type" name="device_type" required>
              <option value="">Select device type...</option>
              <option value="Thermostat">Thermostat</option>
              <option value="Fan">Fan</option>
              <option value="Lamp">Lamp</option>
              <option value="Smart AC">Smart AC</option>
              <option value="Generic">Generic Device</option>
            </select>
          </div>

          <button type="submit">Create Device</button>

        </form>
      <?php endif; ?>

    </div>

    <a href="dashboard.php">← Back to Dashboard</a>

  </div>

</body>
</html>
