<!-- Script creates login page for File Sharing Site -->
<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta name="authors" content="Wenxin (Hugo) Xue |　Anamika Basu" />
    <meta name="email" content="hugo@wustl.edu" />
</head>
<body>
    <H1>Login to File Sharing Site</H1>
    <!-- self-submitting POST form that accepts username input  -->
    <form action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
        <p>
            <label for="username">Username: </label>
            <input type="text" name="username" id="username"/>
        </p>
	    <p>
		    <input name="login" type="submit" value="Log In"/>
	    </p>
    </form>
    <?php
        
        if (isset($_POST["login"])) {
            if (isset($_POST["username"]) && !empty($_POST["username"])) {
                //if username is already set as a session variable, move to main page
                $username = $_POST["username"];
                if (isset($_SESSION["user"]) && $_SESSION["user"] == $username) {
                    header("Location: mainPage.php"); 
                    exit;
                //if not, validate username and look through users.txt file to confirm identity
                } else { 
                    //validate username
                    if ( !preg_match('/^[\w_\-]+$/', $username) ){
                        echo "Invalid username. Please use alphanumeric characters.";
                        exit;
                    //look through users.txt file to confirm identity
                    } else {
                        $userExists = FALSE;
                        $users = fopen("/srv/users.txt", "r"); 
                        while( !feof($users) ){
                            if (trim(fgets($users)) == $username) {
                                $userExists = TRUE;
                            }
                        }
                        fclose($users);
                        if ($userExists) {
                            $_SESSION["user"] = $username; //stores user currenntly logged in 
                            header("Location: mainPage.php"); //moves user to main page 
                            exit;
                        }
                        //if username not in users.txt, ask if user wants to create new user 
                        echo "Username not found in system. Would you like to create a new user $username?";
                        ?>
                        <form action="addUser.php" method="POST"> 
                            <p>
                                <input type="hidden" name="newUser" value="<?php echo $username;?>"/>
                                <input name="addUserInput" type="submit" value="Yes"/>
                                <input name="addUserInput" type="submit" value="No"/>
                            </p>
                        </form>
                        <?php 
                        exit;
                    }
                }
            } else {
                echo "No username entered.";
                exit;
            }
        }
        
        
   ?>
</body>
</html>
