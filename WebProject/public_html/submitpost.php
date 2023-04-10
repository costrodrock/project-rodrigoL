<!DOCTYPE html>
<html>
	<?php
		session_start();
		// Check if the user is already logged in
		if (!isset($_SESSION["username"])) {
			header("Location: login.php");
			exit();
		}
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
		<title>Create Post</title>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
		
		<link rel="stylesheet" href="style.css">
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
						<a class="nav-link" href="findthemes.php?userID=' . $_SESSION['userID'] . '">Themes</a>
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
					<li class="nav-item">
						<?php
							if (isset($_SESSION['username'])){
								echo '<a class="nav-link" href="submitTheme.php?userID=' . $_SESSION['userID'] . '">Create Theme</a>';
							} else {
								echo '<a class="nav-link disabled" href="submitTheme.php" disabled>Create Theme</a>';
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

		<!-- Create Post -->
		<div class="container mt-3">
				<div class="row">
					<div class="col-md-8">
						<h1>Submit a Blog Post</h1>
						<form action="add_post.php" method="POST">
							<div class="form-group">
								<label for="title">Title:</label>
								<input type="text" class="form-control" id="title" name="title" placeholder="Enter title">
							</div>
							<div class="form-group">
								<label for="author">Author:</label>
								<pass type="text" class="form-control" id="username" name="username"><?php echo $_SESSION["username"]; ?></pass>
							</div>
							<div class="form-group">
								<label for="content">Content:</label>
								<textarea class="form-control" id="content" name="content" rows="5" placeholder="Enter blog post content"></textarea>
							</div>
							<div class="form-group">
								<label for="themeID">Select a theme:</label>
								<select class="form-control" id="themeID" name="themeID">
									<?php
										// Retrieve all the themes from the database
										$query = "SELECT * FROM themes";
										$result = mysqli_query($connection, $query);
										
										// Loop through the themes and add them as options in the select input
										while ($theme = mysqli_fetch_assoc($result)) {
											echo '<option value="' . $theme['themeID'] . '">' . $theme['title'] . '</option>';
										}
									?>
								</select>
							</div>
							<button type="submit" class="btn btn-primary">Submit</button>
						</form>
					</div>
				</div>
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