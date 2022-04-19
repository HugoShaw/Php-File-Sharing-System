<!-- Author: Wenxin (Hugo) Xue |　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- Script allows users to add to existing folder or delete existing folder. Note: deleting folder does not delete contents because contents are moved to the main folder. -->
<?php
    session_start();
    
   //check whether user has logged in 
   if(!isset($_SESSION['user'])) {
        header("Location: login.php");  
        exit;
    }
    //get the username and make sure it is valid
    $username = $_SESSION['user'];
    if(!preg_match('/^[\w_\-]+$/', $username) ){
        echo "Invalid username";
        exit;
    }
    //set necessary variables
    $folder_action = $_POST['folder_action'];
    $folder_path = $_POST['folder_path'];
    //user wants to add to existing folder
    if($folder_action=="Add to Folder") {
        //check whether there is anything to add in the first place
        $dir_contents = scandir($_SESSION['path_to_dir']);
        foreach($dir_contents as $k => $item) {
            if(!is_file($_SESSION['path_to_dir']."/".$item) ){
                unset($dir_contents[$k]);
            }
        }
        if (count($dir_contents) > 0) {
            $_SESSION['folder_path'] = $folder_path;
            header("Location: addToFolder.php");
            exit;
        } else { //no files exist to add to user-created folder
            header("Location: mainPage.php?msg");
            exit;
        }
    //user wants to delete existing folder    
    } else if ($folder_action=="Delete"){
        $files_in_folder = scandir($folder_path);
        for ($i=2; $i< count($files_in_folder); ++$i) {
            rename($folder_path."/".$files_in_folder[$i], $_SESSION['path_to_dir']."/".$files_in_folder[$i]);
        }
        rmdir($folder_path);
        header("Location: mainPage.php");
        exit;
    }

    
?>