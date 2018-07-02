<?php
include "config.php";

//session_start();

// Check user login or not
if(!isset($_SESSION['uname'])){
    header('Location: logout.php');
}

// logout
if(isset($_POST['but_logout'])){
    session_destroy();
    header('Location: logout.php');
}

// clean
if(isset($_POST['but_clean'])){
    $sql_clean_record = "truncate table historique";
    $req_clean_record = mysqli_query($con, $sql_clean_record) or die('Erreur SQL !<br />'.$sql_clean_record.'<br />'.mysqli_error());
}

// lock
if(isset($_POST['but_lock'])) {
    //lock true
    $up_lock = "update admin set lock_data = 1 where username=\"LStoune\" ";
    mysqli_query($con, $up_lock) or die ('Erreur SQL !'.$up_lock.'<br />'.mysqli_error()); 
}

//unlock
if(isset($_POST['but_unlock'])) {
    //lock false
    $up_lock = "update admin set lock_data = 0 where username=\"LStoune\" ";
    mysqli_query($con, $up_lock) or die ('Erreur SQL !'.$up_lock.'<br />'.mysqli_error());  
}

?>




<html>
    
    <head>
        <title>Homepage</title>
        <link rel="stylesheet" href="homestyle.css" type="text/css">
    </head>
    
    <body>
        <p id="title">Homepage</p>
        
        <div id="logout">
            <p>Already leaving?</p>
            <form method='post' action="logout.php">
                <input type="submit" value="Logout" name="but_logout" id="but_logout">
            </form>
        </div>
        
        <div id="position">
            <p style="text-align: center; font-size: 150%; font-weight : bold; font-family: \"Georgia\";">Box Location</p>
            <form action="" method="post">
                Latitude : <input type="number" step="any" name="latitude" placeholder="Enter a latitude" style="margin : 5px;">
                <br>
                <br>Longitude : <input type="number" step="any" name="longitude" placeholder="Enter a longitude" style="margin : 5px;">
                <br>
                <br><input type="submit" value="Update" name="envoi" id="but_up">
                
                <div id="display_pos">
                    <p style="font-weight : bold">Actual coordinates</p>
                <?php
                if(isset($_POST['latitude']) && isset($_POST['longitude'])){ // si formulaire soumis
                    $curr_lat = $_POST['latitude'];
                    $curr_long = $_POST['longitude'];
                    $up_db_lat_lg = "update admin set curr_lat = '".$curr_lat."', curr_lg = '".$curr_long."' where username = \"LStoune\" ";
                    mysqli_query($con,$up_db_lat_lg) or die ('Erreur SQL !'.$up_db_lat_lg.'<br />'.mysqli_error());
                    //echo $curr_lat."<br>".$curr_long."<br>";  
                }
                $sql_recup_curr_pos = "select curr_lat, curr_lg from admin where username = \"LStoune\" ";
                $req_curr_pos = mysqli_query($con, $sql_recup_curr_pos) or die('Erreur SQL !<br />'.$sql_recup_curr_pos.'<br />'.mysqli_error());
                while($data_curr_pos = mysqli_fetch_array($req_curr_pos)){
                    echo "Latitude : ".$data_curr_pos['curr_lat']."<br>"."Longitude : ".$data_curr_pos['curr_lg'];
                }
                ?>
                </div>
                
            </form>
        </div>
        
        <div id="lock_syst">
            <form method='post' action="">
                <input type="submit" onclick="alert_lock()" value="Lock the system" name="but_lock" id="but_lock">
                <script>
                function alert_lock() {
                    alert("System locked");
                }
                </script>
            </form>
            <form method='post' action="">
                <input type="submit" onclick="alert_unlock()" value="Unlock the system" name="but_unlock" id="but_unlock">
                <script>
                function alert_unlock() {
                    alert("System unlocked");
                }
                </script>
            </form>
                <!--
                <script type="text/javascript">
                                      
                    function change() 
                    {
                        var x = document.getElementById("but_lock");
                        //elem.innerHTML = "Unlock the system";
                        if (x.innerHTML === "Lock the system"){
                            x.innerHTML = "Unlock the system";
                        }else {x.innerHTML = "Lock the system"};
                    }
                    
                </script>
                -->
                    
        </div>
        
        <div id="clean_record">
            <form method='post' action="">
                <input type="submit" value="Clean the record" name="but_clean" id="but_clean">
            </form>
        </div>
        
        <div id="portrait_container">
            <div id="justinD" class="portrait">
                <strong style="color : white">Pseudo : Cybiniuj</strong>
                <br> Name : Duban 
                <br> Firstname : Justin
                <br> Phone : 03.35.48.64.65
                <br> <a style = "color:white" href=mailto:?to=justin.duban@isen.yncrea.fr>Contact</a>
                <img src="logojustin.jpg" style="width:25%" id="logojustin">
            </div>
            <div id="thibaultG" class="portrait">
                <strong style="color : white">Pseudo : knowDaC#</strong>
                <br> Name : Gruez 
                <br> Firstname : Thibault
                <br> Phone : 06.12.12.12.12
                <br> <a style = "color:white" href=mailto:?to=thibault.gruez@isen.yncrea.fr>Contact</a>
                <img src="logothib.png" style="width:25%" id="logothib">
            </div>
            <div id="louisT" class="portrait">
                <strong style="color : white">Pseudo : Noirock</strong>
                <br> Name : Thiery 
                <br> Firstname : Louis
                <br> Phone : 06.08.20.02.00
                <br> <a style = "color:white" href=mailto:?to=louis.thiery@isen.yncrea.fr>Contact</a>
                <img src="logolouis.jpg" style="width:25%" id="logolouis">
            </div>
            <div id="sixtyneV" class="portrait">
                <strong style="color : white">Pseudo : LStoune</strong>
                <br> Name : Vanhaverbeke 
                <br> Firstname : Sixtyne
                <br> Phone : 07.04.04.16.16
                <br> <a style = "color:white" href=mailto:?to=sixtyne.vanhaverbeke@isen.yncrea.fr>Contact</a>
                <img src="logosix.jpg" style="width: 25%" id="logosix">
            </div>
            <div id="gauthierV" class="portrait">
                <strong style="color : white">Pseudo : Astreides</strong>
                <br> Name : Vroylandt 
                <br> Firstname : Gauthier
                <br> Phone : 06.07.08.09.10
                <br> <a style = "color:white" href=mailto:?to=gauthier.vroylandt@isen.yncrea.fr>Contact</a>
                <img src="logogau.jpg" style="width:25%" id="logogau">
            </div>
        </div>
        
        <div id="contenu">
                        
            <div id="hist">
                <h1>Connexion Record</h1>
                <div id="table">
                <?php
                // DISPLAY DATA FROM HISTORIQUE TABLE
                $data_histo = "select time, user from historique";
                if($con->query($data_histo)){ // Pour enlever l'erreur quand db vide
                    $result_data_histo = $con->query($data_histo);
                    if($result_data_histo->num_rows > 0){
                        while($row_hist = $result_data_histo->fetch_assoc()){
                            echo "Connexion time : ".$row_hist["time"]." - User : ".$row_hist["user"]."<br>";
                        }
                    }else{echo "No data available at the moment";}
                }
                ?>
                </div>
            </div>
            <div id="key_end">
                <img src="end_key.png" alt="" />
            </div>
        </div>

    </body>
    
</html>
