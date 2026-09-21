<?php

require './connection.php';

// Test is signed in
require './bumpcheck.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $dbuser, $dbpassword);

    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo '{';


    // Close
    echo '}';

} catch(PDOException $e) {
    echo '{"error": "'. $e->getMessage().'"}';
}

?>