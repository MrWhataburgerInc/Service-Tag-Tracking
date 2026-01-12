<?php
// test_connection.php - Diagnostic tool to test your database connection
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>XAMPP Database Connection Test</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; }
        .test { margin: 15px 0; padding: 15px; border-left: 4px solid #ccc; background: #f9f9f9; }
        .success { border-left-color: #28a745; background: #d4edda; color: #155724; }
        .error { border-left-color: #dc3545; background: #f8d7da; color: #721c24; }
        .warning { border-left-color: #ffc107; background: #fff3cd; color: #856404; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Connection Diagnostic</h1>
        
        <?php
        // Test 1: Check if PDO extension is loaded
        echo "<div class='test " . (extension_loaded('pdo') ? 'success' : 'error') . "'>";
        echo "<strong>Test 1: PDO Extension</strong><br>";
        if (extension_loaded('pdo')) {
            echo "✅ PDO is loaded";
        } else {
            echo "❌ PDO is NOT loaded. Enable it in php.ini";
        }
        echo "</div>";
        
        // Test 2: Check MySQL PDO driver
        echo "<div class='test " . (extension_loaded('pdo_mysql') ? 'success' : 'error') . "'>";
        echo "<strong>Test 2: PDO MySQL Driver</strong><br>";
        if (extension_loaded('pdo_mysql')) {
            echo "✅ PDO MySQL driver is loaded";
        } else {
            echo "❌ PDO MySQL driver is NOT loaded. Enable it in php.ini";
        }
        echo "</div>";
        
        // Test 3: Connect to MySQL
        echo "<div class='test'>";
        echo "<strong>Test 3: Database Connection</strong><br>";
        
        $host = 'localhost';
        $dbname = 'tech_db';
        $username = 'root';
        $password = '';
        
        echo "Attempting to connect to:<br>";
        echo "<code>Host: $host</code><br>";
        echo "<code>Database: $dbname</code><br>";
        echo "<code>User: $username</code><br><br>";
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; color: #155724;'>";
            echo "✅ <strong>SUCCESS!</strong> Connected to database 'tech_db'";
            echo "</div>";
            
            // Test 4: Check tables
            echo "<br><strong>Test 4: Database Tables</strong><br>";
            $tables = ['technician', 'unit', 'work_history'];
            
            foreach ($tables as $table) {
                $stmt = $conn->query("SELECT COUNT(*) as count FROM $table");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ <code>$table</code> exists with " . $result['count'] . " records<br>";
            }
            
            // Test 5: Check columns in unit table
            echo "<br><strong>Test 5: Unit Table Structure</strong><br>";
            $columns = $conn->query("DESCRIBE unit")->fetchAll(PDO::FETCH_ASSOC);
            echo "Columns found:<br>";
            foreach ($columns as $col) {
                echo "  • <code>" . $col['Field'] . "</code> (" . $col['Type'] . ")<br>";
            }
            
        } catch(PDOException $e) {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; color: #721c24;'>";
            echo "❌ <strong>CONNECTION FAILED</strong><br>";
            echo "Error: " . $e->getMessage();
            echo "</div>";
            
            echo "<br><strong>Troubleshooting:</strong><br>";
            echo "1. Make sure MySQL is running (XAMPP Control Panel)<br>";
            echo "2. Make sure database 'tech_db' exists<br>";
            echo "3. Try connecting with phpMyAdmin first<br>";
            echo "4. Check if port 3306 is correct<br>";
        }
        ?>
        
        <div class="test warning">
            <strong>💡 Next Steps:</strong><br>
            1. If all tests pass, your connection is working<br>
            2. Check your <code>submit_entries.php</code> file for typos<br>
            3. Test with a simple INSERT query<br>
            4. Check browser console (F12) for JavaScript errors
        </div>
    </div>
</body>
</html>