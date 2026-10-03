<?php
require_once 'config.php';

#redirect if not logged in
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

#deleting changes data, so require a CSRF-protected form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Use the project delete form.');
}

#check if project ID exists
if (!isset($_POST['id'])) {
    header("Location: projects.php");
    exit();
}

$pid = filter_var($_POST['id'], FILTER_VALIDATE_INT);
if ($pid === false || $pid < 1) {
    http_response_code(400);
    exit('Invalid project ID.');
}
$uid = $_SESSION['uid'];

#delete only allowed if project belongs to a logged in user
$stmt = $conn->prepare("DELETE FROM projects WHERE pid = ? AND uid = ?");
$stmt->bind_param("ii", $pid, $uid);

$stmt->execute();

#redirect back to projects page
header("Location: projects.php");
exit();
?>
