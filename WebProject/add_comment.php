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

        // Check if the session variable is set
        if(isset($_SESSION['username']) && $_SERVER["REQUEST_METHOD"] == "POST") {
            // Get the current user's ID based on their username
            $username = $_SESSION['username'];
            $query = "SELECT userID FROM users WHERE username = '$username'";
            $result = $connection->query($query);
            $user = $result->fetch_assoc();
            $userID = $user['userID'];

            // Check if the userID exists in the users table
            $query = "SELECT COUNT(*) as count FROM users WHERE userID = '$userID'";
            $result = $connection->query($query);
            $row = $result->fetch_assoc();

            if ($row['count'] == 0) {
                echo "Error: User does not exist.";
            } else {
                // Get post data
                $postID = $_GET['postID'];
                $content = $_POST['content'];
                $date = date('Y-m-d H:i');

                // Insert the comment into the database
                $query = "INSERT INTO comments (postID, userID, content, date) VALUES ('$postID', '$userID', '$content', '$date')";
                if ($connection->query($query)) {
                    // Close the database connection
                    $connection->close();

                    // Redirect back to the post page
                    $referer = $_SERVER['HTTP_REFERER'];
                    header("Location: $referer");
                    exit();
                } else {
                    echo "Error: " . $query . "<br>" . mysqli_error($connection);
                }
            }
        } else {
            echo "You are not logged in.";
        }

        // Close the database connection
        $connection->close();
    ?>
</html>
