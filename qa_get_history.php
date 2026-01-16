<?php
// qa_get_history.php - Get complete work history for a specific unit
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'tech_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get service tag from request
$serviceTag = strtoupper(trim($_GET['service_tag'] ?? ''));

if (empty($serviceTag)) {
    echo json_encode(['success' => false, 'message' => 'Service tag required']);
    exit;
}

// Get work history for this unit
$stmt = $conn->prepare("
    SELECT 
        technician_name,
        work_date,
        status_before,
        status_after,
        on_hold_status,
        notes,
        submitted_at
    FROM work_history
    WHERE service_tag = ?
    ORDER BY submitted_at DESC
");

$stmt->execute([$serviceTag]);
$history = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'history' => $history,
    'count' => count($history)
]);
?>