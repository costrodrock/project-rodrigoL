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
    $userID = $_GET['userID'];

    // Delete the row from the 'comments' table
    $sql = "DELETE FROM comments WHERE postID = $postID";
    if ($connection->query($sql) === TRUE) {
        // Delete the row from the 'posts' table
        $sql = "DELETE FROM posts WHERE postID = $postID";
        if ($connection->query($sql) === TRUE) {
            // Get the userID from the 'posts' table
            $sql = "SELECT userID FROM users WHERE userID = $userID";
            $result = $connection->query($sql);
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $userID = $row["userID"];
                header("Location: home.php?userID=" . $userID);
                exit();
            } else {
                echo "Error getting userID from posts table: " . $connection->error;
            }
        } else {
            echo "Error deleting post: " . $connection->error;
        }
    } else {
        echo "Error deleting comments: " . $connection->error;
    }

    // Close the connection
    $connection->close();
?>