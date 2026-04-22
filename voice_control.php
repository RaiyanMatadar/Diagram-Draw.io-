<?php 

require_once "includes/config_session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

require_once "includes/dbh.inc.php";

$command_result = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["voice_command"])) {
    $command = strtolower(trim($_POST["command"]));
    
    // Get all devices
    $query = "SELECT * FROM devices WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    $devices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Parse commands
    if (strpos($command, "turn on all") !== false || strpos($command, "turn on all lights") !== false) {
        $query = "UPDATE devices SET state = 'On' WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $command_result = "✅ All devices turned on successfully!";
        
    } elseif (strpos($command, "turn off all") !== false || strpos($command, "turn off all lights") !== false) {
        $query = "UPDATE devices SET state = 'Off' WHERE user_id = :user_id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        $command_result = "✅ All devices turned off successfully!";
        
    } elseif (preg_match('/turn on (.+)/', $command, $matches)) {
        $device_name = $matches[1];
        $query = "UPDATE devices SET state = 'On' WHERE user_id = :user_id AND LOWER(device_name) LIKE :name";
        $stmt = $pdo->prepare($query);
        $search_name = "%" . $device_name . "%";
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":name", $search_name);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $command_result = "✅ Turned on: " . ucfirst($device_name);
        } else {
            $command_result = "❌ Device not found: " . ucfirst($device_name);
        }
        
    } elseif (preg_match('/turn off (.+)/', $command, $matches)) {
        $device_name = $matches[1];
        $query = "UPDATE devices SET state = 'Off' WHERE user_id = :user_id AND LOWER(device_name) LIKE :name";
        $stmt = $pdo->prepare($query);
        $search_name = "%" . $device_name . "%";
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":name", $search_name);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $command_result = "✅ Turned off: " . ucfirst($device_name);
        } else {
            $command_result = "❌ Device not found: " . ucfirst($device_name);
        }
        
    } elseif (strpos($command, "status") !== false || strpos($command, "what is on") !== false) {
        $active_devices = array_filter($devices, fn($d) => $d["state"] === "On");
        if (count($active_devices) > 0) {
            $device_list = implode(", ", array_map(fn($d) => $d["device_name"], $active_devices));
            $command_result = "📊 Currently active: " . $device_list;
        } else {
            $command_result = "📊 No devices are currently active.";
        }
        
    } elseif (strpos($command, "help") !== false) {
        $command_result = "🎤 Available commands:<br>
                          • 'Turn on all' - Turn on all devices<br>
                          • 'Turn off all' - Turn off all devices<br>
                          • 'Turn on [device name]' - Turn on specific device<br>
                          • 'Turn off [device name]' - Turn off specific device<br>
                          • 'Status' - Check active devices";
                          
    } else {
        $command_result = "❌ Command not recognized. Type 'help' for available commands.";
    }
}

// Get devices for display
$query = "SELECT * FROM devices WHERE user_id = :user_id ORDER BY device_name ASC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Voice Control</title>
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

    .result-box {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 16px;
      margin-top: 18px;
      font-size: 14px;
      line-height: 1.6;
    }

    .device-list {
      list-style: none;
      margin-top: 12px;
    }

    .device-list li {
      padding: 10px;
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 6px;
      margin-bottom: 8px;
      font-size: 14px;
    }

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

    <h1>Voice<br><span>Control.</span></h1>
    <p>
      Type commands to control your devices.<br>
      Try "Turn on all" or "Status".
    </p>

    <div>

      <form method="post" action="voice_control.php">

        <div>
          <label for="command">Voice Command</label>
          <input type="text" id="command" name="command" placeholder='e.g. "Turn on all lights"' value="<?= htmlspecialchars($_POST['command'] ?? '') ?>" required>
        </div>

        <button type="submit" name="voice_command">Execute Command</button>

      </form>

      <?php if ($command_result): ?>
        <div class="result-box"><?= $command_result ?></div>
      <?php endif; ?>

      <h3 style="margin-top: 24px; margin-bottom: 12px; font-size: 15px;">Your Devices:</h3>
      <ul class="device-list">
        <?php foreach ($devices as $device): ?>
          <li>
            <strong><?= htmlspecialchars($device["device_name"]) ?></strong> - 
            <span style="color: <?= $device["state"] === 'On' ? '#10b981' : '#6b7280' ?>">
              <?= $device["state"] ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>

    </div>

    <a href="dashboard.php">← Back to Dashboard</a>

  </div>

</body>
</html>
