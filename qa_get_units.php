<?php
// qa_get_units.php - Get units with filters for QA dashboard
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

// Get filter parameters
$dateFrom = $_GET['dateFrom'] ?? null;
$dateTo = $_GET['dateTo'] ?? null;
$status = $_GET['status'] ?? '';
$tech = $_GET['tech'] ?? '';
$customer = $_GET['customer'] ?? '';

// Build query with filters
$query = "SELECT 
    service_tag,
    customer_name as customer,
    current_status,
    on_hold_status as current_on_hold_status,
    assigned_technician as current_tech,
    job_number,
    updated_at as last_updated
FROM unit WHERE 1=1";
$params = [];

if ($dateFrom) {
    $query .= " AND updated_at >= ?";
    $params[] = $dateFrom . ' 00:00:00';
}

if ($dateTo) {
    $query .= " AND updated_at <= ?";
    $params[] = $dateTo . ' 23:59:59';
}

if ($status) {
    $query .= " AND current_status = ?";
    $params[] = $status;
}

if ($tech) {
    $query .= " AND assigned_technician = ?";
    $params[] = $tech;
}

if ($customer) {
    $query .= " AND customer_name = ?";
    $params[] = $customer;
}

$query .= " ORDER BY updated_at DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$units = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$statsQuery = "
    SELECT 
        current_status,
        COUNT(*) as count
    FROM unit
    GROUP BY current_status
";
$statsStmt = $conn->query($statsQuery);
$statsRaw = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

$stats = [
    'diagnosed' => 0,
    'repaired' => 0,
    'partial' => 0,
    'completed' => 0,
    'ber' => 0
];

foreach ($statsRaw as $stat) {
    $status = strtolower($stat['current_status']);
    if (isset($stats[$status])) {
        $stats[$status] = (int)$stat['count'];
    }
}

echo json_encode([
    'success' => true,
    'units' => $units,
    'stats' => $stats,
    'count' => count($units)
]);
?>