<!DOCTYPE html>
<html>
	<?php
		session_start();
		// Establish a database connection
		$host = "localhost";
		$database = "WebProject";
		$user = "webuser";
		$password = "P@ssw0rd";

		// Check if the user is already logged in
		if (!isset($_SESSION["username"])) {
		    header("Location: login.php");
		    exit();
		}
        
		// Establish a database connection
		$connection = new mysqli($host, $user, $password, $database);
        
        // Get the user information from the database
        $userID = $_SESSION["userID"];
        $query = "SELECT * FROM users WHERE userID = '$userID'";
		$result = mysqli_query($connection, $query);
		$user = mysqli_fetch_assoc($result);
	 ?>
    
    <head>
        <meta charset="UTF-8">
        <title>User Profile</title>

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

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">Username: <?= $user["username"] ?></p>
                            <p class="card-text">Email: <?= $user["email"] ?></p>
                            <?php
                                // Display user image if available
                                $query = "SELECT * FROM userimages WHERE userID = '$userID'";
                                $result = mysqli_query($connection, $query);
                                $image = mysqli_fetch_assoc($result);

                                if ($image) {
                                    // Show the existing user image if it's available and the user didn't click on the "edit" button
                                    $contentType = $image['contentType'];
                                    $base64 = base64_encode($image['image']);
                                    $src = "data:$contentType;base64,$base64";
                                    echo "<img src=\"$src\" alt=\"User Image\">";
                                }
                            ?>

                            <button class="btn btn-primary mr-2" id="editBtn" onclick="editUser()">Edit</button>
                            <form action="deleteuser.php?userID=' . $userID . '" method="post">
							    <button class="btn btn-danger" id="deleteBtn" onclick="return confirmDelete();">Delete</button>
							</form>
                            <a href="logout.php" class="btn btn-primary">Logout</a>
                        </div>
                    </div>
                </div>

                 <!-- Edit post form (hidden by default) -->
                <form id="editForm" action="edituser.php?userID=<?php echo $userID; ?>" method="POST" style="display: none;" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?= $user['firstName'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?= $user['lastName'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= $user['email'] ?>">
                                </div>
                                <div class="form-group">
                                    <label for="userImage">Profile Image</label>
                                    <input type="file" class="form-control-file" id="userImage" name="userImage">
                                </div>
                                <button type="submit" class="btn btn-success">Save</button>
                                <button type="button" class="btn btn-danger" onclick="cancelEdit()">Cancel</button>
                                <input type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>">
                </form>

                <!-- Delete confirmation modal -->
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <p>Are you sure you want to delete this user?</p>
                      </div>
                       <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <form method="POST">
                          <button type="submit" class="btn btn-danger" name="confirm-delete">Delete</button>
                        </form>
                      </div>
                    </div>
                </div>

                <!-- Display themes and posts that user is part of -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Themes you've posted in:</h5>
                            <?php
                                // Display all themes user has posts in
                                $query = "SELECT DISTINCT themes.* FROM posts JOIN themes ON posts.themeID = themes.themeID WHERE userID = '$userID'";
                                $result = mysqli_query($connection, $query);

                                if (mysqli_num_rows($result) > 0) {
                                    echo '<ul class="list-group">';
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo '<li class="list-group-item"><a href="themes.php?themeID=' . $row["themeID"] . '">' . $row["title"] . '</a></li>';
                                    }
                                    echo '</ul>';
                                } else {
                                    echo '<p class="card-text">You have not posted in any themes yet.</p>';
                                }
                            ?>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Posts you've made:</h5>
                            <?php
                            // Display all posts user has made
                            $query = "SELECT * FROM posts WHERE userID = '$userID'";
                            $result = mysqli_query($connection, $query);

                            if (mysqli_num_rows($result) > 0) {
                                echo '<ul class="list-group">';
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo '<li class="list-group-item"><a href="post.php?postID=' . $row["postID"] . '">' . $row["title"] . '</a></li>';
                                }
                                echo '</ul>';
                            } else {
                                echo '<p class="card-text">You have not made any posts yet.</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script to hide user content & revert back -->
        <script>
            function editUser() {
                document.getElementById("editForm").style.display = "block";
                document.getElementById("editBtn").style.display = "none";
            }

            function cancelEdit() {
                document.getElementById("editForm").style.display = "none";
                document.getElementById("editBtn").style.display = "block";
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