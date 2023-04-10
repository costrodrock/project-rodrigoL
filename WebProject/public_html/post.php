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
		$query = "SELECT posts.*, users.username FROM posts JOIN users ON posts.userID = users.userID WHERE postID='$postID'";
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
		<!-- Display Post -->
		<div class="card mb-4">
			<div class="card-body">
				<?php if(isset($_SESSION['userID'])){$userID = $_GET['userID'];}?>
				<h1 class="card-title"><?php echo $post['title']; ?></h1>
				<p class="card-text">Posted by <?php echo $post['username']; ?> on <?php echo $post['date']; ?></p>
				<p class="card-text"><?php echo $post['content']; ?></p>
				<?php
					// Check if viewer is logged in
					if (isset($_SESSION['userID'])) {
						// Check if current user is the author of the post
						if ($_SESSION['userID'] == $post['userID']) {
							// Show edit and delete buttons
							echo '<button class="btn btn-primary mr-2" id="editBtn" onclick="editPost()">Edit</button>';
							echo '<form action="deletepost.php?postID=' . $postID . '&userID=' . $userID . '" method="post">';
							echo '<button class="btn btn-danger" id="deleteBtn" onclick="return confirmDelete();">Delete</button>';
							echo '</form>';
						}
						// Show like and dislike buttons
						echo '<form action="updatepost.php?postID=' . $postID . '" method="post">';
						echo '<button type="submit" class="btn btn-success mr-2" name="likeBtn">Like</button>';
						//Show number of likes in between buttons
						$query = "SELECT likes FROM posts WHERE postID = '$postID'";
						$result = mysqli_query($connection, $query);
						$row = mysqli_fetch_assoc($result);
						$likes = $row['likes'];
						echo '<div class="btn btn-info" style="pointer-events: none;">Likes (' . $likes . ')</div>';
						echo '<button type="submit" class="btn btn-danger mr-2" name="dislikeBtn">Dislike</button>';
						echo '</form>';
						// Show number of comments button
						$query = "SELECT COUNT(*) AS total_comments FROM comments WHERE postID = '$postID'";
						$result = mysqli_query($connection, $query);
						$row = mysqli_fetch_assoc($result);
						$total_comments = $row['total_comments'];
						echo '<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#commentsSection" aria-expanded="false" aria-controls="commentsSection">Comments (' . $total_comments . ')</button>';
						// Display comments
						echo '<div class="collapse mt-3" id="commentsSection">';
						$query = "SELECT * FROM comments WHERE postID = '$postID'";
						$result = mysqli_query($connection, $query);
						while ($comment = mysqli_fetch_assoc($result)) {
							echo '<div class="card mb-3">';
							echo '<div class="card-body">';
							echo '<h6 class="card-subtitle mb-2 text-muted">Commented by ' . $comment['username'] . ' on ' . $comment['date'] . '</h6>';
							echo '<p class="card-text">' . $comment['content'] . '</p>';
							echo '</div>';
							echo '</div>';
						}
						echo '</div>';
					} else {
						//Show number of likes
						$query = "SELECT likes FROM posts WHERE postID = '$postID'";
						$result = mysqli_query($connection, $query);
						$row = mysqli_fetch_assoc($result);
						$likes = $row['likes'];
						echo '<div class="btn btn-info" style="pointer-events: none;">Likes (' . $likes . ')</div>';
						// Show number of comments button
						$query = "SELECT COUNT(*) AS total_comments FROM comments WHERE postID = '$postID'";
						$result = mysqli_query($connection, $query);
						$row = mysqli_fetch_assoc($result);
						$total_comments = $row['total_comments'];
						echo '<div class="btn btn-info" style="pointer-events: none;">Comments (' . $total_comments . ')</div>';
					}
				?>
			</div>
		</div>

			<!-- Edit post form (hidden by default) -->
			<form id="editForm" action="editpost.php?postID=<?php echo $postID; ?>" method="post" style="display: none;">
				<textarea class="form-control mb-3" name="content" id="editContent" placeholder="<?php echo $post['content']; ?>"><?php echo $post['content']; ?></textarea>
				<button class="btn btn-success mr-2" type="submit">Save</button>
				<button class="btn btn-secondary" type="button" onclick="cancelEdit()">Cancel</button>
			</form>

			<!-- Form to submit a new comment -->
			<?php
				if (isset($_SESSION['userID'])){ 
					echo "<form method='POST' action='add_comment.php?postID=$postID'>";
					echo '<div class="form-group">';
					echo '<label for="content">Comment:</label>';
					echo '<textarea class="form-control" name="content" placeholder="Write a comment here..." required></textarea>';
					echo '</div>';
					echo '<input type="hidden" name="postID" value="<?php echo $postID; ?>">';
					echo '<button type="submit" class="btn btn-primary">Submit</button>';
					echo '</form>';
				}
			?>

			<!-- Display existing comments -->
			<h2>Comments:</h2>
			<?php
				// Get postID and userID from URL parameters
				$postID = $_GET['postID'];
				if(isset($_SESSION['userID'])){$userID = $_GET['userID'];} else {$userID = NULL;}

				// Query comments table for comments on current post and join with users table to get username for each comment
				$queryComments = "SELECT comments.*, users.username FROM comments JOIN users ON comments.userID = users.userID WHERE comments.postID = '$postID'";
				$resultComments = $connection->query($queryComments);

				// Loop through comments and display each one
				echo '<ul>';
				while ($comment = $resultComments->fetch_assoc()) {
					// Get username for current comment
					$commentUserID = $comment['userID'];
					$queryUsername = "SELECT username FROM users WHERE userID = $commentUserID";
					$resultUsername = $connection->query($queryUsername);
					$rowUsername = $resultUsername->fetch_assoc();
					$username = $rowUsername['username'];

					// Display comment content and author's username
					echo '<li>';
					echo '<div class="comment">';
					echo '<p>Comment by ' . $username . ' on ' . $comment['date'] . '</p>';

					// Check if comment is being edited
					if (isset($_POST['edit']) && $_POST['commentID'] == $comment['commentID']) {
						// check if cancel button was clicked
						if (isset($_POST['cancel'])) {
							// turn input field back into a list object
							echo '<li>' . $comment['content'] . '</li>';
						} else {
							// display input box with current comment content as placeholder
							echo '<form action="editcomment.php?commentID=' . $comment['commentID'] . '" method="post">';
							echo '<input type="hidden" name="commentID" value="' . $comment['commentID'] . '">';
							echo '<input type="text" name="content" value="' . $comment['content'] . '" placeholder="' . $comment['content'] . '">';
							echo '<button type="submit" name="save">Save</button>';
							echo '<button type="submit" name="cancel" formaction="">Cancel</button>';
							echo '</form>';
						}
					} else {
						// Display comment content and author's username
						echo '<p>' . $comment['content'] . '</p>';

						// Query users table for current user's userID
						$queryUserID = "SELECT userID FROM users WHERE username='$username'";
						$resultUserID = mysqli_query($connection, $queryUserID);
						$rowUserID = mysqli_fetch_assoc($resultUserID);
						$current_userID = $rowUserID['userID'];

							// Check if viewer is logged in
						if (isset($_SESSION['userID'])) {
							// Show like and dislike buttons
							echo '<form action="updatecomment.php?commentID=' . $comment['commentID'] . '" method="post">';
							echo '<button type="submit" class="btn btn-success mr-2" name="likeBtn">Like</button>';
							// Show number of likes in between buttons
							$query = "SELECT likes FROM comments WHERE commentID = '{$comment['commentID']}'";
							$result = mysqli_query($connection, $query);
							$row = mysqli_fetch_assoc($result);
							$likes = $row['likes'];
							echo '<div class="btn btn-info" style="pointer-events: none;">Likes (' . $likes . ')</div>';
							echo '<button type="submit" class="btn btn-danger mr-2" name="dislikeBtn">Dislike</button>';
							echo '</form>';
						} else {
							// Show number of likes in between buttons
							$query = "SELECT likes FROM comments WHERE commentID = '{$comment['commentID']}'";
							$result = mysqli_query($connection, $query);
							$row = mysqli_fetch_assoc($result);
							$likes = $row['likes'];
							echo '<div class="btn btn-info" style="pointer-events: none;">Likes (' . $likes . ')</div>';
						}

						// Check if user is author of comment or an admin
						if ($current_userID == $userID || isset($_SESSION['admin'])) {
							// Display edit and delete buttons
							echo '<form action="post.php?postID=' . $postID . '&userID=' . $_SESSION['userID'] . '" method="post">';
							echo '<input type="hidden" name="commentID" value="' . $comment['commentID'] . '">';
							echo '<button type="submit" name="edit">Edit</button>';
							echo '</form>';
							echo '<form action="deletecomment.php?commentID=' . $comment['commentID'] . '" method="post">';
							echo '<input type="hidden" name="commentID" value="' . $comment['commentID'] . '">';
							echo '<button type="submit" onclick="return confirmDelete();"  id="deleteBtn" name="delete">Delete</button>';
							echo '</form>';
						}
					}
					echo '</div>';
					echo '</li>';
				}
				echo '</ul>';
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