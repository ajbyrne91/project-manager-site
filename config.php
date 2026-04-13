<?php
#initialise a new session or resume an existing one
session_start();

#database configuration variables
$host = "localhost";
$user = "root";
$password = "";
$dbname = "project_manager_db";

#create connection & assign it to $conn variable
$conn = new mysqli($host, $user, $password, $dbname);

#check if connection failed
if ($conn->connect_error) {
    die("Connection to database failed.");
}
?>