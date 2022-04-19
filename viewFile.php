<!-- Author: Wenxin (Hugo) Xue |　Anamika Basu -->
<!-- Email: hugo@wustl.edu -->

<!-- Script allows users to view file in browser or download file -->
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
    
    $file_path = $_SESSION['file_path'];
    $file_name = basename($_SESSION['file_path']);
    //check file name is valid
    if(!preg_match('/^[\w_\.\-]+$/', $file_name) ){
        echo "Invalid filename";
        exit;
    }    

    // slice file path for file extension
    $file_extension = strtolower(substr(strrchr($file_name,"."),1));

    // change content-type for different data type of file 
    if ($file_extension=="txt"){
        header("Content-type: text/html");
        $h = fopen($file_path, "r");
        echo "<ul>\n";
        while( !feof($h) ){
            printf("\t<li>%s</li>\n",
                fgets($h)
            );
        }
        echo "</ul>\n";
        fclose($h);
    }elseif($file_extension=="gif"){
        header("Content-type: image/gif");
        $img = file_get_contents($file_path);
        echo $img;
    }elseif($file_extension=="png"){
        header("Content-type: image/png");
        $img = file_get_contents($file_path);
        echo $img;
    }elseif($file_extension=="jpeg" || $file_extension=="jpg"){
        header("Content-type: image/jpeg");
        $img = file_get_contents($file_path);
        echo $img;
    }elseif($file_extension=="pdf"){
        header("Content-type: application/pdf");
        readfile($file_path);
    }
?>