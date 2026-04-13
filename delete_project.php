<?php
require_once 'config.php';

#redirect if not logged in
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

#check if project ID exists
if (!isset($_GET['id'])) {
    header("Location: projects.php");
    exit();
}

$pid = $_GET['id'];
$uid = $_SESSION['uid'];

#delete only allowed if project belongs to a logged in user
$stmt = $conn->prepare("DELETE FROM projects WHERE pid = ? AND uid = ?");
$stmt->bind_param("ii", $pid, $uid);

$stmt->execute();

#redirect back to projects page
header("Location: projects.php");
exit();
?>