<?php
// Database connection details
$host = 'localhost';
$port = '1521';
$sid = 'orcl';
$username = 'projetMaster';
$password = 'psw';

// Connect to Oracle database
$conn_str = "(DESCRIPTION =
        (ADDRESS_LIST =
            (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))
        )
        (CONNECT_DATA =
            (SERVICE_NAME = $sid)
        )
    )";

$conn = oci_connect($username, $password, $conn_str);
if (!$conn) {
    http_response_code(500);
    echo "<script>showErrorModal('Database connection failed.');</script>";
    exit;
}

// Receive data from AJAX request
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

// Check if data is correctly formatted
if (!$data || !isset($data['headers']) || !isset($data['rows'])) {
    http_response_code(400);
    echo "<script>showErrorModal('Invalid data format.');</script>";
    oci_close($conn);
    exit;
}

// Hardcode the target table name for testing purposes
$targetTable = 'WILAYAS'; // Replace with the actual target table name

// Debugging: Log information about received data
error_log("Received data headers: " . print_r($data['headers'], true));
error_log("Received data rows: " . print_r($data['rows'], true));

// Prepare and execute the SQL statement to insert data into the target table
// Temporary fixed SQL statement for testing purposes
$sql = "INSERT INTO $targetTable (CODE_WILAYA, LIBELLE_WILAYA) VALUES (:CODE_WILAYA, :LIBELLE_WILAYA)";
$stid = oci_parse($conn, $sql);
if (!$stid) {
    http_response_code(500);
    echo "<script>showErrorModal('Failed to prepare SQL statement.');</script>";
    oci_close($conn);
    exit;
}

foreach ($data['rows'] as $row) {
    oci_bind_by_name($stid, ':CODE_WILAYA', $row[0]);
    oci_bind_by_name($stid, ':LIBELLE_WILAYA', $row[1]);
    
    $result = oci_execute($stid, OCI_DEFAULT);
    if (!$result) {
        http_response_code(500);
        echo "<script>showErrorModal('Failed to insert data.');</script>";
        oci_free_statement($stid);
        oci_close($conn);
        exit;
    }
}

// Commit the transaction
$commit = oci_commit($conn);
if (!$commit) {
    http_response_code(500);
    echo "<script>showErrorModal('Failed to commit transaction.');</script>";
    oci_close($conn);
    exit;
}

oci_free_statement($stid);
oci_close($conn);

echo "<script>showErrorModal('Data inserted successfully into $targetTable table.');</script>";
?>