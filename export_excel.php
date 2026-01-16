<?php
// export_excel.php - Export filtered units data to Excel (CSV format for better compatibility)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Units_' . date('Y-m-d_His') . '.csv"');

// Database connection
$host = 'localhost';
$dbname = 'tech_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die('Database connection failed');
}

// Get filter parameters from query string
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
    customer_name,
    current_status,
    on_hold_status,
    assigned_technician,
    job_number,
    created_at,
    updated_at
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

// Service tag search
if ($serviceTag) {
    $query .= " AND service_tag LIKE ?";
    $params[] = '%' . strtoupper($serviceTag) . '%';
}

// Job number search
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

// Create output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 (helps Excel recognize encoding)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Add header row
fputcsv($output, [
    'Service Tag',
    'Customer',
    'Status',
    'On Hold Status',
    'Assigned Tech',
    'Job Number',
    'Created Date',
    'Last Updated'
]);

// Add data rows
foreach ($units as $unit) {
    fputcsv($output, [
        $unit['service_tag'],
        $unit['customer_name'] ?? '',
        $unit['current_status'],
        $unit['on_hold_status'] ?? '',
        $unit['assigned_technician'] ?? '',
        $unit['job_number'] ?? '',
        date('m/d/Y H:i', strtotime($unit['created_at'])),
        date('m/d/Y H:i', strtotime($unit['updated_at']))
    ]);
}

fclose($output);
?>