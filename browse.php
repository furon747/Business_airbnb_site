<?php 
session_start(); 
error_reporting(E_ALL);
ini_set('display_errors', '1');

$search_text = "";
$desired_rooms = 0;
$desired_rooms = 0;
$desired_start_date = null;
$desired_end_date = null;
$has_filters = false;
$filter_text = "1 = 1 ";
    $profile_btn_html = "";
    if($_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }

    $connection = mysqli_connect("localhost", "root", "", "webclass");
    $query = "SELECT * FROM SPACES WHERE ";

    if(isset($_POST['filter-btn']) && $_POST['filter-btn']){

        if(!empty($_POST['filter-date-start']) && !empty($_POST['filter-date-end'])){
            $filter_start = $_POST['filter-date-start'];
            $filter_end = $_POST['filter-date-end'];

            $filter_text .= sprintf("AND (frame_1_start >= STR_TO_DATE('%s','%%Y-%%m-%%d') AND frame_1_end <= STR_TO_DATE('%s','%%Y-%%m-%%d')) OR
                (frame_2_start >= STR_TO_DATE('%s','%%Y-%%m-%%d') AND frame_2_end <= STR_TO_DATE('%s','%%Y-%%m-%%d')) OR
                (frame_3_start >= STR_TO_DATE('%s','%%Y-%%m-%%d') AND frame_3_end <= STR_TO_DATE('%s','%%Y-%%m-%%d'))", 
                $filter_start, $filter_end, $filter_start, $filter_end, $filter_start, $filter_end);
        }

        if($_POST['room-filter'] != "-"){
            $filter_text .= " AND rooms >= " . $_POST['room-filter'];
        }

        if($_POST['seat-filter'] != "-"){
            $filter_text .= " AND seats >= " . $_POST['seat-filter'];
        }

        if($_POST['search-field']){
            $filter_text .= " AND lower(name) like '%" . strtolower($_POST['search-field']) . "%'";
        }

    }
    $query .= $filter_text;
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
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>
        <div>
            <form action="browse.php" method="post">
                <label for="search-field">Search: </label>
                <input type="text" name="search-field" id="search-field" value=<?php echo isset($_POST['search-field']) ? $_POST['search-field'] : '' ?>>
                &nbsp; &nbsp;

                <label for="room-filter">Desired Rooms</label>
                <select name="room-filter" id="room-filter">
                    <option value="-" >-</option>
                    <option value="1" <?php echo isset($_POST['room-filter']) && $_POST['room-filter'] == "1" ? "selected" : ""?>>1</option>
                    <option value="2" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "2" ? "selected" : ""?>>2</option>
                    <option value="3" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "3" ? "selected" : ""?>>3</option>
                    <option value="4" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "4" ? "selected" : ""?>>4</option>
                    <option value="5" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "5" ? "selected" : ""?>>5</option>
                    <option value="6" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "6" ? "selected" : ""?>>6</option>
                    <option value="7" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "7" ? "selected" : ""?>>7</option>
                    <option value="8" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "8" ? "selected" : ""?>>8</option>
                    <option value="9" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "9" ? "selected" : ""?>>9</option>
                    <option value="10" <?php echo  isset($_POST['room-filter']) && $_POST['room-filter'] == "10" ? "selected" : ""?>>10</option>
                </select>
                &nbsp; &nbsp;

                <label for="seat-filter">Desired Seats</label>
                <select name="seat-filter" id="seat-filter">
                    <option value="-" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "-" ? "selected" : ""?>>-</option>
                    <option value="1" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "1" ? "selected" : ""?>>1</option>
                    <option value="2" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "2" ? "selected" : ""?>>2</option>
                    <option value="3" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "3" ? "selected" : ""?>>3</option>
                    <option value="4" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "4" ? "selected" : ""?>>4</option>
                    <option value="5" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "5" ? "selected" : ""?>>5</option>
                    <option value="6" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "6" ? "selected" : ""?>>6</option>
                    <option value="7" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "7" ? "selected" : ""?>>7</option>
                    <option value="8" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "8" ? "selected" : ""?>>8</option>
                    <option value="9" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "9" ? "selected" : ""?>>9</option>
                    <option value="10" <?php echo  isset($_POST['room-filter']) && $_POST['seat-filter'] == "10" ? "selected" : ""?>>10</option>
                </select>
                &nbsp; &nbsp;

                <label>Timeframes between</label>&nbsp;
                <input type="date" name="filter-date-start" id= "filter-date-start" value=<?php echo isset($_POST['filter-date-start']) ? $_POST['filter-date-start'] : '' ?>>
                &nbsp; &nbsp;

                <label>and </label>
                &nbsp; &nbsp;
                <input type="date" name="filter-date-end"id= "filter-date-end" value=<?php echo isset($_POST['filter-date-end']) ? $_POST['filter-date-end'] : '' ?>>
                &nbsp; &nbsp;

                <span id="buttons">
                    <label>&nbsp;</label>
                    <input name="filter-btn" type="submit" value="Filter">
                </span>
            </form>
        </div>
        <div id="space_list">
            <?php 
                for($i = 0; $i < mysqli_num_rows($result); $i++) {
                    $counter = 0;           // will need to remember in the future to load ALL images and to just skip over loading them if they're null
                    $row = $result->fetch_assoc();
                    echo("<div class=\"space-block\">");
                    echo("<h2>".$row['name']."</h2>");
                    echo("<div class=\"row\">");
                    for($j = 0; $j < 5; $j++){
                        if($row['Image_'.$j + 1] != null){
                            echo("<div class=\"column\">");
                            echo("<img width = 100px height = 100px id=\"$i-$counter\" src=\"data:image/jpg;charset=utf8;base64,".base64_encode($row['Image_'.$j + 1])."\" onclick=\"myFunction(this, this.id);\" >");
                            echo("</div>");
                        }
                        $counter++;
                    }
                    echo("</div>");

                    echo("<span class=\"container\" style=\"display: inline-block\">");
                        //  <!-- Close the image -->
                       // echo("<span onclick=\"this.parentElement.style.display='none'\" class=\"closebtn\">&times;</span>");

                        //<!-- Expanded image -->
                        echo("<img id=\"expandedImg-$i\" src=\"data:image/jpg;charset=utf8;base64,".base64_encode($row['Image_1'])."\">");
                      
                        //<!-- Image text -->  <!-- Image text -->
                        echo("<div id=\"imgtext\"></div>");
                    echo("</span>");

                    echo("<span class=\"space-details\" style=\"display: inline-block\">");
                    
                    echo("<div style=\"display: inline-block\">");
                    echo("<p><b>Address: </b>".$row['street_address']." ".$row['city'].", ".$row['state']." ".$row['zip_code']."</p><br>");
                    echo("<p><b>Registration dates: </b>".$row['registration_start_date']." to ".$row['registration_end_date']."</p><br>");
                    if($row['frame_1_start'] != null){
                        echo("<p><b>Timeslot #1 start: </b>".$row['frame_1_start']." to ".$row['frame_1_end']."</p><br>");
                    }
                    if($row['frame_2_start'] != null){
                        echo("<p><b>Timeslot #2 start: </b>".$row['frame_2_start']." to ".$row['frame_2_end']."</p><br>");
                    }
                    if($row['frame_3_start'] != null){
                        echo("<p><b>Timeslot #3 start: </b>".$row['frame_3_start']." to ".$row['frame_3_end']."</p><br>");
                    }

                    echo("<p><b>Available rooms: </b>".$row['rooms']."</p><br>");
                    echo("<p><b>Available seats: </b>".$row['seats']."</p><br>");

                    echo("<form action=\"officespace.php\" method=\"post\">
                    <input name=\"view-space-btn\" type=\"submit\" value=\"View Property\"><br>
                    <input name=\"space-id\" type=\"hidden\" value =\"".$row['space_id']."\">
                    </form>");
                    echo("</div>");     // End tag for above div

                    echo("</span>");    // End tag for space-details
                    
                    echo("</div>");     // End tag for space-block
                }
            ?>
        </div>
    </main>
    <script src="main.js"></script>
</body>
</html>