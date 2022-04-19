<!-- Script adds new users -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>New User</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
</head>
<body>
    <?php
        //check if required variables are set 
        if (isset($_POST["addUserInput"]) && isset($_POST["newUser"])) {
            if ($_POST["addUserInput"] == "No") { //user answered no to creating new user 
                echo "A username is required for accessing the file sharing site.";
            
            } else if ($_POST["addUserInput"] == "Yes") { //user answered yes to creating new user 
                if( !preg_match('/^[\w_\-]+$/', $_POST["newUser"]) ){ //sanity check for new username validity
                    echo "Invalid username";
                } 
                $username = $_POST["newUser"];
                //add new username to users.txt
                $users = fopen("/srv/users.txt", 'a'); 
                fwrite($users, "\n".$username);
                fclose($users); 
                echo "<br>"; 
                echo "New user $username has successfully been added!";
            }
        } else {
            echo "Could not create new user.";
        }
    ?>
    <!-- Button to return user to login page -->
    <form action="login.php">
        <input type="submit" value="Return to Login Page"/>
    </form>
</body>
</html>

