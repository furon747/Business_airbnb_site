<?php session_start(); 
    $profile_btn_html = "";
    $add_new_property_html = "";
    $reg_message = "";
    $error_message = "";
    $result = null;
    $img_content = [null, null];
    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']){
        $profile_btn_html .= "<span class=\"reg_btn\">
            <a href= \"profile.php\" >Profile</a>
            </span>";
    }

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] && isset($_POST['new_loc']) && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $connection = mysqli_connect("localhost", "root", "", "webclass");

        $name = $_POST['space_name'];
        $reg_start = $_POST['reg_start'];
        $company = $_POST['company'];               // This is a 10 digit ID field so do we want this to autopopulate or have them choose? Should this be auto-assigned and given to the user when they register?
        $reg_end = $_POST['reg_end'];
        $status = intval($_POST['status']);
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $zip = $_POST['zip'];
        $frame_1_start = $_POST['frame_1_start'];
        $frame_1_end = $_POST['frame_1_end'];
        $frame_2_start = $_POST['frame_2_start'];
        $frame_2_end = $_POST['frame_2_start'];
        $frame_3_start = $_POST['frame_3_start'];
        $frame_3_end = $_POST['frame_3_end'];
        $available_seats = intval($_POST['available_seats']);
        $total_rooms = intval($_POST['total_rooms']);
        $allowTypes = array('jpg','png','jpeg'); 

        if(valid_fields($name, $reg_start, $reg_end, $company, $address, $city, $zip, $frame_1_start, $frame_1_end, $frame_2_start, $frame_2_end, $frame_3_start, $frame_3_end))
        {
            for($i = 0; $i < count($img_content); $i++){
                $n = $i + 1;
                $img_file_name = basename($_FILES["img_".$n]["name"]);
                $img_file_type = pathinfo($img_file_name, PATHINFO_EXTENSION); 
                if(!empty($_FILES["img_".$n]['name']) && in_array($img_file_type, $allowTypes)){
                    $image = $_FILES["img_".$n]['tmp_name'];
                    $img_content[$i] = addslashes(file_get_contents($image));
                }
            }

            $index = 0;
            $query = sprintf("INSERT INTO SPACES (NAME, REGISTRATION_START_DATE, REGISTRATION_END_DATE, STATUS, STREET_ADDRESS, CITY, STATE, ZIP_CODE, FRAME_1_START, FRAME_1_END,
                FRAME_2_START, FRAME_2_END, FRAME_3_START, FRAME_3_END, SEATS, ROOMS, IMAGE_1, IMAGE_2) 
                VALUES('%s', STR_TO_DATE('%s','%%Y-%%m-%%d'), STR_TO_DATE('%s','%%Y-%%m-%%d'), %d, '%s', '%s', '%s', '%s', STR_TO_DATE('%s','%%Y-%%m-%%d'), STR_TO_DATE('%s','%%Y-%%m-%%d'), STR_TO_DATE('%s','%%Y-%%m-%%d'), 
                STR_TO_DATE('%s','%%Y-%%m-%%d'), STR_TO_DATE('%s','%%Y-%%m-%%d'),STR_TO_DATE('%s','%%Y-%%m-%%d'), %d, %d, '%s', '%s' )", 
                $name, $reg_start, $reg_end, $status, $address, $city, $state, $zip, $frame_1_start, $frame_1_end, $frame_2_start, $frame_2_end, $frame_3_start, $frame_3_end,
                $available_seats, $total_rooms, $img_content[$index++], $img_content[$index++]);

            // NOTE: In the past this has inserted each time we refresh, but using $_SERVER['REQUEST_METHOD'] above seems to have fixed that
            mysqli_query($connection, $query);

                $reg_message = "New location added!";

            // this is to stop query on refresh, might need later
            //header("Location: profile.php");
        }
    }

    if(isset($_POST['logout_btn'])){
        log_out();
    }
    function valid_fields($name, $reg_start, $reg_end, $company, $address, $city, $zip, $frame_1_start, $frame_1_end, $frame_2_start, $frame_2_end, $frame_3_start, $frame_3_end){
            
        global $error_message;
        $passing = false;
        $now = new DateTime();

        if(strlen($name) == 0 || strlen($name) >= 31){
             $error_message .= "Invalid name length. Must be between 1 and 31 characters. ";
             $passing = false;
        }

        if(strlen($address) == 0 || strlen($name) >= 31){
            $error_message .= "Invalid address length. Must be between 1 and 31 characters. ";
            $passing = false;
        }

        if(strlen($city) == 0 || strlen($city) >= 31){
            $error_message .= "Invalid city name length. Must be between 1 and 31 characters. ";
            $passing = false;
        }

        if(strlen($company) != 10){
            $error_message .= "Invalid company ID length. Must be 10 characters. ";
            $passing = false;
        }

        if((new DateTime($reg_start))->diff($now)->format("%r%a") > 0){
            $error_message .= "Registration start date must be after today's date. ";
            $passing = false;
        }
        if($reg_end == $reg_start || date_diff(new DateTime($reg_end), $now)->format("%r%a") > 0 || date_diff(new DateTime($reg_start), new DateTime($reg_end))->format("%r%a") <= 0){
            $error_message .= "Registration end date must be after today and after registration start date. ";
            $passing = false;
        }

        if(strlen($zip) == 0 || strlen($zip) < 5){
            $error_message .= "Zip code must be at least 5 digits ";
            $passing = false;
        }

        // Frame start must be after the registration end and the frame start must be before the frame end
        if(date_diff(new DateTime($frame_1_start), new DateTime($frame_1_end))->format("%r%a") <= 0 || 
        date_diff(new DateTime($reg_end), new DateTime($frame_1_start))->format("%r%a") <= 0){
            $passing = false;
            $error_message .= "Invalid range of time for timeframe 1. ";
            $error_message .= date_diff(new DateTime($frame_1_start), new DateTime($reg_end))->format("%r%a");
        }

        if(date_diff(new DateTime($frame_2_start), new DateTime($frame_2_end))->format("%r%a") <= 0 || 
        date_diff(new DateTime($reg_end), new DateTime($frame_2_start))->format("%r%a") <= 0){
            $passing = false;
            $error_message .= "Invalid range of time for timeframe 2. ";
        }

        if(date_diff(new DateTime($frame_3_start), new DateTime($frame_3_end))->format("%r%a") <= 0 || 
        date_diff(new DateTime($reg_end), new DateTime($frame_3_start))->format("%r%a") <= 0){
            $passing = false;
            $error_message .= "Invalid range of time for timeframe 3. ";
        }

        return $passing;
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
            <p><?php echo isset($_SESSION['logged_in']) && $_SESSION['logged_in'] ? "Logged in as ".$_SESSION['username'] : "" ?></p>
        </span>

        <nav id="nav_menu">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="browse.php">Browse</a></li>
                <li><a href="search.php">Search</a></li>
                <li><a href="about.php">About</a></li>
            </ul>
        </nav>
        
        <div class="heading">
            <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) { ?>
                <h2>Add New Office Space</h2>
                <form action="profile.php" method="post" enctype="multipart/form-data">
                <div id="reg_new_space">
                    <label>Space Name</label>
                    <input type="text" name="space_name" value = "<?php echo isset($_POST['space_name']) ? $_POST['space_name'] :"" ?>"><br><br>

                    <label>Company</label>
                    <input type="text" name="company" value = "<?php echo isset($_POST['company']) ? $_POST['company'] :"" ?>"><br><br>

                    <label>Registration Start</label>
                    <input type="date" name="reg_start" value = "<?php echo isset($_POST['reg_start']) ? $_POST['reg_start'] :"" ?>"><br><br>

                    <label>Registration End</label>
                    <input type="date" name="reg_end" value = "<?php echo isset($_POST['reg_end']) ? $_POST['reg_end'] :"" ?>"><br><br>
                    
                    <label for="status">Status</label>
                    <select name="status" id="status">
                        <option value="1" selected= <?php isset($_POST['status']) && $_POST['status'] == 1 ? "selected" : "" ?>>Active</option>
                        <option value="0" selected= <?php isset($_POST['status']) && $_POST['status'] == 0 ? "selected" : "" ?>>Inactive</option>
                    </select><br><br>

                    <label>Address</label>
                    <input type="text" name="address" value = "<?php echo isset($_POST['address']) ? $_POST['address'] :"" ?>"><br><br>

                    <label>City</label>
                    <input type="text" name="city" value = "<?php echo isset($_POST['city']) ? $_POST['city'] :"" ?>"><br><br>


                    <!-- Need to implement JS here to save the correct value when an input field has an error !-->
                    <label for="state">State</label>
                    <select name="state">
                        <option value="AL">AL</option>
                        <option value="AK">AK</option>
                        <option value="AZ">AZ</option>
                        <option value="AR">AR</option>
                        <option value="CA">CA</option>
                        <option value="CO">CO</option>
                        <option value="CT">CT</option>
                        <option value="DE">DE</option>
                        <option value="DC">DC</option>
                        <option value="FL">FL</option>
                        <option value="GA">GA</option>
                        <option value="HI">HI</option>
                        <option value="ID">ID</option>
                        <option value="IL">IL</option>
                        <option value="IN">IN</option>
                        <option value="IA">IA</option>
                        <option value="KS">KS</option>
                        <option value="KY">KY</option>
                        <option value="LA">LA</option>
                        <option value="ME">ME</option>
                        <option value="MD">MD</option>
                        <option value="MA">MA</option>
                        <option value="MI">MI</option>
                        <option value="MN">MN</option>
                        <option value="MS">MS</option>
                        <option value="MO">MO</option>
                        <option value="MT">MT</option>
                        <option value="NE">NE</option>
                        <option value="NV">NV</option>
                        <option value="NH">NH</option>
                        <option value="NJ">NJ</option>
                        <option value="NM">NM</option>
                        <option value="NY">NY</option>
                        <option value="NC">NC</option>
                        <option value="ND">ND</option>
                        <option value="OH">OH</option>
                        <option value="OK">OK</option>
                        <option value="OR">OR</option>
                        <option value="PA">PA</option>
                        <option value="RI">RI</option>
                        <option value="SC">SC</option>
                        <option value="SD">SD</option>
                        <option value="TN">TN</option>
                        <option value="TX">TX</option>
                        <option value="UT">UT</option>
                        <option value="VT">VT</option>
                        <option value="VA">VA</option>
                        <option value="WA">WA</option>
                        <option value="WV">WV</option>
                        <option value="WI">WI</option>
                        <option value="WY">WY</option>
                    </select>

                    <label>Zip:</label>
                    <input type="text" name="zip" value = "<?php echo isset($_POST['zip']) ? $_POST['zip'] :"" ?>"><br><br>

                    <label>Timeframe 1 Start Date</label>
                    <input type="date" name="frame_1_start" value = "<?php echo isset($_POST['frame_1_start']) ? $_POST['frame_1_start'] :"" ?>"><br><br>

                    <label>Timeframe 1 End Date</label>
                    <input type="date" name="frame_1_end" value = "<?php echo isset($_POST['frame_1_end']) ? $_POST['frame_1_end'] :"" ?>"><br><br>

                    <label>Timeframe 2 Start Date</label>
                    <input type="date" name="frame_2_start" value = "<?php echo isset($_POST['frame_2_start']) ? $_POST['frame_2_start'] :"" ?>"><br><br>

                    <label>Timeframe 2 End Date</label>
                    <input type="date" name="frame_2_end" value = "<?php echo isset($_POST['frame_2_end']) ? $_POST['frame_2_end'] :"" ?>"><br><br>

                    <label>Timeframe 3 Start Date</label>
                    <input type="date" name="frame_3_start" value = "<?php echo isset($_POST['frame_3_start']) ? $_POST['frame_3_start'] :"" ?>"><br><br>

                    <label>Timeframe 3 End Date</label>
                    <input type="date" name="frame_3_end" value = "<?php echo isset($_POST['frame_3_end']) ? $_POST['frame_3_end'] :"" ?>"><br><br>

                    <label for="available_seats">Available Seats</label>
                    <select name="available_seats">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>

                    <!-- Short script to retain chosen value !-->
                    <script type="text/javascript">
                        document.getElementById('available_seats').value = "<?php echo $_GET['available_seats'];?>";
                    </script>

                    <label for="total_rooms">Total Rooms</label>
                    <select name="total_rooms">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                    <script type="text/javascript">
                        document.getElementById('total_rooms').value = "<?php echo $_GET['total_rooms'];?>";
                    </script>

                    <label>Image 1</label>
                    <input type="file" name="img_1" value = "<?php echo isset($_POST['img_1']) ? $_POST['img_1'] :"" ?>"><br><br>
                    
                    <label>Image 2</label>
                    <input type="file" name="img_2" value = "<?php echo isset($_POST['img_2']) ? $_POST['img_2'] :"" ?>"><br><br>
                </div>
                <div id="buttons">
                    <label>&nbsp;</label>
                    <input name="new_loc" type="submit" value="Register"><br>
                </div>
            </form>
            <p><?php echo $reg_message; ?></p>
            <p><?php echo $error_message; ?></p>
            <?php }  ?>


   
        </div>

        <form class="heading" action="profile.php" method = "post" enctype="multipart/form-data">
            <div id="buttons">
                <label>&nbsp;</label>
                <input name="logout_btn" type="submit" value="Log out"><br>
            </div>
        </form>
    </main>
</body>
</html>