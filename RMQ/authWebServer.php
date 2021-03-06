<?php
require_once('../include/path.inc');
require_once('../include/get_host_info.inc');
require_once('../include/rabbitMQLib.inc');
require_once('../include/logger.inc');

$client = new rabbitMQClient("../include/testRabbitMQ.ini","testServer");
//echo "authWebServer: " . PHP_EOL . var_dump($client) . PHP_EOL;

if (isset($argv[1]))
{
  	$msg = $argv[1];
}
else
{
  	$msg = "test message";
}

$request = array();
$request['type'] = $_POST["type"];
$request['username'] = $_POST["email"];
$request['password'] = hash("sha256", $_POST["psw"]);
$request['message'] = $_POST["email"];
//echo var_dump($request);
$response = $client->send_request($request);
//echo var_dump($response);
//$response = $client->publish($request);

//echo "client received response: ".PHP_EOL;
//echo $response["message"] . PHP_EOL;
//echo "\n\n";
//echo var_dump($response);
switch ($response["message"]["type"]) {
	case "login":
		if ($response["message"]["result"]["value"]) {
			/*
			$_SESSION["userID"] = $response["message"]["result"]["user"];
			$_SESSION["acctID"] = $response["message"]["result"]["acct"];
			$_SESSION["email"] = $response["message"]["result"]["email"];
			*/
			setcookie("userID", $response["message"]["result"]["user"], 0, "/");
			setcookie("acctID", $response["message"]["result"]["acct"], 0, "/");
			setcookie("email", $response["message"]["result"]["email"], 0, "/");
			//echo "IN GOOD LOGIN CASE" . PHP_EOL;
			header("Location: /pages/home.html");
		} else {
			echo  "Login error!" . PHP_EOL;
			logger( __FILE__ . " : " . __LINE__ . " :error: " . "Failed Login Attempt");
		}
		break;
	case "register":
		if ($response["message"]["result"]["value"]) {
			/*
			$_SESSION["userID"] = $response["message"]["result"]["user"];
			$_SESSION["acctID"] = $response["message"]["result"]["acct"];
			$_SESSION["email"] = $response["message"]["result"]["email"];
			*/
			setcookie("userID", $response["message"]["result"]["user"], 0, "/");
			setcookie("acctID", $response["message"]["result"]["acct"], 0, "/");
			setcookie("email", $response["message"]["result"]["email"], 0, "/");
			//echo "IN GOOD LOGIN CASE" . PHP_EOL;
			header("Location: /pages/home.html");
		} else {
			echo "Registration error!" . PHP_EOL;
			logger( __FILE__ . " : " . __LINE__ . " :error: " . "Failed Registration Attempt");
		}		
		break;
	default:
		echo "REQUEST ERROR!" . PHP_EOL;
		logger( __FILE__ . " : " . __LINE__ . " :error: " . "Bad Request Type");
}




//echo $argv[0]." END".PHP_EOL;

?>
