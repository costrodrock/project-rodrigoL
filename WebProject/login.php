<!DOCTYPE html>
<html>

<p>Here are some results:</p>
    <?php
      session_start();
  
      // Redirect user to home.php if already logged in
      if(isset($_SESSION["username"])){
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
      // Display login form
      echo '<form method="post" action="processlogin.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>
            <input type="submit" value="Login">
            </form>';

      mysqli_close($connection);
    ?>
</html>