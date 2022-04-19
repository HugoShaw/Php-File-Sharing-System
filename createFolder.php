<!-- Script creates new folders -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Create a folder</title>
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
            //filtering files from all directory contents because only files can be added to user-created folder
            $dir_contents = scandir($_SESSION['path_to_dir']);
            foreach($dir_contents as $k => $item) {
                if(!is_file($_SESSION['path_to_dir']."/".$item) ){
                    unset($dir_contents[$k]);
                }
            }
        
        ?>

        <script type="text/javascript">
            //passes the PHP array with file names to javascript 
            var passedArray = <?php echo json_encode($dir_contents); ?>;
            var form = document.createElement("form");
            form.setAttribute("method", "post");
            form.setAttribute("action", "<?php echo htmlentities($_SERVER['PHP_SELF']);?>");
            //create an input element for folder name
            var instruction1 = document.createElement("h2");
            instruction1.innerHTML = "Select folder name: ";
            var folderName = document.createElement("input");
            folderName.setAttribute("type", "text");
            folderName.setAttribute("name", "folder_name");
            var label = document.createElement('label')
            label.setAttribute("for", "folder_name");
            label.innerHTML = "Folder Name: ";
            form.appendChild(instruction1);
            form.appendChild(label);
            form.appendChild(folderName);
            form.appendChild(document.createElement("br"));
            form.appendChild(document.createElement("br"));
            var instruction2 = document.createElement("h2");
            instruction2.innerHTML = "Select files to add to folder: ";
            form.appendChild(instruction2);
            if (passedArray.length == 0) {
                var message = document.createElement("p");
                message.innerHTML = "No files exist that can be added to folder.";
                form.appendChild(message);
            } else {
                
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
            }
            //create a submit button
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
                if (isset($_POST['folder_name']) && !empty($_POST['folder_name'])) {
                    //get the filename and make sure it is valid
                    $folder_name = $_POST['folder_name'];
                    if( !preg_match('/^[\w_\-]+$/', $folder_name) ){//filter input.
                        echo "Invalid Folder Name";
                        exit;
                    } 
                    //checks whether folder already exists 
                    //if it does, then send user to "add to existing file" page
                    $folder_path = $_SESSION['path_to_dir']."/".$folder_name;
                    if (!is_dir($folder_path)){
                        mkdir($folder_path, 0777, true);
                    } else {
                        echo "Folder with this name already exists.";
                        echo "Would you like to add to this folder?"; 
                        ?>
                        <form action="addToFolder.php" method="POST">
                            <input type="submit" name="addition_input" value="Yes"/>
                            <input type="submit" name="addition_input" value="No"/>
                        </form>
                        <?php echo("</div><br>");
                    }  
                    $files_selected = array_values($_POST);
                    unset($files_selected[0]); //drops the folder name
                    foreach($files_selected as $key => $value){
                        rename($_SESSION['path_to_dir']."/".$value, $folder_path."/".$value);
                    }
                    header('Location: mainPage.php');
                    $_POST = array();
                    exit;
                } else {
                    echo "No Folder Name Given";
                    $_POST = array();
                    exit;
                }
            }
            
        ?>
    </body>
</html>