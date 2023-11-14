<?php session_start(); 
    $profile_btn_html = "";
    if($_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }

    if(isset($_POST['logout_btn'])){
        log_out();
    }
    function log_out(){
        $_SESSION['username'] = "";
        $_SESSION["logged_in"] = false;
        $profile_btn_html = "";
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
        <?php echo $profile_btn_html; ?>
        <span>
            <p><?php echo $_SESSION['logged_in'] == true ? "Logged in as ".$_SESSION['username'] : "" ?></p>
        </span>

        <nav id="nav_menu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="#">Browse</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>

        <form action="profile.php" method = "post">

            <div id="buttons">
                <label>&nbsp;</label>
                <input name="logout_btn" type="submit" value="Log out"><br>
            </div>
        </form>
    </main>
</body>
</html>