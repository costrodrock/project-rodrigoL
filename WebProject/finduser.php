<!DOCTYPE html>
<html>

<p>Here are some results:</p>
<?php
    session_start();
    $host = "localhost";
    $database = "webProject";
    $user = "webuser";
    $password = "P@ssw0rd";

    $connection = mysqli_connect($host, $user, $password, $database);

    $error = mysqli_connect_error();
    if ($error != null) {
        $output = "<p>Unable to connect to database!</p>";
        exit($output);
    }

    // Check if the form has been submitted via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all required parameters are set
        if (!isset($_POST["username"])) {
            echo "<p>Missing username parameter!</p>";
        } else {
            $username = $_POST["username"];

            // Check if user exists in database with given username
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = mysqli_query($connection, $sql);
            if (mysqli_num_rows($result) > 0) {
                // User found, display their info
                $row = mysqli_fetch_assoc($result);
                echo "<fieldset><legend>User Info:</legend>";
                echo "<table>";
                echo "<tr><td>First Name:</td><td>{$row['firstName']}</td></tr>";
                echo "<tr><td>Last Name:</td><td>{$row['lastName']}</td></tr>";
                echo "<tr><td>Email:</td><td>{$row['email']}</td></tr>";
                echo "<tr><td>userID:</td><td>{$row['userID']}</td></tr>";
                echo "</table></fieldset>";
                
                // Get the user's image
                $sql = "SELECT contentType, image FROM userImages WHERE userID=?";
                $stmt = mysqli_stmt_init($connection);
                mysqli_stmt_prepare($stmt, $sql);
                mysqli_stmt_bind_param($stmt, "i", $userID);
                $result = mysqli_stmt_execute($stmt) or die(mysqli_stmt_error($stmt));
                mysqli_stmt_bind_result($stmt, $type, $image);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
                
                // Output the user's image
                echo '<img src="data:image/'.$type.';base64,'.base64_encode($image).'"/>';
            } else {
                echo "<p>User not found.</p>";
            }
        }
    }
    mysqli_close($connection);
?>


</html>
