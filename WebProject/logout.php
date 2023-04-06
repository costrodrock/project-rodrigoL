<!DOCTYPE>
<html>
<?php
    session_start();

    // Check if the user is not logged in
    if (!isset($_SESSION["username"])) {
        header("Location: login.php");
        exit();
    }

    // Clear the session data
    session_unset();
    session_destroy();

    // Redirect back to the referring page
    $referer = $_SERVER["HTTP_REFERER"];
    header("Location: $referer");
    exit();
?>
</html>