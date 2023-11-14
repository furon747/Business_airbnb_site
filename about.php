<?php 
    session_start(); 
    $profile_btn_html = "";
    if($_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>About</title>
</head>
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
                <li><a href="search.php" >Search</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>
        <h1 class="heading">About Us</h1>
        <div class="heading_desc">
            <p >Welcome to [Company Name]! Our site was created with the aim of allowing those who need temporary working spaces to find a location anywhere they may be!
                Here you can browse for available spaces near your location or near anywhere you choose, and see reviews for the spaces before you reserve a spot for them.
            </p>
        </div>
 
    </main>
    <script src="main.js"></script>
</body>
</html>