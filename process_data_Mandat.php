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
    echo "Database connection failed.";
    exit;
}

// Receive data from AJAX request
$raw_data = file_get_contents('php://input');
$data = json_decode($raw_data, true);

// Check if data is correctly formatted
if (!$data || !isset($data['headers']) || !isset($data['rows'])) {
    http_response_code(400);
    echo "Invalid data format.";
    oci_close($conn);
    exit;
}

// Hardcode the target table name for testing purposes
$targetTable = 'Mandat'; // Replace with the actual target table name

// Debugging: Log information about received data
error_log("Received data headers: " . print_r($data['headers'], true));
error_log("Received data rows: " . print_r($data['rows'], true));

// Prepare and execute the SQL statement to insert data into the target table
// Temporary fixed SQL statement for testing purposes
$sql = "INSERT INTO $targetTable (CODE_PORTEF, CODE_ORD, GESTION, CODE_MANDAT, DT_EMISSION, STATUT, PROGRAMME, SOUS_PROGRAMME, ACTION, SOUS_ACTION, AXE_ECO, DISPOS, MONTANT) VALUES (:CODE_PORTEF, :CODE_ORD, :GESTION, :CODE_MANDAT, :DT_EMISSION, :STATUT, :PROGRAMME, :SOUS_PROGRAMME, :ACTION, :SOUS_ACTION, :AXE_ECO, :DISPOS, :MONTANT)";
$stid = oci_parse($conn, $sql);
if (!$stid) {
    http_response_code(500);
    echo "Failed to prepare SQL statement.";
    oci_close($conn);
    exit;
}

foreach ($data['rows'] as $row) {
    oci_bind_by_name($stid, ':CODE_PORTEF', $row[0]);
    oci_bind_by_name($stid, ':CODE_ORD', $row[1]);
    oci_bind_by_name($stid, ':GESTION', $row[2]);
    oci_bind_by_name($stid, ':CODE_MANDAT', $row[3]);
    oci_bind_by_name($stid, ':DT_EMISSION', $row[4]);
    oci_bind_by_name($stid, ':STATUT', $row[5]);
    oci_bind_by_name($stid, ':PROGRAMME', $row[6]);
    oci_bind_by_name($stid, ':SOUS_PROGRAMME', $row[7]);
    oci_bind_by_name($stid, ':ACTION', $row[8]);
    oci_bind_by_name($stid, ':SOUS_ACTION', $row[9]);
    oci_bind_by_name($stid, ':AXE_ECO', $row[10]);
    oci_bind_by_name($stid, ':DISPOS', $row[11]);
    oci_bind_by_name($stid, ':MONTANT', $row[12]);
    
    $result = oci_execute($stid, OCI_DEFAULT);
    if (!$result) {
        http_response_code(500);
        echo "Failed to insert data.";
        oci_free_statement($stid);
        oci_close($conn);
        exit;
    }
}

// Commit the transaction
$commit = oci_commit($conn);
if (!$commit) {
    http_response_code(500);
    echo "Failed to commit transaction.";
    oci_close($conn);
    exit;
}

oci_free_statement($stid);
oci_close($conn);

http_response_code(200);
echo "Data inserted successfully into $targetTable table.";
?>
