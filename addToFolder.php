<!-- Script adds files to existing folders -->

<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add to Folder</title>
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
            if(!preg_match('/^[\w_\-]+$/', $username) ){
                echo "Invalid username";
                exit;
            }
            //input comes from createFolder.php 
            //if the user tries to create a folder that already exists, the user is asked whether they want to add to existing folder
            if (isset($_POST['addition_input'])) {
                $additionInput = $_POST['addition_input'];
                if ($additionInput == "No") {
                    header("Location: mainPage.php");
                    exit;
                }
            }
            //filtering files from all directory contents because only files can be added to user-created folder
            $dir_contents = scandir($_SESSION['path_to_dir']);
            foreach($dir_contents as $k => $item) {
                if(!is_file($_SESSION['path_to_dir']."/".$item) ){
                    unset($dir_contents[$k]);
                }
            }
            
        ?>
        <h2>Select files to add to folder</h2>
        <script type="text/javascript">
            //passes the PHP array with file names to javascript 
            var passedArray = <?php echo json_encode($dir_contents); ?>;
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "<?php echo htmlentities($_SERVER['PHP_SELF']);?>");

            //dynamically creates number of checkboxes based on number of files present in user's directory
            for (const [key, value] of Object.entries(passedArray)) {
                var checkbox = document.createElement('input');
                checkbox.type = "checkbox";
                checkbox.name = "file" + key;
                checkbox.value = value;
                checkbox.id = "file" + key;
                var label = document.createElement('label')
                label.setAttribute("for", "file" + key);
                label.innerHTML = value;
                form.appendChild(checkbox);
                form.appendChild(label);
                form.appendChild(document.createElement("br"));
            }
            //creates a submit button
            form.appendChild(document.createElement("br"));
            form.appendChild(document.createElement("br"));
            var s = document.createElement("input");
            s.setAttribute("type", "submit");
            s.setAttribute("value", "Submit");
            s.setAttribute("name", "Submit");
            
            form.appendChild(s);
            document.getElementsByTagName("body")[0].appendChild(form);
        </script>
        <?php  
            
            if (isset($_POST["Submit"])) { //checks if Submit has been clicked 
                $folder_path = $_SESSION['folder_path'];
                //POST is a dictionary where the keys are indices and values are files selected, we only want values
                $files_selected = array_values($_POST); 
                foreach($files_selected as $key => $value){
                    rename($_SESSION['path_to_dir']."/".$value, $folder_path."/".$value);
                }
                header("Location: mainPage.php");
                exit;
            }
        ?>
    </body>
</html>