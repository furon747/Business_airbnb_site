<?php 
    session_start(); 
    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    $profile_btn_html = "";
    $error_message = "";
    $general_message = "";
    $connection = mysqli_connect("localhost", "root", "", "webclass");
    $space_id;
    $chosen_seats;
    $chosen_rooms;

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }

    if(isset($_POST['view-space-btn']) || isset($_POST['register-space-btn']))
        $space_id = $_POST['space-id'];


    if( isset($_POST['register-space-btn']) && $_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if((isset($_SESSION['logged_in']) && $_SESSION['logged_in']))
        {
            $chosen_seats = $_POST['seat-amount'];
            $chosen_rooms = $_POST['room-amount'];
            $chosen_slot = $_POST['select-slot'];
            $query = sprintf("SELECT frame_%d_start, frame_%d_end FROM spaces WHERE space_id = %d", $chosen_slot, $chosen_slot, $space_id);
            $result = mysqli_query($connection, $query)->fetch_assoc();

            // get the start and end time and then put it in the insert query and start css
            $chosen_slot_start = $result['frame_'.$chosen_slot.'_start'];
            $chosen_slot_end = $result['frame_'.$chosen_slot.'_end'];

            $query = sprintf("INSERT INTO space_rentals (space_id, seats, rooms, rental_start_date, rental_end_date) VALUES ( %d, %d, %d, STR_TO_DATE('%s','%%Y-%%m-%%d'), STR_TO_DATE('%s','%%Y-%%m-%%d'))",
                $space_id, $chosen_seats, $chosen_rooms, $chosen_slot_start, $chosen_slot_end);
            
            mysqli_query($connection, $query);
            $general_message = "Sucessfully registered!";
        }
        else{
            $error_message .= "You must be logged in to register for a timeslot";
        }
    }
    


        $query = sprintf("SELECT * FROM SPACES WHERE space_id = %s", $space_id); // WHERE REGISTRATION_END_DATE - SYSDATE() > 15 / 1440"        → show everything then allow filters and searching
        $result = mysqli_query($connection, $query);
    

    // Wanna query all the details for this property, display them, and then allow to register & edit if owner. After that point, catch site up with CSS and any loopholes throughout
    // and in general clean everything up
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
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>
        <?php 
        // technically this is fine in a foor loop but we should get rid of it since i will be 1, so it's just a waste
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
                    
                    echo("<div style=\"display: block\">");
                    echo("<p><b>Address: </b>".$row['street_address']." ".$row['city'].", ".$row['state']." ".$row['zip_code']."</p>");
                    echo("<p><b>Registration dates: </b>".$row['registration_start_date']." to ".$row['registration_end_date']."</p>");
                    
                    if($row['frame_1_start'] != null){
                        echo("<p><b>Timeslot #1: </b>".$row['frame_1_start']." to ".$row['frame_1_end']."</p>");
                    }
                    if($row['frame_2_start'] != null){
                        echo("<p><b>Timeslot #2: </b>".$row['frame_2_start']." to ".$row['frame_2_end']."</p>");
                    }
                    if($row['frame_3_start'] != null){
                        echo("<p><b>Timeslot #3: </b>".$row['frame_3_start']." to ".$row['frame_3_end']."</p>");
                    }

                    echo("<p><b>Available rooms: </b>".$row['rooms']."</p>");
                    echo("<p><b>Available seats: </b>".$row['seats']."</p>");

                    // Register form
                    echo("<form id=\"stylized\" action=\"officespace.php\" method=\"post\">");

                    echo("<label for=\"room-amount\">Select # of rooms:</label>
                    <select name=\"room-amount\">");
                    for($j = 0; $j < $row['rooms']; $j++){
                        echo("<option value = \"".($j+1)."\">".($j+1)."</option>");
                    }
                    echo("</select>");

                    echo("<label for=\"seat-amount\">Select # of seats:</label>
                    <select name=\"seat-amount\">");
                    for($j = 0; $j < $row['seats']; $j++){
                        echo("<option value = \"".($j+1)."\">".($j+1)."</option>");
                    }
                    echo("</select><br>");

                    echo("<label for=\"select-slot\">Select desired timeslot:</label>
                    <select name=\"select-slot\">");
                    for($j = 0; $j < 3; $j++){
                        if($j > 0 && $row['frame_'.($j+1).'_start'] != null){
                            echo("<option value = \"".($j+1)."\">".($j+1)."</option>");
                        }
                        else if($j == 0)
                            echo("<option value = \"".($j+1)."\">".($j+1)."</option>");
                    }
                    echo("</select><br>");

                    echo("<input name=\"space-id\" type=\"hidden\" value =\"".$space_id."\">");
                    echo("<input name=\"register-space-btn\" type=\"submit\" value=\"Register\">");
                    
                    echo("</form>");


                    echo("</div>");     // End tag for above div

                    
                    echo("</span>");    // End tag for space-details
                    
                    echo("</div>");     // End tag for space-block

                   
                }
                echo("<p>".$general_message."</p>");
            ?>
            
    </main>
</body>
</html>