<?php

session_start();
if (isset($_SESSION['order_id']))
	$id = $_SESSION['order_id'];
else die("EMPTY_CART");

$hn = 'mariadb106.server178479.nazwa.pl:3306';
$db = 'server178479_Astreea';
$un = 'server178479_Astreea';
$pw = 'astrea14!X';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("CONN_ERROR");

if (isset($_POST['email']) && isset($_POST['phone']) && isset($_POST['first_name']) && isset($_POST['surname']) && isset($_POST['street']) && isset($_POST['city']) && isset($_POST['state']) && isset($_POST['postal_code']) && isset($_POST['country']) && isset($_POST['shipping_method']))
{
	$email = $conn->real_escape_string(htmlentities($_POST['email']));
	$firstname = $conn->real_escape_string(htmlentities($_POST['first_name']));
	$surname = $conn->real_escape_string(htmlentities($_POST['surname']));
	$street = $conn->real_escape_string(htmlentities($_POST['street']));
	$city = $conn->real_escape_string(htmlentities($_POST['city']));
	$state = $conn->real_escape_string(htmlentities($_POST['state']));
	$postal_code = $conn->real_escape_string(htmlentities($_POST['postal_code']));
	$country = $conn->real_escape_string(htmlentities($_POST['country']));
	$shipping_method = $conn->real_escape_string(htmlentities($_POST['shipping_method']));
	$phone = $conn->real_escape_string(htmlentities($_POST['phone']));
}
else die("NO_PARAMS");

$res = $conn->query("SELECT * FROM buyer_details WHERE order_id=$id;");

if ($res->num_rows == 0)
	$res = $conn->query("INSERT INTO buyer_details (order_id,email,first_name,surname,street,city,state,postal_code,country,shipping_method,payment,phone) VALUES ($id,'$email','$firstname','$surname','$street','$city','$state','$postal_code','$country', '$shipping_method', '$payment', '$phone');");
else
	$res = $conn->query("UPDATE buyer_details SET email='$email',first_name='$firstname',surname='$surname',street='$street',city='$city',state='$state',postal_code='$postal_code',country='$country',shipping_method='$shipping_method',payment='$payment',phone='$phone' WHERE order_id=$id;");

if ($res)
	echo "SUCCESS";
else echo "QUERY_ERROR";

?>