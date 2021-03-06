<?php

require_once('../include/path.inc');
require_once('../include/get_host_info.inc');
require_once('../include/rabbitMQLib.inc');
require_once('../include/logger.inc');

$client = new rabbitMQClient("../include/queryServer.ini","queryServer");
//echo var_dump($_POST);

//Starter database can be found in SlowPokeBasev2.sql
//import to sql with 'mysql -u <username> -p <database name> < SlowPokeBasev2.sql'

//$request = "This is a test request over queryqueue\n";

//$request["type"] = "pokeSearch";
//$request["type"] = "saveTeam";
//$request["type"] = "userTeams";
//$request["type"] = "userCaught";
//$request["type"] = "addCaught";
//$request["type"] = "editPoke";
//$request["type"] = "teamAnalyze";
//$request["type"] = "bestPoke";
//should be using:
$request["type"] = $_POST["type"];

switch($request["type"]) {
	case "pokeSearch":
		/*
		REQUIRES:
		 	- Game ID - currently either "red-blue-yellow" OR "gold-silver-crystal"
		 	- Name OR Types
		*/

		//$request["gameID"] = "red-blue-yellow";
		//$request["name"] = "bulbasaur";
		//$request["pokeID"] = 1;
		//$request["type1"] = "grass";
		//$request["type2"] = "poison";
		//returns json array of pokemon searched for, use json_decode to parse through array


		$request["gameID"] = $_POST["gameselect"];
		if(isset($_POST["name"])) {
			$request["name"] = $_POST["name"];
		}
		if(isset($_POST["type1"])) {
			$request["type1"] = $_POST["type1"];
			$request["type2"] = $_POST["type2"];
		}

		break;
	case "userTeams":
		/* LISTING ALL USER TEAMS
		REQUIRES:
			- accountID
		*/

		$request["accountID"] = $_POST["acctID"];
		//returns pokemon from each of users saved teams
		break;

	case "userCaught":
		/* LISTING ALL USER CAUGHT POKEMON
		REQUIRES:
			- accountID
		*/

		$request["accountID"] = $_POST["accountID"];

		//returns json of all caught pokemon
		break;
	case "editPoke":
		/* EDITING CAUGHT POKEMON
		REQUIRES:
			- accountID
			- gameID
			- pokemonID
			- level & stats
		*/

		/* //Editing caught pokemon with id 1001 with zeroed stats
		$request["pokemonID"] = 1001;
		$request["level"] = 0;
		$request["hp"] = 0;
		$request["speed"] = 0;
		$request["att"] = 0;
		$request["spAtt"] = 0;
		$request["def"] = 0;
		$request["spDef"] = 0;
		*/
		//returns true or false on udpate success/failure
		break;
	case "tmSearch":
		break;
	case "saveTeam":
		/*
		REQUIRES:
			- accountID
			- teamName
			- gameID
			- array of pokemonIDs
		*/

		/* //SAVING Test Team 1 TO user with accountID 1, gen1, with pokemon 1, 2, 3, 4, 5, 6 
		$request["accountID"] = 1;
		$request["teamName"] = "Test Team 2";
		$request["gameID"] = "red-blue-yellow";
		$request["pokemonIDs"] = array(101, 102, 103, 104, 105, 106);
		*/

		$request["accountID"] = $_POST["accountID"];
		$request["teamName"] = $_POST["teamName"];
		$request["gameID"] = $_POST["gameID"];
		$request["pokemonIDs"] = $_POST["pokemonIDs"];

		//will return true if team inserted and false if error
		break;
	case "generateTeam":
		break;
	case "addCaught":
		/*
		REQUIRES:
			- AccountID of user
			- PokemonID OR Name of original pokemon
			- GameID
			- Custom Level & Stats OR blank Level & Stats
		*/

		/* //ADDING DEFAULT BULBASAUR
		$request["accountID"] = 1;
		$request["gameID"] = "red-blue-yellow";
		$request["name"] = "bulbasaur";
		*/

		/* ADDING CUSTOM BULBASAUR
		$request["accountID"] = 1;
		$request["gameID"] = "red-blue-yellow";
		$request["name"] = "bulbasaur";
		$request["level"] = 100;
		$request["hp"] = 255;
		$request["speed"] = 255;
		$request["att"] = 255;
		$request["spAtt"] = 255;
		$request["def"] = 255;
		$request["spDef"] = 255;
		*/

		$request["accountID"] = $_POST["accountID"];
		$request["gameID"] = $_POST["gameID"];
		$request["name"] = $_POST["name"];
		$request["level"] = $_POST["level"];
		$request["hp"] = $_POST["hp"];
		$request["speed"] = $_POST["speed"];
		$request["att"] = $_POST["att"];
		$request["spAtt"] = $_POST["spAtt"];
		$request["def"] = $_POST["def"];
		$request["spDef"] = $_POST["spDef"];
		$request["sprite"] = $_POST["sprite"];

		//returns true or false on successful/failed insert
		break;
	case "teamAnalyze":
		//$pokemon = array();
		//array_push($pokemon, "rattata");
		//array_push($pokemon, "charmander");
		//array_push($pokemon, "bulbasaur");
		$request["type"] = $_POST["type"];
		$request["pokemon"] = $_POST["pokemon"];
		break;
	case "bestPoke":

		/*
		$request["type"] = "bestPoke";
		$request["pokeType"] = "fire";
		$request["pokeStat"] = "hp";
		*/

		$request["type"] = $_POST["type"];
		$request["pokeType"] = $_POST["pokeType"];
		$request["pokeStat"] = $_POST["pokeStat"];
		break;
	default:
		logger( __FILE__ . " : " . __LINE__ . " :error: " . "Bad Query Type");
		echo "ERROR: BAD QUERY TYPE";
		break;
}
//var_dump($request);
$response = $client->send_request($request);
/*
$responseTest = array();
$responseTest["test"] = "test";
$response = json_encode($responseTest);
*/
echo $response;
/*
$resultArr = json_decode($response);
foreach($resultArr as $res) {
	echo var_dump($res);
	echo $res->team . PHP_EOL;
	foreach($res->pokemon as $pokeData) {
		echo var_dump($pokeData);
		echo $pokeData->name;
		echo $pokeData->level;
		echo $pokeData->type1;
		echo $pokeData->type2;
		echo $pokeData->sprite;
		echo $pokeData->hp;
		echo $pokeData->speed;
		echo $pokeData->att;
		echo $pokeData->spAtt;
		echo $pokeData->def;
		echo $pokeData->spDef;
		echo PHP_EOL;
	}

}*/

/*
HOW TO PARSE THROUGH userTeam json
	- use json decode to use it as a PHP array
	- use foreach to go through each pokemon saved in a user's teams
	- use nested foreach to loop through array of pokemon data
  **** TO PARSE THROUGH pokeSearch items, just use the nested foreach loop
$resultArr = json_decode($response);
foreach($resultArr as $res) {
	//echo var_dump($res);
	echo $res->team . PHP_EOL;
	foreach($res->pokemon as $pokeData) {
		//echo var_dump($pokeData);
		echo $pokeData->name;
		echo $pokeData->level;
		echo $pokeData->type1;
		echo $pokeData->type2;
		echo $pokeData->sprite;
		echo $pokeData->hp;
		echo $pokeData->speed;
		echo $pokeData->att;
		echo $pokeData->spAtt;
		echo $pokeData->def;
		echo $pokeData->spDef;
		echo PHP_EOL;
	}

}
*/

?>
