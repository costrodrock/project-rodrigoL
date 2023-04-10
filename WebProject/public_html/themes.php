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

		// Retrieve filter inputs from the search bar
		$search = $_GET['search'] ?? '';
		$sort_by = $_GET['sort_by'] ?? 'date';
		$order = $_GET['order'] ?? 'desc';
		$page = $_GET['page'] ?? 1;

		// Calculate the offset based on the current page
		$limit = 10;
		$offset = ($page - 1) * $limit;

		// Get the theme information from the database
		$query = "SELECT themes.*, COUNT(posts.postID) as num_posts, GROUP_CONCAT(posts.postID SEPARATOR ',') as postIDs, users.username, posts.date
          FROM themes
          LEFT JOIN posts ON posts.themeID = themes.themeID
          JOIN users ON posts.userID = users.userID
          WHERE themes.title LIKE '%$search%' OR themes.description LIKE '%$search%'
          GROUP BY themes.themeID
          ORDER BY $sort_by $order
          LIMIT $limit OFFSET $offset";
		$result = mysqli_query($connection, $query);

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

		<!-- Main Content -->
		<!-- Filter Search Bar -->
		<div class="col mt-3">
			<form class="form-inline">
				<label class="sr-only" for="search">Search Posts</label>
				<input type="text" class="form-control mb-2 mr-sm-2" id="search" name="search" placeholder="Search Posts" value="<?php echo $search; ?>">

				<label class="sr-only" for="sort_by">Sort By</label>
				<select class="form-control mb-2 mr-sm-2" id="sort_by" name="sort_by">
					<option value="title"<?php if ($sort_by == 'title') echo ' selected'; ?>>Title</option>
					<option value="likes"<?php if ($sort_by == 'likes') echo ' selected'; ?>>Likes</option>
					<option value="comments"<?php if ($sort_by == 'comments') echo ' selected'; ?>>Comments</option>
					<option value="date"<?php if ($sort_by == 'date') echo ' selected'; ?>>Most Recent</option>
				</select>

				<label class="sr-only" for="order">Order</label>
				<select class="form-control mb-2 mr-sm-2" id="order" name="order">
					<option value="desc"<?php if ($order == 'desc') echo ' selected'; ?>>Descending</option>
					<option value="asc"<?php if ($order == 'asc') echo ' selected'; ?>>Ascending</option>
				</select>

				<button type="submit" class="btn btn-primary mb-2">Search</button>
			</form>
		</div>
		<!-- Theme List -->
		<div class="col-md-8">
			<h3>Explore Posts</h3>
			<ul class="list-unstyled">
			<?php
				// Get the theme ID from the URL parameter
				$themeID = isset($_GET['themeID']) ? $_GET['themeID'] : '';

				// Query the database for the themes
				$queryCount = "SELECT COUNT(*) as total FROM themes WHERE title LIKE '%$search%'";
				$resultCount = $connection->query($queryCount);
				$rowCount = $resultCount->fetch_assoc();
				$total_pages = ceil($rowCount['total'] / $limit);

				// If search bar is empty, query all themes in the 'themes' table
				if (empty($search)) {
					$queryCount = "SELECT * FROM themes ORDER BY $sort_by $order LIMIT $limit OFFSET $offset";
				} else {
					// Search bar is not empty, query the database for themes that match the search term
					$queryCount = "SELECT * FROM themes WHERE title LIKE '%$search%' ORDER BY $sort_by $order LIMIT $limit OFFSET $offset";
				}

				$result = $connection->query($query);

				// Display the posts
				if ($result->num_rows > 0) {
					while ($row = $result->fetch_assoc()) {
						$themeID = $row['themeID'];
						$title = $row['title'];
						$description = $row['description'];

						// Query for 'likes' for row matching 'themeID'
						$queryAmount = "SELECT amount FROM themes
										WHERE themeID = $themeID";
						$resultAmount = $connection->query($queryAmount);
						$amount = $resultAmount->fetch_assoc()['amount'];
						echo '<li>
								<h4><a href="themes.php?themeID=' . $themeID . '">' . $title . '</a></h4>
								<p>' . substr($description, 0, 150) . '...</p>
								<p><small class="text-muted">' . $amount . ' posts</small></p>
							</li>';

						// Display the posts related to the themeID
						$queryPosts = "SELECT p.postID, p.title, p.content, p.date, u.username, COUNT(c.commentID) as num_comments, p.themeID 
										FROM posts p 
										JOIN users u ON p.userID = u.userID 
										LEFT JOIN comments c ON p.postID = c.postID 
										WHERE p.themeID IN (
											SELECT themeID FROM themes WHERE title LIKE '%$search%'
										)
										GROUP BY p.postID, p.title, p.content, p.date, u.username, p.themeID 
										ORDER BY $sort_by $order 
										LIMIT $limit OFFSET $offset";
						$resultPosts = $connection->query($queryPosts);

        
						// Check if there are any posts related to the themeID
						if ($resultPosts->num_rows > 0) {
							while ($rowPosts = $resultPosts->fetch_assoc()) {
								$postID = $rowPosts['postID'];
								$postTitle = $rowPosts['title'];
								$postContent = $rowPosts['content'];
								$postDate = date('F j, Y', strtotime($rowPosts['date']));
								$numComments = $rowPosts['num_comments'];
								$postAuthor = $rowPosts['username'];

								// Query for 'likes' for row matching 'postID'
								$queryLikes = "SELECT likes FROM posts WHERE postID = $postID";
								$resultLikes = $connection->query($queryLikes);
								$numLikes = $resultLikes->fetch_assoc()['likes'];

								//Display post information and number of likes
								echo "<div class='post'>";
								echo "<h2 class='post-title'><a href='post.php?id=$postID'>$postTitle</a></h2>";
								echo "<p class='post-meta'>Posted by <a href='user.php?username=$postAuthor'>$postAuthor</a> on $postDate</p>";
								echo "<p>$postContent</p>";
								echo "<p class='post-likes'>$numLikes likes</p>";
								echo "<p class='post-comments'>$numComments comments</p>";
								echo "</div>";
							}
						} else {
							// Display message if there are no posts related to the themeID
							echo "<div class='post'>";
							echo "<h2 class='post-title'>No posts found for this theme.</h2>";
							echo "</div>";
						}
					}
				}
			?>
		</div>

		<!-- Script to prompt user before deleting a comment/post -->
		<script>
			function confirmDelete() {
				return confirm("Are you sure you want to delete this comment?");
			}
		</script>

		<!-- Script to hide post content & revert back -->
		<script>
			//Hides: postContent, edit, and delete & displays: editForm
			function editPost() {
				document.getElementById("postContent").style.display = "none";
				document.getElementById("editForm").style.display = "block";
				document.getElementById("editContent").focus();
				document.getElementById("editBtn").style.display = "none";
				document.getElementById("deleteBtn").style.display = "none";
			}
			//Reverts actions by editPost()
			function cancelEdit() {
				document.getElementById("postContent").style.display = "block";
				document.getElementById("editForm").style.display = "none";
				document.getElementById("editBtn").style.display = "inline-block";
				document.getElementById("deleteBtn").style.display = "inline-block";
			}
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