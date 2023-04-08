<!DOCTYPE php>
<?php
	// Start the session
	session_start();

	// Establish a database connection
	$host = "localhost";
	$database = "WebProject";
	$user = "webuser";
	$password = "P@ssw0rd";

	$connection = mysqli_connect($host, $user, $password, $database);

	// Check if the session variable is set
	if(isset($_SESSION['username']) && $_SERVER["REQUEST_METHOD"] == "POST") {
		// Get the current user's ID from URL
		$userID = $_GET['userID'];

		// Check if the userID exists in the users table
		$query = "SELECT COUNT(*) as count FROM users WHERE userID = '$userID'";
		$result = $connection->query($query);
		$row = $result->fetch_assoc();

		if ($row['count'] == 0) {
			echo "Error: User does not exist.";
		} else {
			// Get theme data
			$title = $_POST['title'];
			$description = $_POST['description'];

			// Insert the theme into the database
			$query = "INSERT INTO themes (title, description) VALUES ('$title', '$description')";
			if ($connection->query($query)) {
				// Get the ID of the newly created theme
				$themeID = mysqli_insert_id($connection);

				// Close the database connection
				$connection->close();

				// Redirect to the newly created theme
				header("Location: themes.php?userID=$userID&themeID=$themeID");
				exit();
			} else {
				echo "Error: " . $query . "<br>" . mysqli_error($connection);
			}
		}
	} else {
		echo "You are not logged in.";
	}
		// Close the database connection
		$connection->close();
?>