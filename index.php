<?php
require_once 'config.php';

#define SQL query to get all project data and show newest first
$sql = "SELECT title, start_date, description, pid, uid FROM projects ORDER BY start_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Projects</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- nav menu -->
    <p>
    <a href="index.php">Home</a> |
    <a href="projects.php">Browse Projects</a> |
    <!-- check if user is logged in -->
    <?php if (isset($_SESSION['uid'])): ?>
        <!--logged in view-->
        <a href="add_project.php">Add Project</a> |
        <a href="logout.php">Logout</a> |
    <?php else: ?>
        <!--logged out view-->
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</p>

<hr>

<!--main content header-->
<h2>All Projects</h2>

<hr>
    <!--check if query returns anything-->
<?php if ($result->num_rows > 0): ?>
    <!--if query returns results, loop through projects & display data -->
    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="margin-bottom: 20px;">

            <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <!--htmlspecialchars() for securtity (prevents xss attacks) -->
            <p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>

            <p><?php echo htmlspecialchars($row['description']); ?></p>

            <a href="project_details.php?id=<?php echo $row['pid']; ?>">View Details</a>

        <!-- edit link. check if user is logged in and if that user owns the project-->
            <?php if (isset($_SESSION['uid']) && $_SESSION['uid'] == $row['uid']): ?>
                | <a href="edit_project.php?id=<?php echo $row['pid']; ?>">Edit</a>
            <?php endif; ?>

        </div>
        <hr>
    <?php endwhile; ?>
<!-- if no projects to show, end loop and show message -->
<?php else: ?>
    <p>No projects available.</p>
<?php endif; ?>

</body>
</html>
