<?php
// submit_entries.php - Updated version without login requirement
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
            continue; // Skip invalid entries
        }
        
        $serviceTag = strtoupper(trim($entry['serviceTag']));
        $techName = $entry['techName'];
        $workDate = $entry['date'] ?: date('Y-m-d');
        $customer = $entry['customer'] ?: null;
        $newStatus = $entry['status'];
        $onHoldStatus = $entry['onHoldStatus'] ?: null;
        $jobNumber = $entry['jobNumber'] ?: null;
        $hoursWorked = $entry['dailyHours'] ?: null;
        
        // Check if unit already exists
        $checkStmt = $conn->prepare("SELECT service_tag, current_status FROM units WHERE service_tag = ?");
        $checkStmt->execute([$serviceTag]);
        $existingUnit = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existingUnit) {
            // UNIT EXISTS - Update it
            $oldStatus = $existingUnit['current_status'];
            
            // Update units table with new status
            $updateUnit = $conn->prepare("
                UPDATE units SET
                    current_status = ?,
                    current_on_hold_status = ?,
                    current_tech = ?,
                    customer = ?,
                    job_number = ?,
                    last_updated = NOW()
                WHERE service_tag = ?
            ");
            $updateUnit->execute([
                $newStatus,
                $onHoldStatus,
                $techName,
                $customer,
                $jobNumber,
                $serviceTag
            ]);
            
            // Add to work history
            $addHistory = $conn->prepare("
                INSERT INTO work_history 
                (service_tag, tech_name, work_date, status_before, status_after, on_hold_status, hours_worked)
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
                INSERT INTO units
                (service_tag, current_status, current_on_hold_status, customer, current_tech, job_number)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $insertUnit->execute([
                $serviceTag,
                $newStatus,
                $onHoldStatus,
                $customer,
                $techName,
                $jobNumber
            ]);
            
            // Add to work history
            $addHistory = $conn->prepare("
                INSERT INTO work_history
                (service_tag, tech_name, work_date, status_before, status_after, on_hold_status, hours_worked)
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
