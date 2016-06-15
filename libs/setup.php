<?php
include './config.php';


$db = new PDO(
	"mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", 
	$db_user, 
	$db_pass
);
