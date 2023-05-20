<?php
function get_product_price($pid)
{
	session_start();
	$myfile = fopen("./products/$pid.dat", "r") or die("ERROR");
	$name = fgets($myfile);
	$name = fgets($myfile);
	fclose($myfile);
	return $name;
}
function get_product_name($pid)
{
	session_start();
	$myfile = fopen("./products/$pid.dat", "r") or die("ERROR");
	$name = fgets($myfile);
	fclose($myfile);
	return $name;
}

?>