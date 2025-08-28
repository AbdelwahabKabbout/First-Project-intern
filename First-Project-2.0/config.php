<?php
$env = parse_ini_file('.env');
$host = $env['DB_HOST'];
$dbname = $env['DB_NAME'];
$user = $env['DB_USER'];
$pass = $env['DB_PASS'];
$port = $env['DB_PORT'];

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    //echo "Connected successfully ";
    echo "<br><br>";
}
