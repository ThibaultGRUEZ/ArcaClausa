<html>
    
    <head>
        <title>Admin Login Page</title>
        <link rel="stylesheet" href="loginstyle.css" type="text/css">
    </head>
    
    <body>
        <div class="container">
            <h1>Admin Login</h1>
                <form method="post" action="login.php">
                    <div id="div_login">
                        <div>
                            <input type="text" class="textbox" id="txt_uname" name="txt_uname" placeholder="Username" />
                        </div>
                        <div>
                            <input type="password" class="textbox" id="txt_pwd" name="txt_pwd" placeholder="Password"/>
                        </div>
                        <div>
                            <input type="submit" value="Submit" name="but_submit" id="but_submit" />
                        </div>
                        <?php
                        include "config.php";
                        //session_start();
                        
                        if(isset($_POST['but_submit'])){

                            $uname = mysqli_real_escape_string($con,$_POST['txt_uname']);
                            $password = mysqli_real_escape_string($con,$_POST['txt_pwd']);

                            if ($uname != "" && $password != ""){

                                $sql_query = "select count(*) as cntUser from admin where username='".$uname."' and password='".$password."'";
                                $result = mysqli_query($con,$sql_query);
                                $row_tocount_admin = mysqli_fetch_array($result);

                                $count = $row_tocount_admin['cntUser'];

                                if($count > 0){
                                    $_SESSION['uname'] = $uname;
                                    header('Location: home.php');
                                }else{
                                    echo "<p id=\"invalid\">Invalid username or password</p>";
                                }

                            }

                        }
                        ?> 
                    </div>
                    
                </form>
        </div>
    </body>
    
</html>


