<?php

$serverName = "localhost";
$userName = "admin_user";
$password = "E@wmVadfeMNlfm)VYAA";
$databaseName = "university_management";

$conn = new mysqli($serverName,$userName,$password,$databaseName);

// if ($conn->connect_error) {
//     die("Database Connect Error.".$conn->connect_error);
// }else {
//     echo "Database connected successfully!";
// }