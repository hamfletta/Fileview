<?php 
session_start();
var_dump($_SESSION);
include("secure.php");

allowEdit();
?>
