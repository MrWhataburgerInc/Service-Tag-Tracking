<?php
// submit_entries.php - Fixed version
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'tech_db';  // Match your other PHP files
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get JSON data from request
$entries = json_decode(file_get_contents('php://input'), true);

if (empty($entries) || !is_array($entries)) {
    echo json_encode(['success' => false, 'message' => 'No entries provided']);
    exit;
}

// Begin transaction
$conn->beginTransaction();

try {
    $successCount = 0;
    $updatedCount = 0;
    $newCount = 0;
    
    foreach ($entries as $entry) {
        // Validate required fields
        if (empty($entry['techName']) || empty($entry['serviceTag']) || empty($entry['status'])) {
            continue;
        }
        
        $serviceTag = strtoupper(trim($entry['serviceTag']));
        $techName = $entry['techName'];
        $workDate = $entry['date'] ?: date('Y-m-d');
        $customerName = $entry['customer'] ?: null;
        $newStatus = $entry['status'];
        $onHoldStatus = $entry['onHoldStatus'] ?: null;
        $jobNumber = $entry['jobNumber'] ?: null;
        $hoursWorked = $entry['dailyHours'] ?: null;
        
        // Check if unit already exists
        $checkStmt = $conn->prepare("SELECT service_tag, current_status FROM unit WHERE service_tag = ? AND job_number = ?");
        $checkStmt->execute([$serviceTag, $jobNumber]);
        $existingUnit = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUnit) {
            // UNIT EXISTS - Update it
            $oldStatus = $existingUnit['current_status'];
            
            // Update unit table with new status
            $updateUnit = $conn->prepare("
                UPDATE unit SET
                    current_status = ?,
                    on_hold_status = ?,
                    assigned_technician = ?,
                    customer_name = ?,
                    job_number = ?,
                    updated_at = NOW()
                WHERE service_tag = ?
            ");
            $updateUnit->execute([
                $newStatus,
                $onHoldStatus,
                $techName,
                $customerName,
                $jobNumber,
                $serviceTag
            ]);
            
            // Add to work history
            $addHistory = $conn->prepare("
                INSERT INTO work_history 
                (service_tag, technician_name, work_date, status_before, status_after, on_hold_status, hours_worked)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $addHistory->execute([
                $serviceTag,
                $techName,
                $workDate,
                $oldStatus,
                $newStatus,
                $onHoldStatus,
                $hoursWorked
            ]);
            
            $updatedCount++;
            
        } else {
            // NEW UNIT - Create it
            $insertUnit = $conn->prepare("
                INSERT INTO unit
                (service_tag, current_status, on_hold_status, customer_name, assigned_technician, job_number)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insertUnit->execute([
                $serviceTag,
                $newStatus,
                $onHoldStatus,
                $customerName,
                $techName,
                $jobNumber
            ]);
            
            // Add to work history
            $addHistory = $conn->prepare("
                INSERT INTO work_history
                (service_tag, technician_name, work_date, status_before, status_after, on_hold_status, hours_worked)
                VALUES (?, ?, ?, NULL, ?, ?, ?)
            ");
            $addHistory->execute([
                $serviceTag,
                $techName,
                $workDate,
                $newStatus,
                $onHoldStatus,
                $hoursWorked
            ]);
            
            $newCount++;
        }
        
        $successCount++;
    }
    
    // Commit transaction
    $conn->commit();
    
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
    echo json_encode([
        'success' => false,
        'message' => 'Error saving entries: ' . $e->getMessage()
    ]);
}
?>