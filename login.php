<?php

// NOTE: Need to have profile button display after logging in and need to direct them to another page or remove the login fields since they just logged in
    session_start();
    $profile_btn_html = "";
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }
    $reg_message = "";
    $login_message = "";
    $error = false;
    //if not logged in then we want to display a login form. That's todo

    $connection = mysqli_connect("localhost", "root", "", "webclass");

    if(isset($_POST['reg_btn']))
    {        
        if($_SESSION['logged_in']){
            $reg_message = $reg_message . " You are already logged in.";
        }else{
            $username = $_POST['username'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
    
            $query = sprintf("SELECT * FROM users WHERE EMAIL_ADDRESS = '%s'", $email);
            $result = mysqli_query($connection, $query);
    
            if(mysqli_num_rows($result) > 0)
            {
                $reg_message = $reg_message . " You are already a registered user, please login.";
            }
            
            $query = sprintf("SELECT * FROM USERS WHERE USERNAME = '%s'", $username);
            $result = mysqli_query($connection, $query);
            if(mysqli_num_rows($result) > 0){
                $error = true;
                $reg_message = $reg_message .  " This username is already taken, please choose another";
            }
    
            if(!$error)
            {
                //NOTE: NEED TO SET UP AUTO-SEQUENCE FOR USER ID IN USERS TABLES
                // todo: input data validation
                $query = sprintf("INSERT INTO users (USERNAME, PASSWORD, FIRST_NAME, LAST_NAME, EMAIL_ADDRESS) VALUES('%s', '%s', '%s', '%s', '%s')", 
                    $username, $password, $first_name, $last_name, $email);
    
                mysqli_query($connection, $query);
                $reg_message = $reg_message .  " You've sucessfully registered as a user!";
    
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
            }
        }
    }
    else if(isset($_POST['login_btn'])){
        if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
            $error = true;
            $login_message = $login_message . " You are already logged in.";
        }

        if(!$error){
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            $query = sprintf("SELECT * FROM users WHERE USERNAME = '%s' and PASSWORD = '%s'", $username, $password);
            $result = mysqli_query($connection, $query);
            if(mysqli_num_rows($result) > 0){
                $login_message = $login_message . "Successfully logged in as ". $username;
                $_SESSION['username'] = $username;
                $_SESSION['logged_in'] = true;
            }
            else{
                $login_message = "Invalid username or password";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Home</title>
</head>
<script src="main.js"></script>
<body>
    <main>        
        <span class="reg_btn">
            <a href="login.php">Register/Login</a>
        </span>
       <?php echo $profile_btn_html ?>     
        <span>
            <p><?php echo $_SESSION['logged_in'] == true ? "Logged in as ".$_SESSION['username'] : "" ?></p>
        </span>

        <nav id="nav_menu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="browse.php">Browse</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>

    <div class ="heading">
        <h1 >Register</h1>
        <form action="login.php" method="post">
            <div id="register_data">
                <label>First name:</label>
                <input type="text" name="first_name"><br>

                <label>Last name:</label>
                <input type="text" name="last_name"><br>

                <label>Username:</label>
                <input type="text" name="username"><br>

                <label>Email:</label>
                <input type="text" name="email"><br>

                <label>Password:</label>
                <input type="password" name="password"><br>
            </div>
            <div id="buttons">
                <label>&nbsp;</label>
                <input name="reg_btn" type="submit" value="Register"><br>
            </div>
        </form>
        <p><?php echo $reg_message; ?></p>
        <p>-- Or --</p>
    </div>
    <div class ="heading">
        <h1 >Login</h1>
        <form action="login.php" method="post">
            <div id="user_data">
                <label>Username:</label>
                <input type="text" name="username"><br>

                <label>Password:</label>
                <input type="password" name="password"><br>
            </div>
            <div id="buttons">
                <label>&nbsp;</label>
                <input name="login_btn" type="submit" value="Log In"><br>
            </div>
        </form>
        <p><?php echo $login_message; ?></p>
    </div>
    </main>
</body>
</html>