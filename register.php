<?php
require_once 'config.php';

#initialise error variable to store any validation or processing errors.
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    #validation. check if any fields are empty. produce error message if anything is missing.
    if (empty($username) || empty($email) || empty($password)) {
    $error = "All fields are required.";
} else {

    #Check if username or email already exists before inserting new user
    $sql = "SELECT uid FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    #If existing users are found block registration & produce error message.
if ($stmt->num_rows > 0) {
    $error = "Username or email already exists.";
} else {

    #hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    #Insert the new user into database
    $sql = "INSERT INTO users (username, password, email) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashed_password, $email);
    $stmt->execute();
    #redirect user to login page
    header("Location: login.php");
    exit();
}
}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
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

<h2>Create Account</h2>

<!--show error message if one exists-->
<?php if ($error): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<!--form sends data via POST. Required for basic browser validation-->
<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

    <label>Username</label><br>
        <input type="text" name="username" required><br><br>

    <label>Email</label><br>
        <input type="email" name="email" required><br><br>

    <label>Password</label><br>
        <input type="password" name="password" required><br><br>

    <button type="submit">Register</button>

</form>

<p><a href="login.php">Already have an account? Login</a></p>

</body>
</html>
