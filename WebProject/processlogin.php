<!DOCTYPE html>
<html>

<p>Here are some results:</p>
<?php
    session_start();
    // Check if the user is already logged in
    if (isset($_SESSION["username"])) {
        header("Location: home.php");
        exit();
    }

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

    // Check if the form has been submitted via POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if all required parameters are set
        if (!isset($_POST["username"]) || !isset($_POST["password"])) {
            echo "<p>Missing parameter(s)!</p>";
        } else {
            $username = $_POST["username"];
            $password = md5($_POST["password"]);

            // Check if user exists in database with given username and password
            $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
            $result = mysqli_query($connection, $sql);
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $_SESSION["username"] = $username;
                $_SESSION["userID"] = $row["userID"];
                header("Location: home.php?userID=" . $row["userID"]);
                exit();
            } else {
                echo "<p>Invalid username and/or password.</p>";
            }
        }
    } else {
        header("Location: login.php");
        exit();
    }
    mysqli_close($connection);
?>
</html>