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
	?>
	<head>
		<meta charset="UTF-8">
		<title>Board the Discussion</title>
		
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
						<?php
							if (isset($_SESSION['username'])){
								echo '<a class="nav-link" href="submitPost.php?userID=' . $_SESSION['userID'] . '">Create Post</a>';
							} else {
								echo '<a class="nav-link disabled" href="submitPost.php" disabled>Create Post</a>';
							}
						?>
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
		<div class="container-fluid mt-3">
			<div class="row justify-content-between">
				<div class="col-9 ml-3">
					<div class="col">
						<h1>Welcome aboard the discussion!</h1>
						<p>Where you'll be able to view, join, and contribute to what other people are saying around the globe.</p>
					</div>
					<div class="col">
						<h3>See what's new!</h3>
						<ul class="list-unstyled">
						<?php
							$query = "SELECT * FROM posts ORDER BY date DESC LIMIT 5";
							$result = $connection->query($query);

							while ($row = $result->fetch_assoc()) {
								if (isset($_SESSION['userID'])) {
									echo '<li><a href="post.php?postID=' . $row['postID'] . '&userID=' . $_SESSION['userID'] . '">' . $row['title'] . '</a></li>';
								} else {
									echo '<li><a href="post.php?postID=' . $row['postID'] . '">' . $row['title'] . '</a></li>';
								}
							}
						?>
					</div>
				</div>
				<div class="col-2 stick-right">
					<div class="col">
					  <h4>Top Posts</h4>
					  <ul class="list-unstyled">
						<?php
							$query = "SELECT * FROM posts ORDER BY date DESC LIMIT 5";
							$result = $connection->query($query);

							while ($row = $result->fetch_assoc()) {
								if (isset($_SESSION['userID'])) {
									echo '<li><a href="post.php?postID=' . $row['postID'] . '&userID=' . $_SESSION['userID'] . '">' . $row['title'] . '</a></li>';
								} else {
									echo '<li><a href="post.php?postID=' . $row['postID'] . '">' . $row['title'] . '</a></li>';
								}
							}
						?>
					  </ul>
					</div>
					<div class="col">
					  <h4>Top Themes</h4>
					  <ul class="list-unstyled">
						<?php

						  $query = "SELECT themeID, SUM(themeID) as total_amount FROM themes GROUP BY themeID ORDER BY total_amount DESC LIMIT 5";
						  $result = $connection->query($query);

						  while ($row = $result->fetch_assoc()) {
							echo '<li><a href="#">' . $row['themeID'] . ' (' . $row['total_amount'] . ')</a></li>';
						  }
						?>
					  </ul>
					</div>
			</div>
		</div>
		<?php
			mysqli_close($connection);
		?>
		<!-- Count the number of posts and calculate the container height -->
		<script>
		  var postCount = document.querySelectorAll("#newPosts li").length;
		  var containerHeight = 90 + (postCount * 40); // 90 is the height of the other elements in the container
		  document.querySelector(".container").style.marginBottom = containerHeight + "px";
		</script>

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