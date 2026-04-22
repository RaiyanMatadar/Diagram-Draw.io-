<?php 

require_once "includes/config_session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

require_once "includes/dbh.inc.php";

// Get user's devices
$query = "SELECT * FROM devices WHERE user_id = :user_id ORDER BY device_name ASC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $routine_name = trim($_POST["routine_name"]);
    $scheduled_time = $_POST["scheduled_time"];
    $action = $_POST["action"];
    $selected_devices = isset($_POST["devices"]) ? $_POST["devices"] : [];
    
    $devices_json = json_encode($selected_devices);
    
    $query = "INSERT INTO routines (user_id, routine_name, devices, action, scheduled_time) 
              VALUES (:user_id, :routine_name, :devices, :action, :scheduled_time)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":routine_name", $routine_name);
    $stmt->bindParam(":devices", $devices_json);
    $stmt->bindParam(":action", $action);
    $stmt->bindParam(":scheduled_time", $scheduled_time);
    $stmt->execute();
    
    $success = "Routine created successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Add Routine</title>
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

    .checkbox-group {
      max-height: 200px;
      overflow-y: auto;
      border: 1.5px solid #d1d5db;
      border-radius: 8px;
      padding: 12px;
    }

    .checkbox-item {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 8px;
    }

    .checkbox-item:last-child {
      margin-bottom: 0;
    }

    .checkbox-item input[type="checkbox"] {
      width: 16px;
      height: 16px;
      accent-color: #2563eb;
      cursor: pointer;
    }

    .checkbox-item label {
      font-size: 14px;
      font-weight: 400;
      cursor: pointer;
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

    <h1>Create a<br><span>routine.</span></h1>
    <p>
      Schedule automated device actions.<br>
      Set it and forget it.
    </p>

    <div>

      <?php if (isset($success)): ?>
        <div data-status="success"><?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <?php if (count($devices) === 0): ?>
        <div data-status="error">You need to add devices first before creating routines.</div>
        <a href="add_device.php">Add Devices</a>
      <?php else: ?>
        <form method="post" action="add_routine.php">

          <div>
            <label for="routine_name">Routine Name</label>
            <input type="text" id="routine_name" name="routine_name" placeholder="e.g. Night Mode" required>
          </div>

          <div>
            <label for="scheduled_time">Scheduled Time</label>
            <input type="time" id="scheduled_time" name="scheduled_time" required>
          </div>

          <div>
            <label for="action">Action</label>
            <select id="action" name="action" required>
              <option value="">Select action...</option>
              <option value="Turn On">Turn On</option>
              <option value="Turn Off">Turn Off</option>
            </select>
          </div>

          <div>
            <label>Select Devices</label>
            <div class="checkbox-group">
              <?php foreach ($devices as $device): ?>
                <div class="checkbox-item">
                  <input type="checkbox" id="device_<?= $device["id"] ?>" name="devices[]" value="<?= $device["id"] ?>">
                  <label for="device_<?= $device["id"] ?>"><?= htmlspecialchars($device["device_name"]) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <button type="submit">Create Routine</button>

        </form>
      <?php endif; ?>

    </div>

    <a href="dashboard.php">← Back to Dashboard</a>

  </div>

</body>
</html>
