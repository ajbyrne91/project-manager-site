<?php
require_once 'config.php';

#initialise error variable to store any login errors.
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    #query the database for a user matching the username, selecting the user ID & hashed password.
    $stmt = $conn->prepare("SELECT uid, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    #check if excatly one user exists
    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        #verify password, if correct store UID & username in the session
        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['username'] = $username;

            #once logged in redirect user to project page and stop execution.
            header("Location: projects.php");
            exit();

        } else {
            $error = "Incorrect password";
        }

    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

<h2>Login</h2>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>

</form>

<p><a href="register.php">Register</a></p>

</body>
</html>
