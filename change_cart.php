<?php

session_start();
if (isset($_SESSION['order_id']))
	$id = $_SESSION['order_id'];
else die("CART_EMPTY");

if (isset($_POST['product_id']) && isset($_POST['quantity']))
{
	$pid = $_POST['product_id'];
	$qua = $_POST['quantity'];
}
else die("NO_PARAMS");

$hn = 'mariadb106.server178479.nazwa.pl:3306';
$db = 'server178479_Astreea';
$un = 'server178479_Astreea';
$pw = 'astrea14!X';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("CONN_ERROR");
$pid = $conn->real_escape_string(htmlentities($pid));
$qua = $conn->real_escape_string(htmlentities($qua));

$res = $conn->query("UPDATE orders_temp SET quantity=$qua WHERE id=$id AND product_id='$pid';");

if ($res)
	echo "SUCCESS";
else echo "QUERY_ERROR";

?>