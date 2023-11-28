<?php session_start(); 
    $profile_btn_html = "";
    if($_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }

    $connection = mysqli_connect("localhost", "root", "", "webclass");
    $query = "SELECT * FROM SPACES WHERE IMAGE_1 IS NOT NULL AND IMAGE_1 <> ''"; // WHERE REGISTRATION_END_DATE - SYSDATE() > 15 / 1440"        → show everything then allow filters and searching
    $result = mysqli_query($connection, $query);

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
                <li><a href="browse.php">Browse</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>
        <div id="space_list">
            <?php 
                for($i = 0; $i < mysqli_num_rows($result); $i++) {
                    $row = $result->fetch_assoc();
                    echo("<h2>".$row['name']."</h2>");
                    echo("<p>".$row['street_address'].", ".$row['city']." ".$row['state']."</p>");
                    echo("<div><img src=\"data:image/jpg;charset=utf8;base64,".base64_encode($row['Image_1'])."\"></div>");
                }
            ?>
        </div>
    </main>
</body>
</html>