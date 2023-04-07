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

	// Get the post ID from a form or URL parameter
	$postID = $_GET['postID'];

	// Get the post content from the database
	$sql = "SELECT content FROM posts WHERE postID = $postID";
	$result = mysqli_query($connection, $sql);

	if (mysqli_num_rows($result) > 0) {
	  $row = mysqli_fetch_assoc($result);
	  $content = $row['content'];
	} else {
	  // Handle error if post ID is not found
	  echo "Post not found";
	  exit();
	}

	// Update the row in the 'posts' table
	if (isset($_POST['content'])) {
	  $newContent = $_POST['content'];
	  $sql = "UPDATE posts SET content='$newContent' WHERE postID=$postID";

	  if ($connection->query($sql) === TRUE) {
		$referer = $_SERVER['HTTP_REFERER'];
		header("Refresh: 0; URL=$referer");
		exit();
	  } else {
		echo "Error updating post: " . $connection->error;
	  }
	}

	// Close the connection
	$connection->close();
?>