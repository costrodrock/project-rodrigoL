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

        // Get the post ID and comment data from the form submission
        $postID = $_POST['postID'];
        $content = $_POST['content'];
        $date = date('Y-m-d H:i');

        // Get the current user's ID based on their username
        $username = $_SESSION['username'];
        $query = "SELECT userID FROM users WHERE username = '$username'";
        $result = $connection->query($query);
        $user = $result->fetch_assoc();
        $userID = $user['userID'];

        // Insert the comment into the database
        $query = "INSERT INTO comments (postID, userID, content, date) VALUES ('$postID', '$userID', '$content', '$date')";
        $result = $connection->query($query);

        // Redirect back to the post page
        $referer = $_SERVER['HTTP_REFERER'];
		header("Refresh: 0; URL=$referer");
		exit();
    ?>
</html>




