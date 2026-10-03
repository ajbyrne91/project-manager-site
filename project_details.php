<?php
require_once 'config.php';

#check if ID exists
if (!isset($_GET['id'])) {
    echo "No project selected.";
    exit();
}

#project ID is stored in a variable.
$pid = $_GET['id'];

#Prepared statement to get the project and owner username without exposing the account email.
$stmt = $conn->prepare("
    SELECT projects.*, users.username
    FROM projects
    JOIN users ON projects.uid = users.uid
    WHERE projects.pid = ?
");

#execute the query and check that exactly one result is returned. If no matching project is found exit page with error message.
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Project not found.";
    exit();
}

#Get project data as an associative array to be displayed below in HTML section.
$project = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Project Details</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- nav menu -->
<p>
    <a href="projects.php">Browse Projects</a> |
    <a href="index.php">Home</a> |


    <?php if (isset($_SESSION['uid'])): ?>
        <a href="add_project.php">Add Project</a> |
        <a href="logout.php">Logout</a> |
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</p>

<hr>

<h2><?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h2>

<p><strong>Start Date:</strong> <?php echo htmlspecialchars($project['start_date']); ?></p>
<p><strong>End Date:</strong> <?php echo htmlspecialchars($project['end_date']); ?></p>
<p><strong>Phase:</strong> <?php echo htmlspecialchars($project['phase']); ?></p>

<p><strong>Description:</strong></p>
<p><?php echo htmlspecialchars($project['description']); ?></p>

<p><strong>Owner:</strong> <?php echo htmlspecialchars($project['username']); ?></p>

<br>

<a href="index.php">← Back to All Projects</a>

</body>
</html>
