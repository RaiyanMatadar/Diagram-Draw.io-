<?php 

require_once "includes/config_session.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

require_once "includes/dbh.inc.php";

// Get all devices with energy data
$query = "SELECT d.*, r.name as room_name FROM devices d 
          LEFT JOIN rooms r ON d.room_id = r.id 
          WHERE d.user_id = :user_id ORDER BY d.energy_usage DESC";
$stmt = $pdo->prepare($query);
$stmt->bindParam(":user_id", $user_id);
$stmt->execute();
$devices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$total_energy = 0;
$total_hours = 0;
foreach ($devices as $device) {
    $total_energy += $device["energy_usage"];
    $total_hours += $device["hours_used"];
}

// Energy efficiency recommendations
$recommendations = [];
foreach ($devices as $device) {
    if ($device["state"] === "On" && $device["hours_used"] > 12) {
        $recommendations[] = "Consider turning off " . $device["device_name"] . " - it's been running for " . round($device["hours_used"], 1) . " hours.";
    }
    if ($device["energy_usage"] > 50) {
        $recommendations[] = $device["device_name"] . " has high energy consumption (" . number_format($device["energy_usage"], 1) . " kWh). Check if it's needed.";
    }
}

if ($total_energy > 100) {
    $recommendations[] = "Your total energy usage is high. Try activating 'sleep mode' routines at night to reduce consumption.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DevsEntity — Energy Report</title>
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
      max-width: 1000px;
      margin: 0 auto;
    }

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

    .btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      border: none;
      transition: background .18s;
    }

    .btn-secondary {
      background: #fff;
      color: #1a1a1a;
      border: 1.5px solid #d1d5db;
    }

    .btn-secondary:hover { background: #f3f4f6; }

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

    .section {
      background: #fff;
      border: 1px solid #e5e7eb;
      border-radius: 14px;
      padding: 24px;
      margin-bottom: 24px;
    }

    .section h2 {
      font-size: 20px;
      font-weight: 700;
      margin-bottom: 16px;
    }

    .energy-table {
      width: 100%;
      border-collapse: collapse;
    }

    .energy-table th,
    .energy-table td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }

    .energy-table th {
      font-size: 13px;
      font-weight: 600;
      color: #666;
      background: #f9fafb;
    }

    .energy-table td {
      font-size: 14px;
    }

    .energy-bar {
      width: 100%;
      height: 8px;
      background: #e5e7eb;
      border-radius: 4px;
      overflow: hidden;
    }

    .energy-bar-fill {
      height: 100%;
      background: #2563eb;
      border-radius: 4px;
      transition: width 0.3s;
    }

    .recommendation-list {
      list-style: none;
    }

    .recommendation-item {
      background: #fef3c7;
      border: 1px solid #fcd34d;
      border-radius: 8px;
      padding: 14px;
      margin-bottom: 10px;
      font-size: 14px;
      color: #92400e;
      line-height: 1.5;
    }

    .recommendation-item::before {
      content: "💡 ";
    }

    footer {
      margin-top: 40px;
      text-align: center;
      font-size: 12px;
      color: #aaa;
    }

    @media (max-width: 768px) {
      .header h1 { font-size: 24px; }
      .stats-grid { grid-template-columns: 1fr 1fr; }
      .energy-table { font-size: 12px; }
    }

    @media (max-width: 480px) {
      .stats-grid { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

  <div class="container">

    <div class="header">
      <h1>Energy <span>Report</span></h1>
      <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Energy</h3>
        <div class="value"><?= number_format($total_energy, 1) ?> kWh</div>
      </div>
      <div class="stat-card">
        <h3>Total Hours</h3>
        <div class="value"><?= number_format($total_hours, 1) ?> hrs</div>
      </div>
      <div class="stat-card">
        <h3>Devices Tracked</h3>
        <div class="value"><?= count($devices) ?></div>
      </div>
    </div>

    <div class="section">
      <h2>Device Energy Breakdown</h2>
      <?php if (count($devices) === 0): ?>
        <p style="color: #999; text-align: center; padding: 40px;">No devices to track yet.</p>
      <?php else: ?>
        <table class="energy-table">
          <thead>
            <tr>
              <th>Device</th>
              <th>Room</th>
              <th>Status</th>
              <th>Hours Used</th>
              <th>Energy (kWh)</th>
              <th>Usage</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $max_energy = max(array_column($devices, "energy_usage")) ?: 1;
            foreach ($devices as $device): 
              $percentage = ($device["energy_usage"] / $max_energy) * 100;
            ?>
              <tr>
                <td><strong><?= htmlspecialchars($device["device_name"]) ?></strong></td>
                <td><?= htmlspecialchars($device["room_name"] ?: "N/A") ?></td>
                <td><?= $device["state"] ?></td>
                <td><?= number_format($device["hours_used"], 1) ?></td>
                <td><?= number_format($device["energy_usage"], 1) ?></td>
                <td>
                  <div class="energy-bar">
                    <div class="energy-bar-fill" style="width: <?= $percentage ?>%"></div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>

    <?php if (count($recommendations) > 0): ?>
      <div class="section">
        <h2>Efficiency Recommendations</h2>
        <ul class="recommendation-list">
          <?php foreach ($recommendations as $rec): ?>
            <li class="recommendation-item"><?= htmlspecialchars($rec) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <footer>© 2026 DevsEntity · SmartHome Manager</footer>

  </div>

</body>
</html>
