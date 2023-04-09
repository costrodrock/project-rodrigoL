<!DOCTYPE html>
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

	// Get the user ID from the URL
	$userID = $_GET['userID'];

	// Get the post content from the database
	$sql = "SELECT * FROM users WHERE userID = $userID";
	$result = mysqli_query($connection, $sql);

	// Get the user profile image from the database
	$sqlImage = "SELECT contentType, image FROM userImages WHERE userID = $userID";
	$resultImage = mysqli_query($connection, $sqlImage);

	if (mysqli_num_rows($resultImage) > 0) {
	  $row = mysqli_fetch_assoc($resultImage);
	  $contentType = $row['contentType'];
	  $imagedata = $row['image'];
	} else {
	  $contentType = "";
	  $imagedata = null;
	}

        // If a new profile image is uploaded, update the row in the 'userImages' table
        if (isset($_FILES['userImage'])) {
            $target_dir = "src_images/";
            $target_file = $target_dir . basename($_FILES["userImage"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $uploadOk = 1;
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit"])) {
                $check = getimagesize($_FILES["userImage"]["tmp_name"]);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }
            // Check if file already exists
            if (file_exists($target_file)) {
                // Delete existing image record for this user
                $sql = "DELETE FROM userImages WHERE userID=?";
                $stmt = mysqli_stmt_init($connection);
                if (mysqli_stmt_prepare($stmt, $sql)) {
                    mysqli_stmt_bind_param($stmt, "i", $userID);
                    if (mysqli_stmt_execute($stmt)) {
                        mysqli_stmt_close($stmt);
                    } else {
                        echo "Error executing statement: " . mysqli_error($connection);
                        exit();
                    }
                } else {
                    echo "Error preparing statement: " . mysqli_error($connection);
                    exit();
                }
            }
            // Check file size
            if ($_FILES["userImage"]["size"] > 500000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $target_file)) {
                    // If the image file is uploaded successfully, insert a new record into the 'userImages' table with the image data
                    $imagedata = file_get_contents($target_file);
                    $sql = "INSERT INTO userImages (userID, contentType, image) VALUES (?, ?, ?)";
                    $stmt = mysqli_stmt_init($connection);
                    if (mysqli_stmt_prepare($stmt, $sql)) {
                        mysqli_stmt_bind_param($stmt, "iss", $userID, $imageFileType, $imagedata);
                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_close($stmt);
                            $referer = $_SERVER['HTTP_REFERER'];
                            header("Refresh: 0; URL=$referer");
                            exit();
                        } else {
                            echo "Error executing statement: " . mysqli_error($connection);
                        }
                    } else {
                        echo "Error preparing statement: " . mysqli_error($connection);
                    }
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
    

	// Close the connection
	$connection->close();
?>