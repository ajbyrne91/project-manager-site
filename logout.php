<?php
require_once 'config.php';

#destroy the session
session_destroy();

#take the user back to the index page
header("Location: index.php");
exit();
