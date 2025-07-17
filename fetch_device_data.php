<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    $device_id = isset($_GET['device_id']) ? $conn->real_escape_string($_GET['device_id']) : null;
    if (!$device_id) throw new Exception("Device ID is required");

    // Step 1: Find latest timestamp
    $latestQuery = "SELECT MAX(timestamp) as latest_time FROM device_data WHERE device_id = ?";
    $stmt = $conn->prepare($latestQuery);
    $stmt->bind_param('s', $device_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $latest_time = $result->fetch_assoc()['latest_time'];

    if (!$latest_time) throw new Exception("No data available");

    // Step 2: Get last 24 hours from that time
    $query = "SELECT * FROM device_data 
              WHERE device_id = ? 
              AND timestamp BETWEEN DATE_SUB(?, INTERVAL 23 HOUR) AND ? 
              ORDER BY timestamp ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $device_id, $latest_time, $latest_time);
    $stmt->execute();
    $result = $stmt->get_result();

    $response = ['success' => true, 'data' => ['device_data' => []]];
    while ($row = $result->fetch_assoc()) {
        $response['data']['device_data'][] = $row;
    }

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
