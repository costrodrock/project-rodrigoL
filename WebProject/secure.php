<!DOCTYPE html>
<html>
	<?php
		session_start();
		// Check if the user is already logged in
		if (!isset($_SESSION["username"])) {
			header("Location: login.php");
			exit();
		}
	?>
<head>
    <title>Secure Page</title>
</head>
<body>
    <h1>This page is so secure omg</h1>
    <p>This content is only visible to logged in users.</p>
    <p>Placeholder text goes here.</p>
    <a href="logout.php">Logout</a>
</body>
</html>