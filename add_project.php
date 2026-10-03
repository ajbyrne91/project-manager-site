<?php
require_once 'config.php';

#check if user is logged in otherwise redirect. only logged in users can createm projects.
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}

$error = "";

#submit form using POST method. Retrieve and trrim() the input data where necessary. Automatically associate project with logged in user by using session user ID.
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = trim($_POST['description']);
    $phase = $_POST['phase'];
    $uid = $_SESSION['uid'];

    $sql = "INSERT INTO projects (title, start_date, end_date, description, phase, uid)
            VALUES (?, ?, ?, ?, ?, ?)";

#prepare statement to insert the project data into database.

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $title, $start_date, $end_date, $description, $phase, $uid);

    #if insert is successful redirect to projects page. If insert fails then dispaly error message.
    if ($stmt->execute()) {
        header("Location: projects.php");
        exit();
    } else {
        $error = "Error adding project. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- nav menu -->
<p>
    <a href="index.php">Home</a> |
    <a href="projects.php">Browse Projects</a> |

    <?php if (isset($_SESSION['uid'])): ?>
        <a href="add_project.php">Add Project</a> |
        <a href="logout.php">Logout</a> |
    <?php else: ?>
        <a href="login.php">Login</a> |
        <a href="register.php">Register</a>
    <?php endif; ?>
</p>

<hr>

<h2>Add New Project</h2>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<!--Form includes fields for title, start & end date, description, and a dropdown selection for phase of project-->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

    <label>Title</label><br>
    <input type="text" name="title" required><br><br>

    <label>Start Date</label><br>
    <input type="date" name="start_date" required><br><br>

    <label>End Date</label><br>
    <input type="date" name="end_date" required><br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea><br><br>

    <label>Phase</label><br>

    <select name="phase" required>
        <option value="design">Design</option>
        <option value="development">Development</option>
        <option value="testing">Testing</option>
        <option value="deployment">Deployment</option>
        <option value="complete">Complete</option>
    </select><br><br>

    <button type="submit">Add Project</button>

</form>

<p><a href="projects.php">← Back to Projects</a></p>

</body>
</html>
