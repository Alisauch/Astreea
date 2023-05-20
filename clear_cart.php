<?php

session_start();
if (isset($_SESSION['order_id']))
	$id = $_SESSION['order_id'];
else die("SUCCESS");

$hn = 'mariadb106.server178479.nazwa.pl:3306';
$db = 'server178479_Astreea';
$un = 'server178479_Astreea';
$pw = 'astrea14!X';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("CONN_ERROR");

$res = $conn->query("DELETE FROM orders_temp WHERE id=$id;");

if ($res)
	echo "SUCCESS";
else echo "QUERY_ERROR";

?>