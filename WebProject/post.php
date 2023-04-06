<!DOCTYPE html>
<html>
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

		// Get the post information from the database
		$postID = $_GET['postID'];
		$query = "SELECT * FROM posts WHERE postID='$postID'";
		$result = mysqli_query($connection, $query);
		$post = mysqli_fetch_assoc($result);
	?>
	<head>
		<meta charset="UTF-8">
		<title>Board the Discussion - <?php echo $post['title']; ?></title>
		
		<link rel="stylesheet" href="style.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	</head>
	<body>
		<!-- Navigation Bar -->
		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<a class="navbar-brand" href="home.php">Board the Discussion</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-end" id="navbarNav">
				<ul class="navbar-nav">
					<span class="navbar-text justify-content-right">
						Logged in as: <?php if(!isset($_SESSION["username"])){
											echo "No one";
										 } else { 
											echo $_SESSION["username"]; }
									  ?>
					</span>
					<li class="nav-item active">
						<a class="nav-link" href="home.php">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="findpost.php">Navigate</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="submitPost.php">Create Post</a>
					</li>
					<li>
						<a class="nav-link" href="secure.php">Profile</a>
					</li>
					<?php
						if (!isset($_SESSION["username"])) {
							echo '<li class="nav-item"><a class="nav-link" href="login.php">Log in</a></li>';
							echo '<li class="nav-item"><a class="nav-link" href="newuser.html">Sign up</a></li>';
						} else {
							echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
						}
					?>
					</ul>
			</div>
		</nav>

		<!-- Main Content -->
		<div class="container mt-3">
			<h1><?php echo $post['title']; ?></h1>
			<p>Posted by <?php echo $post['username']; ?> on <?php echo $post['date']; ?></p>
			<hr>
			<p><?php echo $post['content']; ?></p>

			<!-- Form to submit a new comment -->
			<form method="POST" action="add_comment.php">
				<input type="hidden" name="postID" value="<?php echo $postID; ?>">
				<input type="hidden" name="username" value="<?php echo $_SESSION["username"]; ?>">
				<div class="form-group">
					<label for="content">Comment:</label>
					<textarea class="form-control" name="content" placeholder="Write a comment here..." required></textarea>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>

			<!-- Display existing comments -->
			<h2>Comments:</h2>
			<?php
				// Query the comments table for comments on the current post
				$query = "SELECT * FROM comments WHERE postID = '$postID'";
				$result = $connection->query($query);

				// Loop through the comments and display each one
				while ($comment = $result->fetch_assoc()) {
					// Query the users table to get the username for the current comment
					$query = "SELECT username FROM users WHERE userID = '{$comment['userID']}'";
					$userResult = $connection->query($query);
					$user = $userResult->fetch_assoc();

					echo '<div class="comment">';
					echo '<p>Comment by ' . $user['username'] . ' on ' . $comment['date'] . '</p>';
					echo '<p>' . $comment['content'] . '</p>';
					echo '</div>';
				}
			?>
		</div>

		<!-- Footer -->
		<footer class="bg-dark text-white mt-3 p-3 text-center">
			<p>&copy; 2023 Board the Discussion</p>
		</footer>

		<!-- Bootstrap JS -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

	</body>
</html>