<?php 

require_once "includes/config_session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$username = $_SESSION["username"];

require_once "includes/dbh.inc.php";

// Handle device toggle
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["toggle_device"])) {
    $device_id = $_POST["device_id"];
    $new_state = $_POST["new_state"];
    
    $query = "UPDATE devices SET state = :state WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":state", $new_state);
    $stmt->bindParam(":id", $device_id);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    
    header("Location: dashboard.php");
    exit();
}

// Handle emergency mode
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["emergency_mode"])) {
    $query = "UPDATE devices SET state = 'Off' WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();
    
    $emergency_success = "Emergency mode activated! All devices turned off.";
}

// Get all rooms for user
$query = "SELECT * FROM rooms WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all devices for user
$query = "SELECT d.*, r.name as room_name FROM devices d 
          LEFT JOIN rooms r ON d.room_id = r.id 
          WHERE d.user_id = :user_id ORDER BY d.created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get active routines
$query = "SELECT * FROM routines WHERE user_id = :user_id AND is_active = 1 ORDER BY scheduled_time ASC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total energy usage
$total_energy = 0;
$malfunction_devices = [];
foreach ($devices as $device) {
    $total_energy += $device["energy_usage"];
    if ($device["malfunction"]) {
        $malfunction_devices[] = $device;
    }
}

