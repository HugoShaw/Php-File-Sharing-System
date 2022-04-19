<!-- Script allows users to view, delete, or move files back to the main folder -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Handle Files</title>
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
            //get the username and make sure it is valid
            $username = $_SESSION['user'];
            if( !preg_match('/^[\w_\-]+$/', $username) ){
                echo "Invalid username";
                exit;
            }
            //set necessary variables
            $file_action = $_POST['file_action'];
            $file_path = $_POST['file_path'];
            
            //user wants to view file
            if($file_action=="View"){ 
                $_SESSION['file_path'] = $file_path;
                header("Location: viewFile.php");
                exit;
            }
            //user wants to delete file
            elseif($file_action=="Delete") { 
                unlink($file_path);
                header("Location: mainPage.php");
                exit;
            }
            //user cannot delete file from user-created folder, must move file to main folder to delete 
            elseif($file_action=="Move to Main Folder") {
                rename($file_path, $_SESSION['path_to_dir']."/".basename($file_path));
                header("Location: mainPage.php");
                exit;
            }
        ?>
    </body>
</html>