<?php
include "config.php";
//session_start();

if(isset($_POST["login"]) && isset($_POST["passwd"]) && isset($_POST["sender"]) && isset($_POST["latitude"]) && isset($_POST["longitude"])){
    $sql_recup_lock = "select lock_data from admin where username = \"LStoune\" ";
    $req_lock = mysqli_query($con, $sql_recup_lock) or die('Erreur SQL !<br />'.$sql_recup_lock.'<br />'.mysqli_error());
    $data_lock = mysqli_fetch_array($req_lock);
    if($data_lock['lock_data'] == 0){
        if(htmlspecialchars($_POST["sender"]) == "123"){
            $recup_login = htmlspecialchars($_POST["login"]);
            $recup_password = htmlspecialchars($_POST["passwd"]);
            $recup_lat = htmlspecialchars($_POST["latitude"]);
            $recup_long = htmlspecialchars($_POST["longitude"]);
            //echo "Received 1 : $recup_login and $recup_password"; 

            //Calcul position 
            $sql_recup_curr_pos = "select curr_lat, curr_lg from admin where username = \"LStoune\" ";
            $req_curr_pos = mysqli_query($con, $sql_recup_curr_pos) or die('Erreur SQL !<br />'.$sql_recup_curr_pos.'<br />'.mysqli_error());
            $data_curr_pos = mysqli_fetch_array($req_curr_pos);
            /*while($data_curr_pos = mysqli_fetch_array($req_curr_pos)){
                echo $data_curr_pos['curr_lat']."<br>".$data_curr_pos['curr_lg'];
            }*/
            $diff_long = $recup_long - $data_curr_pos['curr_lg'];
            $diff_lat = $recup_lat - $data_curr_pos['curr_lat'];

            $login = mysqli_real_escape_string($con,$recup_login);
            $passwd = mysqli_real_escape_string($con,$recup_password);

            if ($login != "" && $passwd != ""){
                //if(abs($diff_lat) < 0.0005 && abs($diff_long) < 0.0005){
                    $sql_query = "select count(*) as cntUser from users where idusers='".$login."' and passwd='".$passwd."'";
                    $result = mysqli_query($con,$sql_query);
                    $row_tocount_lp = mysqli_fetch_array($result);

                    $count = $row_tocount_lp['cntUser'];

                    if($count > 0){
                        // Script - Renvoyer "ok"
                        echo "youshallpass"; 
                        //echo "Received 2 : $login and $passwd"; return;

                        // Ajout de l'utilisateur à la BDD "historique"
                        date_default_timezone_set("Europe/Paris");
                        $time = date("Y-m-d H:i:s");
                        $histo_sql = "INSERT INTO historique(time, user) VALUES('".$time."','".$login."')";
                        mysqli_query($con,$histo_sql) or die ('Erreur SQL !'.$histo_sql.'<br />'.mysqli_error());
                        return;
                    }else{
                        echo "login";return;
                    }
                //}else{echo "coord";return;}
            }else {echo "login"; return;}
        }else {echo "Wrong sender";return;}
    }else {echo "lock"; return;}
}else {echo "login";return;}
?>