<?php
// submit_entries.php - With detailed error logging
header('Content-Type: application/json');

// Log file for debugging
$logFile = __DIR__ . '/submit_log.txt';

function logError($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

logError("=== NEW REQUEST ===");
logError("Method: " . $_SERVER['REQUEST_METHOD']);
logError("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));

// Database connection
$host = 'localhost';
$dbname = 'tech_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    logError("✅ Database connected successfully");
} catch(PDOException $e) {
    logError("❌ Database connection failed: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get JSON data from request
$rawInput = file_get_contents('php://input');
logError("Raw input received: " . strlen($rawInput) . " bytes");

$entries = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    $error = json_last_error_msg();
    logError("❌ JSON decode error: " . $error);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON: ' . $error]);
    exit;
}

logError("Entries parsed: " . count($entries ?? []) . " items");

if (empty($entries) || !is_array($entries)) {
    logError("❌ No entries provided or not an array");
    echo json_encode(['success' => false, 'message' => 'No entries provided']);
    exit;
}

// Begin transaction
$conn->beginTransaction();
logError("Transaction started");

try {
    $successCount = 0;
    $updatedCount = 0;
    $newCount = 0;
    
    foreach ($entries as $index => $entry) {
        logError("Processing entry $index");
        
        // Validate required fields
        if (empty($entry['techName']) || empty($entry['serviceTag']) || empty($entry['status'])) {
            logError("  Skipped - missing required fields");
            continue;
        }
        
        $serviceTag = strtoupper(trim($entry['serviceTag']));
        $techName = $entry['techName'];
        $workDate = $entry['date'] ?: date('Y-m-d');
        $customerName = $entry['customer'] ?: null;
        $newStatus = $entry['status'];
        $onHoldStatus = $entry['onHoldStatus'] ?: null;
        $jobNumber = $entry['jobNumber'] ?: null;
        
        logError("  Tag: $serviceTag, Tech: $techName, Status: $newStatus");
        
        // Check if unit already exists
        $checkStmt = $conn->prepare("SELECT service_tag, current_status FROM unit WHERE service_tag = ? AND job_number = ?");
        $checkStmt->execute([$serviceTag, $jobNumber]);
        $existingUnit = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUnit) {
            logError("  Unit EXISTS - updating");
            $oldStatus = $existingUnit['current_status'];
            
            // Update unit table with new status
            $updateUnit = $conn->prepare("
                UPDATE unit SET
                    current_status = ?,
                    on_hold_status = ?,
                    assigned_technician = ?,
                    customer_name = ?,
                    updated_at = NOW()
                WHERE service_tag = ? AND job_number = ?
            ");
            $updateUnit->execute([
                $newStatus,
                $onHoldStatus,
                $techName,
                $customerName,
                $serviceTag,
                $jobNumber
            ]);
            
            // Add to work history
            $addHistory = $conn->prepare("
                INSERT INTO work_history 
                (service_tag, job_number, technician_name, work_date, status_before, status_after, on_hold_status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $addHistory->execute([
                $serviceTag,
                $jobNumber,
                $techName,
                $workDate,
                $oldStatus,
                $newStatus,
                $onHoldStatus
            ]);
            
            logError("  ✅ Unit updated and history added");
            $updatedCount++;
            
        } else {
            logError("  NEW UNIT - creating");
            
            // NEW UNIT - Create it
            $insertUnit = $conn->prepare("
                INSERT INTO unit
                (service_tag, job_number, current_status, on_hold_status, customer_name, assigned_technician)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insertUnit->execute([
                $serviceTag,
                $jobNumber,
                $newStatus,
                $onHoldStatus,
                $customerName,
                $techName
            ]);
            
            // Add to work history
            $addHistory = $conn->prepare("
                INSERT INTO work_history
                (service_tag, job_number, technician_name, work_date, status_before, status_after, on_hold_status)
                VALUES (?, ?, ?, ?, NULL, ?, ?, ?)
            ");
            $addHistory->execute([
                $serviceTag,
                $jobNumber,
                $techName,
                $workDate,
                $newStatus,
                $onHoldStatus
            ]);
            
            logError("  ✅ New unit created and history added");
            $newCount++;
        }
        
        $successCount++;
    }
    
    // Commit transaction
    $conn->commit();
    logError("✅ Transaction committed successfully");
    
    echo json_encode([
        'success' => true,
        'message' => "Successfully processed $successCount entries ($newCount new, $updatedCount updated)",
        'count' => $successCount,
        'new' => $newCount,
        'updated' => $updatedCount
    ]);
    
} catch(Exception $e) {
    // Rollback on error
    $conn->rollBack();
    $errorMsg = $e->getMessage();
    logError("❌ Error during transaction: " . $errorMsg);
    logError("Error trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error saving entries: ' . $errorMsg
    ]);
}
?>