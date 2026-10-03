<?php
#initialise a new session or resume an existing one
session_start();

#protect form submissions against cross-site requests
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    (!isset($_POST['csrf_token']) || !is_string($_POST['csrf_token']) ||
     !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']))) {
    http_response_code(403);
    exit('Invalid form submission. Reload the page and try again.');
}

#database configuration variables
$host = getenv("DB_HOST") ?: "localhost";
$user = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASSWORD") ?: "";
$dbname = getenv("DB_NAME") ?: "project_manager_db";
$port = (int) (getenv("DB_PORT") ?: 3306);

#create connection & assign it to $conn variable
$conn = new mysqli($host, $user, $password, $dbname, $port);

#check if connection failed
if ($conn->connect_error) {
    die("Connection to database failed.");
}
?>
