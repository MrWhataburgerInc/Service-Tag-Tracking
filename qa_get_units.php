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
$updatedFrom = $_GET['updatedFrom'] ?? null;
$updatedTo = $_GET['updatedTo'] ?? null;
$serviceTag = $_GET['serviceTag'] ?? '';
$jobNumber = $_GET['jobNumber'] ?? '';
$status = $_GET['status'] ?? '';
$onHold = $_GET['onHold'] ?? '';
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
    created_at,
    updated_at as last_updated
FROM unit WHERE 1=1";
$params = [];

// Updated date filters
if ($updatedFrom) {
    $query .= " AND updated_at >= ?";
    $params[] = $updatedFrom . ' 00:00:00';
}

if ($updatedTo) {
    $query .= " AND updated_at <= ?";
    $params[] = $updatedTo . ' 23:59:59';
}

// Service tag search (partial match)
if ($serviceTag) {
    $query .= " AND service_tag LIKE ?";
    $params[] = '%' . strtoupper($serviceTag) . '%';
}

// Job number search (exact or partial match)
if ($jobNumber) {
    $query .= " AND job_number LIKE ?";
    $params[] = '%' . $jobNumber . '%';
}

if ($status) {
    $query .= " AND current_status = ?";
    $params[] = $status;
}

if ($onHold) {
    $query .= " AND on_hold_status = ?";
    $params[] = $onHold;
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

// Count units with specific on_hold_status values
$needsImagingQuery = "SELECT COUNT(*) as count FROM unit WHERE on_hold_status = 'AWAITING REIMAGING'";
$needsImaging = $conn->query($needsImagingQuery)->fetch(PDO::FETCH_ASSOC)['count'];

$dellDepotQuery = "SELECT COUNT(*) as count FROM unit WHERE on_hold_status = 'AWAITING DELL DEPOT'";
$dellDepot = $conn->query($dellDepotQuery)->fetch(PDO::FETCH_ASSOC)['count'];

$stats = [
    'diagnosed' => 0,
    'repaired' => 0,
    'partial' => 0,
    'completed' => 0,
    'needs_imaging' => (int)$needsImaging,
    'dell_depot' => (int)$dellDepot
];

foreach ($statsRaw as $stat) {
    $status = strtolower(str_replace(' ', '_', $stat['current_status']));
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