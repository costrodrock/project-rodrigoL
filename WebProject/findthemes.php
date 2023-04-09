<!DOCTYPE html>
<html>
	<?php
		session_start();
		// Establish a database connection
		$host = "localhost";
		$database = "WebProject";
		$user = "webuser";
		$password = "P@ssw0rd";

		// Retrieve filter inputs from the search bar
		$search = $_GET['search'] ?? '';
		$sort_by = $_GET['sort_by'] ?? 'title';
		$order = $_GET['order'] ?? 'desc';
		$page = $_GET['page'] ?? 1;
	
		// Calculate the offset based on the current page
		$limit = 10;
		$offset = ($page - 1) * $limit;
	
		// Establish a database connection
		$mysqli = new mysqli($host, $user, $password, $database);


		if ($mysqli->connect_errno) {
			echo "Failed to connect to MySQL: " . $mysqli->connect_error;
			exit();
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
						<a class="nav-link" href="submitPost.php">Create Post</a>
					</li>
					<li>
						<a class="nav-link" href="secure.php">Profile</a>
					</li>
					<?php
						if (!isset($_SESSION["username"])) {
							echo '<li class="nav-item"><a class="nav-link" href="login.php">Log in</a></li>';
							echo '<li class="nav-item"><a class="nav-link" href="newuser.php">Sign up</a></li>';
						} else {
							echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
						}
					?>
					</ul>
			</div>
		</nav>

		<!-- Main Content -->
		<div class="container mt-3">
			<div class="row justify-content-between">
				<!-- Filter Search Bar -->
				<div class="col mt-3">
					<form class="form-inline">
						<label class="sr-only" for="search">Search Themes</label>
						<input type="text" class="form-control mb-2 mr-sm-2" id="search" name="search" placeholder="Search Themes" value="<?php echo $search; ?>">

						<label class="sr-only" for="sort_by">Sort By</label>
						<select class="form-control mb-2 mr-sm-2" id="sort_by" name="sort_by">
							<option value="title"<?php if ($sort_by == 'title') echo ' selected'; ?>>Title</option>
							<option value="num_posts"<?php if ($sort_by == 'num_posts') echo ' selected'; ?>>Number of Posts</option>
						</select>

						<label class="sr-only" for="order">Order</label>
						<select class="form-control mb-2 mr-sm-2" id="order" name="order">
							<option value="desc"<?php if ($order == 'desc') echo ' selected'; ?>>Descending</option>
							<option value="asc"<?php if ($order == 'asc') echo ' selected'; ?>>Ascending</option>
						</select>

						<button type="submit" class="btn btn-primary mb-2">Search</button>

						<?php if (isset($_SESSION['username'])): ?>
							<a href="submittheme.php?userID=' . $_SESSION['userID'] . '" class="btn btn-primary mb-2">Create</a>
						<?php endif; ?>
					</form>
				</div>
				<!-- Theme List -->
				<div class="col-md-8">
					<h3>Explore Themes</h3>
					<ul class="list-unstyled">
						<?php
							// Query the database for the themes
							$query = "SELECT COUNT(*) as total FROM themes WHERE title LIKE '%$search%'";

							$result = $mysqli->query($query);

							// Display the themes
							if ($result->num_rows > 0) {
								while ($row = $result->fetch_assoc()) {
									$themeID = $row['themeID'];
									$title = $row['title'];
									$description = $row['description'];
									$num_posts = $row['num_posts'];
									echo '<div class="card my-3">
											<div class="card-body">
												<h5 class="card-title"><a href="themes.php?themeID=' . $themeID . '">' . $title . '</a></h5>
												<p class="card-text">' . $description . '</p>
												<span class="badge badge-secondary">' . $num_posts . ' posts</span>
											</div>
										</div>';
								}
							} else {
								// Display "No results found" message if no posts match the search term
								echo '<div class="card my-3">
										<div class="card-body">
											<h5 class="card-title">No results found</h5>
										</div>
									</div>';
							}
						?>
					</ul>
					<nav aria-label="Page navigation">
						<ul class="pagination justify-content-center">
							<?php
								// Get total number of posts and check if searchbar is empty
								if (empty($search)) {
									$query = "SELECT COUNT(*) as total FROM posts";
								} else {
									$query = "SELECT COUNT(*) as total FROM posts WHERE title LIKE '%$search%'";
								}
								$result = $mysqli->query($query);
								$total_posts = $result->fetch_assoc();
								$total_pages = ceil($total_posts['total'] / $limit);

								// Display pagination links
								for ($i = 1; $i <= $total_pages; $i++) {
									$active_class = $page == $i ? ' active' : '';
									echo "<li class='page-item$active_class'><a class='page-link' href='index.php?search=$search&sort_by=$sort_by&order=$order&page=$i'>$i</a></li>";
								}
							?>
						</ul>
					</nav>
				</div>
			</div>
		</div>

		<?php
			$mysqli->close();
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