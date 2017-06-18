<?php
# run this if you want to add first user
$pdo_my = new PDO("mysql:host=127.0.0.1;dbname=db_test;charset=utf8mb4", 'testuser', '');

$uemail   = "jose@localhost.com";
$uname    = "joserizal";
$upass    = "joserizal";

$new_password = password_hash($upass, PASSWORD_DEFAULT);

$stmt = $pdo_my->prepare("INSERT INTO users(username, useremail, userpassword)
    VALUES(:uname, :uemail, :upass)");

$stmt->bindparam(":uemail", $uemail);
$stmt->bindparam(":uname", $uname);
$stmt->bindparam(":upass", $new_password);
$stmt->execute();
echo 'Done!';
?>
