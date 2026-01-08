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
$query = "SELECT * FROM units WHERE 1=1";
$params = [];

if ($dateFrom) {
    $query .= " AND last_updated >= ?";
    $params[] = $dateFrom . ' 00:00:00';
}

if ($dateTo) {
    $query .= " AND last_updated <= ?";
    $params[] = $dateTo . ' 23:59:59';
}

if ($status) {
    $query .= " AND current_status = ?";
    $params[] = $status;
}

if ($tech) {
    $query .= " AND current_tech = ?";
    $params[] = $tech;
}

if ($customer) {
    $query .= " AND customer = ?";
    $params[] = $customer;
}

$query .= " ORDER BY last_updated DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$units = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get stats
$statsQuery = "
    SELECT 
        current_status,
        COUNT(*) as count
    FROM units
    GROUP BY current_status
";
$statsStmt = $conn->query($statsQuery);
$statsRaw = $statsStmt->fetchAll(PDO::FETCH_ASSOC);

$stats = [
    'diagnosed' => 0,
    'repaired' => 0,
    'partial' => 0,
    'completed' => 0
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