// Check for malfunction alerts (devices used more than 24 hours)
foreach ($devices as $device) {
    if ($device["hours_used"] > 24 && !$device["malfunction"]) {
        $query = "UPDATE devices SET malfunction = 1 WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":id", $device["id"]);
        $stmt->execute();
        $malfunction_devices[] = $device;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Dashboard</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Segoe UI', system-ui, sans-serif;
      background: #f5f5f7;
      color: #1a1a1a;
      min-height: 100vh;
      padding: 24px;
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
    }

    /* ── Header ── */
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 32px;
      flex-wrap: wrap;
      gap: 16px;
    }

    .header h1 {
      font-size: 32px;
      font-weight: 700;
    }

    .header h1 span { color: #2563eb; }

    .header-actions {
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: background .18s, transform .1s;
    }

    .btn:active { transform: scale(.97); }

    .btn-primary {
      background: #2563eb;
      color: #fff;
    }

    .btn-primary:hover { background: #1d4ed8; }

    .btn-danger {
      background: #ef4444;
      color: #fff;
    }

    .btn-danger:hover { background: #dc2626; }

    .btn-secondary {
      background: #fff;
      color: #1a1a1a;
      border: 1.5px solid #d1d5db;
    }

    .btn-secondary:hover { background: #f3f4f6; }

    /* ── Stats Cards ── */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 16px;
      margin-bottom: 32px;
    }

    .stat-card {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 20px;
    }

    .stat-card h3 {
      font-size: 13px;
      color: #666;
      margin-bottom: 8px;
      font-weight: 600;
    }

    .stat-card .value {
      font-size: 28px;
      font-weight: 700;
      color: #2563eb;
    }

    /* ── Alerts ── */
    .alert {
      padding: 14px 18px;
      border-radius: 10px;
      margin-bottom: 20px;
      font-size: 14px;
    }

    .alert-warning {
      background: #fef3c7;
      color: #92400e;
      border: 1px solid #fcd34d;
    }

    .alert-success {
      background: #f0fdf4;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    /* ── Room Sections ── */
    .room-section {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 24px;
    }

    .room-section h2 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 16px;
      color: #1a1a1a;
    }

    .devices-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 16px;
    }

    .device-card {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      padding: 18px;
      transition: box-shadow .18s;
    }

    .device-card:hover {
      box-shadow: 0 4px 12px rgba(0,0,0,.08);
    }

    .device-card.malfunction {
      border-color: #ef4444;
      background: #fef2f2;
    }

    .device-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .device-name {
      font-size: 16px;
      font-weight: 600;
    }

    .device-status {
      font-size: 12px;
      padding: 4px 10px;
      border-radius: 12px;
      font-weight: 600;
    }

    .status-on {
      background: #d1fae5;
      color: #065f46;
    }

    .status-off {
      background: #f3f4f6;
      color: #6b7280;
    }

    .status-malfunction {
      background: #fee2e2;
      color: #991b1b;
    }

    .device-features {
      font-size: 13px;
      color: #666;
      margin-bottom: 12px;
      line-height: 1.6;
    }

    .device-actions {
      display: flex;
      gap: 8px;
    }

    .btn-small {
      padding: 8px 14px;
      font-size: 13px;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: background .18s;
    }

    .btn-toggle-on {
      background: #10b981;
      color: #fff;
    }

    .btn-toggle-on:hover { background: #059669; }

    .btn-toggle-off {
      background: #6b7280;
      color: #fff;
    }

    .btn-toggle-off:hover { background: #4b5563; }

    /* ── Routines Section ── */
    .routine-list {
      list-style: none;
    }

    .routine-item {
      background: #f9fafb;
      border: 1px solid #e5e7eb;
      border-radius: 8px;
      padding: 14px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .routine-info h4 {
      font-size: 15px;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .routine-info p {
      font-size: 13px;
      color: #666;
    }

    .routine-time {
      font-size: 18px;
      font-weight: 700;
      color: #2563eb;
    }

    /* ── Empty State ── */
    .empty-state {
      text-align: center;
      padding: 60px 20px;
      color: #999;
    }

    .empty-state-icon {
      font-size: 48px;
      margin-bottom: 16px;
    }

    .empty-state h3 {
      font-size: 18px;
      margin-bottom: 8px;
      color: #666;
    }

    .empty-state p {
      font-size: 14px;
      margin-bottom: 20px;
    }

    /* ── Footer ── */
    footer {
      margin-top: 40px;
      text-align: center;
      font-size: 12px;
      color: #aaa;
    }

    @media (max-width: 768px) {
      .header h1 { font-size: 24px; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 480px) {
      .stats-grid { grid-template-columns: 1fr; }
      .devices-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <div class="container">

    <div class="header">
      <h1>Welcome, <span><?= htmlspecialchars($username) ?></span></h1>
      <div class="header-actions">
        <a href="add_device.php" class="btn btn-primary">+ Add Device</a>
        <a href="add_routine.php" class="btn btn-secondary">+ Add Routine</a>
        <a href="voice_control.php" class="btn btn-secondary">🎤 Voice Control</a>
        <a href="energy_report.php" class="btn btn-secondary">⚡ Energy Report</a>
        <form method="POST" style="display: inline;">
          <button type="submit" name="emergency_mode" class="btn btn-danger" onclick="return confirm('Activate emergency mode? All devices will turn off.')">🚨 Emergency</button>
        </form>
        <a href="logout.php" class="btn btn-secondary">Logout</a>
      </div>
    </div>

    <?php if (isset($emergency_success)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($emergency_success) ?></div>
    <?php endif; ?>

    <?php if (count($malfunction_devices) > 0): ?>
      <div class="alert alert-warning">
        <strong>⚠️ Malfunction Alert:</strong> 
        <?php foreach ($malfunction_devices as $malfunction_device): ?>
          <?= htmlspecialchars($malfunction_device["device_name"]) ?> has been running for over 24 hours. Consider resetting it.
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Devices</h3>
        <div class="value"><?= count($devices) ?></div>
      </div>
      <div class="stat-card">
        <h3>Active Now</h3>
        <div class="value"><?= count(array_filter($devices, fn($d) => $d["state"] === "On")) ?></div>
      </div>
      <div class="stat-card">
        <h3>Total Rooms</h3>
        <div class="value"><?= count($rooms) ?></div>
      </div>
      <div class="stat-card">
        <h3>Energy Used</h3>
        <div class="value"><?= number_format($total_energy, 1) ?> kWh</div>
      </div>
    </div>

    <?php if (count($devices) === 0): ?>
      <div class="room-section">
        <div class="empty-state">
          <div class="empty-state-icon">🏠</div>
          <h3>No devices yet</h3>
          <p>Add your first device to get started with smart home management.</p>
          <a href="add_device.php" class="btn btn-primary">Add Your First Device</a>
        </div>
      </div>
    <?php else: ?>
      <?php foreach ($rooms as $room): ?>
        <div class="room-section">
          <h2><?= htmlspecialchars($room["name"]) ?></h2>
          <div class="devices-grid">
            <?php 
            $room_devices = array_filter($devices, fn($d) => $d["room_id"] == $room["id"]);
            foreach ($room_devices as $device): 
            ?>
              <div class="device-card <?= $device["malfunction"] ? 'malfunction' : '' ?>">
                <div class="device-header">
                  <div class="device-name"><?= htmlspecialchars($device["device_name"]) ?></div>
                  <div class="device-status <?= $device["malfunction"] ? 'status-malfunction' : ($device["state"] === 'On' ? 'status-on' : 'status-off') ?>">
                    <?= $device["malfunction"] ? '⚠️ Malfunction' : $device["state"] ?>
                  </div>
                </div>
                <div class="device-features">
                  <div>Type: <?= htmlspecialchars($device["device_type"]) ?></div>
                  <div>Energy: <?= number_format($device["energy_usage"], 1) ?> kWh</div>
                  <?php if ($device["features"]): ?>
                    <div>Features: <?= htmlspecialchars($device["features"]) ?></div>
                  <?php endif; ?>
                </div>
                <div class="device-actions">
                  <form method="POST" style="flex: 1;">
                    <input type="hidden" name="device_id" value="<?= $device["id"] ?>">
                    <input type="hidden" name="new_state" value="<?= $device["state"] === 'On' ? 'Off' : 'On' ?>">
                    <button type="submit" name="toggle_device" class="btn-small <?= $device["state"] === 'On' ? 'btn-toggle-off' : 'btn-toggle-on' ?>" style="width: 100%;">
                      <?= $device["state"] === 'On' ? 'Turn Off' : 'Turn On' ?>
                    </button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php if (count($routines) > 0): ?>
      <div class="room-section">
        <h2>Active Routines</h2>
        <ul class="routine-list">
          <?php foreach ($routines as $routine): ?>
            <li class="routine-item">
              <div class="routine-info">
                <h4><?= htmlspecialchars($routine["routine_name"]) ?></h4>
                <p>Action: <?= htmlspecialchars($routine["action"]) ?></p>
              </div>
              <div class="routine-time"><?= date("h:i A", strtotime($routine["scheduled_time"])) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <footer>© 2026 DevsEntity · SmartHome Manager</footer>

  </div>

</body>
</html>
