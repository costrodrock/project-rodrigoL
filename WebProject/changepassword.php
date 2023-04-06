<!DOCTYPE html>
<html>

<p>Here are some results:</p>
<?php

    $host = "localhost";
    $database = "lab9";
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
      if (!isset($_POST["username"]) || !isset($_POST["oldpassword"]) || !isset($_POST["newpassword"])) {
        echo "<p>Missing parameter(s)!</p>";
      } else {
        $username = $_POST["username"];
        $oldpassword = md5($_POST["oldpassword"]);
        $newpassword = md5($_POST["newpassword"]);

        // Check if username and old password combination are valid
        $sql = "SELECT * FROM users WHERE username='$username' AND password='$oldpassword'";
        $result = mysqli_query($connection, $sql);
        if (mysqli_num_rows($result) > 0) {
          // Update user's password in the database
          $sql = "UPDATE users SET password='$newpassword' WHERE username='$username'";
          if (mysqli_query($connection, $sql)) {
            echo "<p>User's password has been updated!</p>";
          } else {
            echo "<p>Error: " . mysqli_error($connection) . "</p>";
          }
        } else {
          // Display error message if username and/or password are incorrect
          echo "<p>Invalid username and/or password!</p>";
        }
      }
    }

    mysqli_close($connection);

?>

</html>
