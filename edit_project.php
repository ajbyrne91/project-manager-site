<?php
require_once 'config.php';

#redirect if not logged in
if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit();
}


$uid = $_SESSION['uid'];
$error = "";

#check if project ID exists
if (!isset($_GET['id'])) {
    echo "No project selected.";
    exit();
}

#get correct project
$pid = $_GET['id'];

#prepared statement to retrieve project data. If no matching project is found stop execution. Prevents user editing someone elses project.
$stmt = $conn->prepare("SELECT * FROM projects WHERE pid = ? AND uid = ?");
$stmt->bind_param("ii", $pid, $uid); #both values are integers
$stmt->execute();
$result = $stmt->get_result();

#ensure exactly one project and load data into project variable.
if ($result->num_rows === 1) {
    $project = $result->fetch_assoc();
} else {
    echo "Error loading project.";
    exit();
}

#handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    #get updated values from form.
    $title = trim($_POST['title']);
    $start_date = $_POST['start_date'];
    $description = trim($_POST['description']);
    $end_date = $_POST['end_date'];
    $phase = $_POST['phase'];

    #update only the correct project that is owned by the user.
    $stmt = $conn->prepare("
    UPDATE projects
    SET title = ?, start_date = ?, end_date = ?, description = ?, phase = ?
    WHERE pid = ? AND uid = ?
");
    $stmt->bind_param("sssssii", $title, $start_date, $end_date, $description, $phase, $pid, $uid); #"sssssii" string, string...int,int

    #if successfull redirect to projects.
    if ($stmt->execute()) {
        header("Location: projects.php");
        exit();
    } else {
        $error = "Error updating project.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
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

<h2>Edit Project</h2>

<?php if ($error): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<!--form fields are pre-populated with the existing project data to allow users to see/modify current values easily.-->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

<label>Title</label><br>
<input
    type="text"
    name="title"
    value="<?php echo htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>"
    required
><br><br>

<label>Start Date</label><br>
<input
    type="date"
    name="start_date"
    value="<?php echo htmlspecialchars($project['start_date']); ?>"
    required
><br><br>

<label>End Date</label><br>
<input type="date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>" required>
<br><br>

<label>Phase</label><br>
<select name="phase" required>
    <option value="design" <?php if ($project['phase'] === 'design') echo 'selected'; ?>>Design</option>
    <option value="development" <?php if ($project['phase'] === 'development') echo 'selected'; ?>>Development</option>
    <option value="testing" <?php if ($project['phase'] === 'testing') echo 'selected'; ?>>Testing</option>
    <option value="deployment" <?php if ($project['phase'] === 'deployment') echo 'selected'; ?>>Deployment</option>
    <option value="complete" <?php if ($project['phase'] === 'complete') echo 'selected'; ?>>Complete</option>
</select>
<br><br>

<label>Description</label><br>
<textarea name="description" required><?php echo htmlspecialchars($project['description']); ?></textarea>
<br><br>

<button type="submit">Update Project</button>

</form>

</body>
</html>
