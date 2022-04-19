<!-- Script creates page where users can upload files and see previous uploads -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>File Sharing Site</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
</head>
<body>
    <?php  
        //check whether user has logged in 
        if(!isset($_SESSION['user'])) {
            header("Location: login.php");  
            exit;
        }
        // Get the username and make sure it is valid
        $username = $_SESSION['user'];
        if( !preg_match('/^[\w_\-]+$/', $username) ){
            echo "Invalid username";
            exit;
        }

        //create a folder for user if one does not already exist
        $path_to_dir = sprintf("/srv/uploads/%s", $username); 
        $_SESSION['path_to_dir'] = $path_to_dir;
        if (!is_dir($path_to_dir)){
            mkdir($path_to_dir, 0777, true);
        }
    ?>
    <H1>Welcome, <?php echo $username; ?>!</H1>
    <H2>Upload a File</H2>
    <!-- Form to upload file -->
    <form enctype="multipart/form-data" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <p>
            <input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
            <label for="uploadfile_input">Choose a file to upload:</label> <input name="uploadedfile" type="file" id="uploadfile_input" />
        </p>
        <p>
            <input name="fileUploadButton" type="submit" value="Upload File" />
        </p>
    </form>
    <?php
        if (isset($_POST["fileUploadButton"])) {
            // Get the filename and make sure it is valid
            $filename = basename($_FILES['uploadedfile']['name']);
            $filename=preg_replace("/[^a-zA-Z0-9\.]/","",$filename); //removes nonalphanumeric characters from file name                
            $filename=str_replace("_","",$filename); //remove underscores from file name
            $filename=str_replace(" ","",$filename); //remove spaces from file name 
            if( !preg_match('/^[\w_\.\-]+$/', $filename) ){
                echo "Invalid filename";
            } else {
                $full_path = sprintf($path_to_dir."/%s", $filename); 
                if (file_exists($full_path)) {
                    echo "File already exists.";
                } else {
                    //move uploaded file to user's folder
                    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $full_path) ){
                        echo "File upload was a success.";
                    } else{
                        echo "File upload was a failure.";
                    }
                }
                
            }  
        }
    ?>
    <H2>Directory Contents</H2>
    <!-- create a form to ask user if they want to create a folder -->
    <form method="POST" action="createFolder.php">
        <input type="submit" value="Create Folder"/>
    </form>
    <br>
    <br>
    <?php
        if(isset($_GET['msg'])) {
            echo "No files to add to directory.";
        }
        $dir_contents = scandir($path_to_dir); //get user directory contents
        if(count($dir_contents)<=2) { //ignores . and .. 
            echo("No files have been uploaded.<br><br>");
        } else {
            for ($i=2; $i< count($dir_contents); ++$i) { //starts at index 2 to ignore . and .. 
                $path_to_item = $path_to_dir."/".$dir_contents[$i];
                if (is_file($path_to_item)) { //checks if item is a file
                    printf("%s", $dir_contents[$i]); //prints out file name
                    ?>
                    <!-- Buttons for File Actions -->
                    <form action="handleFiles.php" method="POST"> 
                        <input type="hidden" name="file_path" value="<?php echo $path_to_item;?>"/>
                        <input type="submit" name="file_action" value="View"/>
                        <input type="submit" name="file_action" value="Delete"/>
                    </form>
                    <br>
                    <?php echo("</div><br>");
                } else { // item is a folder
                    echo "<p style='font-weight: bold'>".htmlentities($dir_contents[$i])."</p>"; //prints out folder name in bold
                    ?>
                    <!-- Buttons for Folder Actions -->
                    <form action="handleFolders.php" method="POST">
                        <input type="hidden" name="folder_path" value="<?php echo $path_to_item;?>"/>
                        <input type="submit" name="folder_action" value="Add to Folder"/>
                        <input type="submit" name="folder_action" value="Delete"/>
                    </form>
                    <?php echo("</div><br>");
                    $files_in_nested_dir = scandir($path_to_dir."/".$dir_contents[$i]);
                   
                    //lists out files in folder 
                    if(count($files_in_nested_dir)>2){
                        for ($j=2; $j< count($files_in_nested_dir); ++$j) {
                            $path_to_nested_file = $path_to_dir."/".$dir_contents[$i]."/".$files_in_nested_dir[$j];
                            echo "<p style='text-indent: 40px'>".htmlentities($files_in_nested_dir[$j])."</p>"; //prints out file name 
                            ?>
                            <!-- Buttons for File Actions -->
                            <form style="text-indent: 40px" action="handleFiles.php" method="POST">
                                <input type="hidden" name="file_path" value="<?php echo $path_to_nested_file;?>"/>
                                <input type="submit" name="file_action" value="View"/>
                                <input type="submit" name="file_action" value="Move to Main Folder"/>
                            </form>
                            <br>
                        
                    <?php echo("</div><br>");
                        } 
                    }
                }

            } 
        }
    ?>
    <!-- Button to Logout -->
    <form action="logout.php" method="POST">
        <input type="submit" value="Log out" id="logout"/>
    </form>

</body>
</html>