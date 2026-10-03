<?php
require_once 'config.php';

#initialise search variable & check if a search term has been provided via the URL using the $_GET superglobal.
$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

#if search term exists, prepare SQL query that joins the projects & users table.
if (!empty($search)) {
    $stmt = $conn->prepare("SELECT p.*, u.username FROM projects p
                            JOIN users u ON p.uid = u.uid
                            WHERE p.title LIKE ? OR p.start_date = ?
                            ORDER BY p.start_date DESC");
    #allow for partial mathcing on the title.
    $search_term = "%$search%";
    $stmt->bind_param("ss", $search_term, $search);
} else {
    #default query if no search then return everything.
    $stmt = $conn->prepare("SELECT p.*, u.username FROM projects p
                            JOIN users u ON p.uid = u.uid
                            ORDER BY p.start_date DESC");
}

$stmt->execute();
$result = $stmt->get_result();
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

    <?php if (isset($_SESSION['uid'])): ?>
        <a href="add_project.php">Add Project</a> |
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</p>

<hr>

<!-- search form-->
<div class="container">
    <h2>Browse & Search Projects</h2>

    <form method="GET" class="search-form">
        <input type="text" name="search" placeholder="Search by title or date (YYYY-MM-DD)"
               value="<?php echo htmlspecialchars($search); ?>" class="search-input">
        <button type="submit" class="btn">Search</button>

        <!--show clear only when searching-->
        <?php if ($search): ?>
            <a href="projects.php">Clear</a>
        <?php endif; ?>
    </form>

    <hr>
    <!--check there are results before looping-->
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <!--display project info-->
            <div class="project-card">
                <h3><?php echo htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><strong>Start Date:</strong> <?php echo htmlspecialchars($row['start_date']); ?></p>
                <p><strong>Phase:</strong> <?php echo $row['phase']; ?></p>
                <p><strong>Owner:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
                <!--limit description lenght and keep the UI clean-->
                <p><?php echo htmlspecialchars(substr($row['description'], 0, 150)); ?>...</p>

                <a href="project_details.php?id=<?php echo $row['pid']; ?>">View Details</a>

                <!--logged in user can edit and/or delete if they own project-->
              <?php if (isset($_SESSION['uid']) && $_SESSION['uid'] == $row['uid']): ?>
                    | <a href="edit_project.php?id=<?php echo $row['pid']; ?>">Edit</a>
                    | <form method="POST" action="delete_project.php" style="display: inline;"
                            onsubmit="return confirm('Are you sure you want to delete this project?');">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                        <input type="hidden" name="id" value="<?php echo (int) $row['pid']; ?>">
                        <button type="submit">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <!--display message and option to clear search-->
        <p>No projects found.</p>
        <?php if ($search): ?>
            <p><a href="projects.php">Clear Search & View All Projects</a></p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
