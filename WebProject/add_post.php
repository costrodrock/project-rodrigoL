<?php
    // Start the session
    session_start();

    // Establish a database connection
    $host = "localhost";
    $database = "WebProject";
    $user = "webuser";
    $password = "P@ssw0rd";

    $connection = mysqli_connect($host, $user, $password, $database);

    // Check if the session variable is set
    if (isset($_SESSION['username']) && $_SERVER["REQUEST_METHOD"] == "POST") {
        //Get userID
        $username = $_SESSION['username'];
        $query = "SELECT userID FROM users WHERE username = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userID);
        if (mysqli_stmt_fetch($stmt) === false) {
            echo "Error: Could not retrieve user ID.";
            exit();
        }
        mysqli_stmt_close($stmt);

        //Check if userID exist
        $stmt = mysqli_prepare($connection, "SELECT COUNT(*) as count FROM users WHERE userID = ?");
        mysqli_stmt_bind_param($stmt, 'i', $userID);
        mysqli_stmt_execute($stmt);
        $count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['count'];

        if ($count == 0) {
            echo "Error: User does not exist.";
        } else {
            //Get data for post
            $title = mysqli_real_escape_string($connection, $_POST['title']);
            $content = mysqli_real_escape_string($connection, $_POST['content']);
            $date = date('Y-m-d H:i');
            $themeID = $_POST['themeID'];

            //Insert post
            $query = "INSERT INTO posts (title, userID, content, date, likes, disliked_by, comments, themeID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            $likes = 0;
            $disliked_by = null;
            $comments = 0;
            mysqli_stmt_bind_param($stmt, 'sisssiii', $title, $userID, $content, $date, $likes, $disliked_by, $comments, $themeID);

            // Check if the themeID exists in the themes table
            $themeID = isset($_POST['themeID']) ? $_POST['themeID'] : null;

            if ($themeID != null) {
                $query = "SELECT COUNT(*) as count FROM themes WHERE themeID = ?";
                $stmt2 = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($stmt2, 'i', $themeID);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_store_result($stmt2);
                mysqli_stmt_bind_result($stmt2, $count2);
                mysqli_stmt_fetch($stmt2);
                mysqli_stmt_free_result($stmt2);

                if ($count2 == 0) {
                    echo "Error: Theme does not exist.";
                } else {
                    if ($stmt->execute()) {
                                $postID = $stmt->insert_id;
                                // Close the database connection
                                $stmt->close();
                                $connection->close();

                                // Redirect to the newly created post
                                header("Location: post.php?postID=$postID&userID=$userID");
                                exit();
                    } else {
                       echo "Error: " . $stmt->error;
                    }
                }
            }
        } 
    }
    // Close the database connection
	$connection->close();
?>