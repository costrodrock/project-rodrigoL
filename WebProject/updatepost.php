<!DOCTYPE>
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

    // Get the comment ID from the URL parameter
    $postID = $_GET['postID'];

    // Determine which button was clicked
    switch (true) {
            case isset($_POST['likeBtn']):
                $postID = $_GET['postID'];
                // Retrieve the current likes, liked_by, and disliked_by values in a single query
                $sql = "SELECT likes, liked_by, disliked_by FROM posts WHERE postID = ?";
                $stmt = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt, 'i', $postID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_array($result);

                // Extract the values from the query result
                $likes = $row['likes'];
                $liked_by = unserialize($row['liked_by']);
                $disliked_by = unserialize($row['disliked_by']);

                // Check if the user has already liked the comment
                if (!empty($liked_by) && in_array($_SESSION['userID'], $liked_by)) {
                    // If the user has already liked the comment, remove their like
                    $likes--; // Decrement likes count
                    $liked_by = array_diff($liked_by, array($_SESSION['userID'])); // Remove user from liked_by array
                } else {
                    // If the user has not already liked the comment, update the 'liked_by' column of the row in the 'posts' table
                    $likes++; // Increment likes count
                    $liked_by[] = $_SESSION['userID']; // Add user to liked_by array

                    // If the user has already disliked the comment, remove their dislike and add like instead
                    if (!empty($disliked_by) && in_array($_SESSION['userID'], $disliked_by)) {
                        $disliked_by = array_diff($disliked_by, array($_SESSION['userID'])); // Remove user from disliked_by array
                    }
                }
                // Serialize the liked_by and disliked_by arrays
                $serialized_liked_by = serialize($liked_by);
                $serialized_disliked_by = serialize($disliked_by);

                // Update the 'likes', 'liked_by', and 'disliked_by' columns in the 'posts' table
                $sql = "UPDATE posts SET likes = ?, liked_by = ?, disliked_by = ? WHERE postID = ?";
                $stmt = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt, 'isss', $likes, $serialized_liked_by, $serialized_disliked_by, $postID);
                mysqli_stmt_execute($stmt);
            break;
            case isset($_POST['dislikeBtn']):
                $postID = $_GET['postID'];
                // Retrieve the current likes, liked_by, and disliked_by values in a single query
                $sql = "SELECT likes, liked_by, disliked_by FROM posts WHERE postID = ?";
                $stmt = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt, 'i', $postID);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $row = mysqli_fetch_array($result);

                // Extract the values from the query result
                $likes = $row['likes'];
                $liked_by = unserialize($row['liked_by']);
                $disliked_by = unserialize($row['disliked_by']);

                // Check if the user has already disliked the comment
                if (!empty($disliked_by) && in_array($_SESSION['userID'], $disliked_by)) {
                    // If the user has already disliked the comment, remove their dislike
                    $likes++; // Increment likes count
                    $disliked_by = array_diff($disliked_by, array($_SESSION['userID'])); // Remove user from disliked_by array
                } else {
                    // If the user has not already disliked the comment, update the 'disliked_by' column of the row in the 'posts' table
                    $likes--; // Decrement likes count
                    $disliked_by[] = $_SESSION['userID']; // Add user to disliked_by array

                    // If the user has already liked the comment, remove their like and add dislike instead
                    if (!empty($liked_by) && in_array($_SESSION['userID'], $liked_by)) {
                        $liked_by = array_diff($liked_by, array($_SESSION['userID'])); // Remove user from liked_by array
                    }
                }
                // Serialize the liked_by and disliked_by arrays
                $serialized_liked_by = serialize($liked_by);
                $serialized_disliked_by = serialize($disliked_by);

                // Update the 'likes', 'liked_by', and 'disliked_by' columns in the 'posts' table
                $sql = "UPDATE posts SET likes = ?, liked_by = ?, disliked_by = ? WHERE postID = ?";
                $stmt = mysqli_prepare($connection, $sql);
                mysqli_stmt_bind_param($stmt, 'isss', $likes, $serialized_liked_by, $serialized_disliked_by, $postID);
                mysqli_stmt_execute($stmt);
            break;
            case false:
                // If neither button was clicked, redirect to the referring page
                $referer = $_SERVER['HTTP_REFERER'];
                header("Refresh: 0; URL=$referer");
                exit();
            break;
    }


    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // If the update(s) were successful, redirect to the referring page
        $referer = $_SERVER['HTTP_REFERER'];
        header("Refresh: 0; URL=$referer");
        exit();
    } else {
        // If there was an error, display the error message
        echo "Error updating comment";
    }
    // Close the statement
    mysqli_stmt_close($stmt);

    // Close the connection
    $connection->close();
?>