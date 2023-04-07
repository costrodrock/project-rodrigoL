<!DOCTYPE php>
<?php
	session_start();

	// Establish a database connection
	$host = "localhost";
	$database = "WebProject";
	$user = "webuser";
	$password = "P@ssw0rd";

	$connection = mysqli_connect($host, $user, $password, $database);

	$error = mysqli_connect_error();
	if ($error != null) {
		$output = "<p>Unable to connect to database!</p>";
		exit($output);
	}

	// Get the comment ID from a form or URL parameter
	$commentID = $_POST['commentID'];
	$content = $_POST['content'];

	// Update the row in the 'comments' table
	$sql = "UPDATE comments SET content='$content' WHERE commentID=$commentID";
	if ($connection->query($sql) === TRUE) {
        $referer = $_SERVER['HTTP_REFERER'];
		header("Refresh: 0; URL=$referer");
		exit();
	} else {
		echo "Error updating comment: " . $connection->error;
	}

	// Close the connection
	$connection->close();
?>