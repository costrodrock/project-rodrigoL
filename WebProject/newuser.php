<!DOCTYPE html>
<html>
<?php
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
      if (!isset($_POST["firstname"]) || !isset($_POST["lastname"]) || !isset($_POST["username"]) || !isset($_POST["email"]) || !isset($_POST["password"])) {
        echo "<p>Missing parameter(s)!</p>";
      } else {
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"];
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = md5($_POST["password"]);

        // Check if user already exists in database
        $sql = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $result = mysqli_query($connection, $sql);
        if (mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          echo "<p>User already exists with username " . $row['username'] . " and email " . $row['email'] . ".</p>";
          echo "<a href='javascript:history.back()'>Go back</a>";
        } else {
          // Insert data into database
          $sql = "INSERT INTO users (firstName, lastName, username, email, password) VALUES ('$firstname', '$lastname', '$username', '$email', '$password')";

          if (mysqli_query($connection, $sql)) {
            echo "<p>User account has been created!</p>";

            // Insert image data into database and get the ID of the inserted user
            $userID = mysqli_insert_id($connection);
            $imageFileType = $_FILES['userImage']['type'];
            $imagedata = file_get_contents($_FILES['userImage']['tmp_name']);

            $sql = "INSERT INTO userImages (userID, contentType, image) VALUES(?,?,?)";
            $stmt = mysqli_stmt_init($connection);
            mysqli_stmt_prepare($stmt, $sql);
            $null = NULL;
            mysqli_stmt_bind_param($stmt, "isb", $userID, $imageFileType, $null);
            mysqli_stmt_send_long_data($stmt, 2, $imagedata);
            $result = mysqli_stmt_execute($stmt) or die(mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);

            // Redirect the user to the page they were before
            $previous_page = $_SERVER['HTTP_REFERER'];
            header("Location: $previous_page");
          } else {
            echo "<p>Error: " . mysqli_error($connection) . "</p>";
          }
        }
      }
    }

    mysqli_free_result($results);
    mysqli_close($connection);
?>

</html>
