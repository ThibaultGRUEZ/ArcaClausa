<?php
session_start();
$host = "localhost"; /* Host name */
$user = "root"; /* User */
$password = "isen2018"; /* Password */
$dbname = "projectlogin"; /* Database name */

$con = mysqli_connect($host, $user, $password,$dbname);
// Check connection
if (!$con) {
 die("Connection failed: " . mysqli_connect_error());
}

