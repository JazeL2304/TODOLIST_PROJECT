<?php
/**
 * Database Connection Configuration
 * For XAMPP Local Server
 */

function getDatabaseConnection() {
    // KONFIGURASI UNTUK XAMPP LOCALHOST
    $servername = "localhost";       // Jangan diganti!
    $username = "root";               // Default XAMPP
    $password = "";                   // Coba kosongkan dulu (default XAMPP)
    $dbname = "todo_db";              // Nama database
    
    // JIKA MASIH ERROR, COBA PASSWORD INI:
    // $password = "230404";
    // $password = "root";
    // $password = "mysql";
    // $password = "";  // (password kosong)

    // Buat koneksi
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Set charset untuk support emoji dan karakter khusus
    $conn->set_charset("utf8mb4");

    // Cek koneksi
    if ($conn->connect_error) {
        // Log error untuk debugging
        error_log("Database Connection Failed: " . $conn->connect_error);
        
        // Tampilkan pesan error yang user-friendly
        die("
        <div style='font-family: Arial; padding: 20px; background: #fee; border: 2px solid #c33; border-radius: 8px; margin: 20px;'>
            <h2 style='color: #c33; margin: 0 0 10px 0;'>❌ Database Connection Error</h2>
            <p><strong>Error Message:</strong> " . $conn->connect_error . "</p>
            <hr style='border: 1px solid #c33;'>
            <h3>Troubleshooting Steps:</h3>
            <ol>
                <li><strong>Pastikan XAMPP sudah running:</strong>
                    <ul>
                        <li>Buka XAMPP Control Panel</li>
                        <li>Start Apache & MySQL (kedua harus hijau)</li>
                    </ul>
                </li>
                <li><strong>Cek konfigurasi database.php:</strong>
                    <ul>
                        <li>Servername: <code>localhost</code></li>
                        <li>Username: <code>root</code></li>
                        <li>Password: <code>230404</code></li>
                        <li>Database: <code>todo_db</code></li>
                    </ul>
                </li>
                <li><strong>Pastikan database sudah dibuat:</strong>
                    <ul>
                        <li>Buka <a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>
                        <li>Cek apakah database <code>todo_db</code> ada</li>
                        <li>Jika belum, jalankan SQL script setup database</li>
                    </ul>
                </li>
                <li><strong>Cek password MySQL:</strong>
                    <ul>
                        <li>Jika password bukan <code>230404</code>, ubah di file database.php</li>
                        <li>Untuk cek password, buka phpMyAdmin dan coba login</li>
                    </ul>
                </li>
            </ol>
            <p style='margin-top: 15px;'><strong>Need Help?</strong> Copy error message di atas dan hubungi developer.</p>
        </div>
        ");
    }

    return $conn;
}

/**
 * OPTIONAL: Fungsi untuk test koneksi database
 * Uncomment untuk testing
 */
/*
function testDatabaseConnection() {
    try {
        $conn = getDatabaseConnection();
        echo "✅ Database connection successful!<br>";
        echo "Server Info: " . $conn->server_info . "<br>";
        echo "Host Info: " . $conn->host_info . "<br>";
        
        // Test query
        $result = $conn->query("SELECT DATABASE() as db_name");
        $row = $result->fetch_assoc();
        echo "Connected to database: <strong>" . $row['db_name'] . "</strong><br>";
        
        // Cek tabel
        $result = $conn->query("SHOW TABLES");
        echo "<br>Available tables:<br>";
        while($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
        
        $conn->close();
        return true;
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage();
        return false;
    }
}
*/

// CATATAN PENTING:
// 1. File ini untuk XAMPP localhost saja
// 2. Jangan upload ke hosting dengan konfigurasi ini
// 3. Untuk hosting, gunakan kredensial dari hosting provider
// 4. Jangan commit password ke Git repository

?>