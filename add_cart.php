<?php

session_start();

if (isset($_POST['product_id']) && isset($_POST['quantity']))
{
	$hn = 'mariadb106.server178479.nazwa.pl:3306';
    $db = 'server178479_Astreea';
    $un = 'server178479_Astreea';
    $pw = 'astrea14!X';
    $conn = new mysqli($hn, $un, $pw, $db);
    if ($conn->connect_error) die("CONN_ERROR");
	
	$pid = $conn->real_escape_string(htmlentities($_POST['product_id']));
	$qua = $conn->real_escape_string(htmlentities($_POST['quantity']));
	if (!isset($_SESSION['order_id']))
	{
		$res = $conn->query("INSERT INTO orders_match (x) VALUES (3) RETURNING id;");
		if (!$res) die("QUERY_ERROR");
		if ($res->num_rows == 1)
		{
			$row = $res->fetch_row();
			$_SESSION['order_id'] = $row[0];
			$id = $row[0];
		}
		else die("DB_ERROR");
	}
	else $id = $_SESSION['order_id'];
	$res = $conn->query("SELECT quantity FROM orders_temp WHERE product_id='$pid' AND id=$id;");
	if ($res->num_rows == 1)
	{
		$row = $res->fetch_row();
		$qact = $row[0];
		$qua = $qua + $qact;
		$res = $conn->query("UPDATE orders_temp SET quantity=$qua WHERE product_id='$pid' AND id=$id;");
		if ($res) echo "SUCCESS";
		else die($conn->error);
	}
	else if ($res->num_rows == 0)
	{
		$res = $conn->query("INSERT INTO orders_temp (id, product_id, quantity) VALUES ($id, '$pid', $qua);");
		if ($res) echo "SUCCESS";
		else die("SELECT quantity FROM orders_temp WHERE product_id='$pid' AND id=$id;");
	}
	else die("DB_ERROR");
}

?>