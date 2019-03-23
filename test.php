<?php


$db = mysqli_connect('localhost', 'root', '', 'vtest');

if($db->connect_error){
    die("fail" . $db->connect_error);
}

echo "succ";

?>