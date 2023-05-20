<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$order_data = "";

function get_product_price($pid)
{
	$myfile = fopen("./products/$pid.dat", "r") or die("ERROR");
	$name = fgets($myfile);
	$name = fgets($myfile);
	fclose($myfile);
	return $name;
}
function get_product_name($pid)
{
	$myfile = fopen("./products/$pid.dat", "r") or die("ERROR");
	$name = fgets($myfile);
	fclose($myfile);
	return $name;
}

session_start();

$price = 0;
$ord_id = 2005;
for ($i = 2005; $i < 30000000; $i++)
{
	if (!file_exists("./orders/" .$i . ".dat"))
	{
		$ord_id = $i;
		break;
	}
}

if (!isset($_SESSION['order_id'])) die("ERROR");
$id = $_SESSION['order_id'];


$hn = 'mariadb106.server178479.nazwa.pl:3306';
$db = 'server178479_Astreea';
$un = 'server178479_Astreea';
$pw = 'astrea14!X';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("CONN_ERROR");

require_once 'lib/openpayu.php';

OpenPayU_Configuration::setEnvironment('secure');

OpenPayU_Configuration::setMerchantPosId('4291419');
OpenPayU_Configuration::setSignatureKey('24e0e6e93a69cedcd6c65e89aea428ec');

OpenPayU_Configuration::setOauthClientId('4291419');
OpenPayU_Configuration::setOauthClientSecret('1d9e1cdf875e3df0c11a4e7b95ef32e8');  

$myfile = fopen("./orders/" .$ord_id . ".dat", "w+");
fwrite($myfile, "$id");
fclose($myfile);

$order['continueUrl'] = 'https://astreea.pl/';
$order['notifyUrl'] = 'https://astreea.pl/';
$order['customerIp'] = $_SERVER['REMOTE_ADDR'];
$order['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
$order['description'] = 'Zamówienie astreea.pl';
$order['currencyCode'] = 'PLN';

$res = $conn->query("SELECT product_id, quantity FROM orders_temp WHERE id = $id;");

for ($i = 0; $i < $res->num_rows; $i++)
{
	$row = $res->fetch_row();
	$pid = $row[0];
	$qua = $row[1];
	$order['products'][$i]['name'] = get_product_name($pid);
	$order['products'][$i]['unitPrice'] = get_product_price($pid)*100;
	$order['products'][$i]['quantity'] = $qua;
	$order_data .= get_product_name($pid) . "(x" . ($qua) . ")<br>Cena: " . (get_product_price($pid) * $qua)."<br>";
	$price += get_product_price($pid) * $qua;
}

$order['totalAmount'] = $price * 100;
$order['extOrderId'] = $ord_id;

$res2 = $conn->query("SELECT email, phone, first_name, surname, street, city, country, postal_code, shipping_method FROM buyer_details WHERE order_id = $id;");

if ($res2->num_rows != 1) die("ERROR");

$row2 = $res2->fetch_row();

$order['buyer']['email'] = $row2[0];
$ml = $row2[0];
$order['buyer']['phone'] = $row2[1];
$order['buyer']['firstName'] = $row[2];
$order['buyer']['lastName'] = $row2[3];

$street = $row2[4];
$city = $row2[5];
$country = $row2[6];
$postal_code = $row2[7];
$s_method = $row2[8];

$response = OpenPayU_Order::create($order);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/PHPMailer/src/Exception.php';
require './PHPMailer/PHPMailer/src/PHPMailer.php';
require './PHPMailer/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
	$mail->SMTPDebug = 0;
	$mail->isSMTP();
	$mail->Host = 'server178479.nazwa.pl';
	$mail->SMTPAuth = true;
	$mail->Username = 'automat@astreea.pl';
	$mail->Password = 'A[?lw~d*?]67';
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;
	$mail->Encoding = 'base64';
	$mail->CharSet = "UTF-8";

	$mail->setFrom('automat@astreea.pl', 'Astreea.pl');
	$mail->addAddress("$ml", "Twoje zamówienie");

	$mail->isHTML(true);
	$mail->Subject = "Dziękujemy za złożenie zamówienia w naszym sklepie!";
	$mail->Body    = "Dziękujemy za złożenie zamówienia w naszym sklepie. <br> Twoje zamówienie:
	$order_data
	<br>
	Dane do wysyłki: <br>
	$street, $city $postal_code <br>
	$country <br>
	<br>
	Metoda wysyłki: <br>
	$s_method <br>
	";

	$mail->send();
} catch (Exception $e) {
	die('Nie udało się wysłać maila z kodem.');
}

header ('Location:'.$response->getResponse()->redirectUri);

?>