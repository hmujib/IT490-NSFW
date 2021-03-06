<?php

//Need autoload file to access PokePHP\PokeApi
require 'vendor/autoload.php';
use PokePHP\PokeApi;

//$api is an object used to reference everything in PokeApi
$api = new PokeApi;

//testing the $api object by referencing cheri berry (see PokePHP documentation)
//using '$api->*(_)' will return a json string, so use json_decode() to transform into an object
//$cheri = json_decode($api->berry(1));
//print($cheri->name);

//offset variable to increment calls to PokeAPI (scrolls through paginated responses)
$offset = 0;

function pokeTypes() {}
function pokeSprite() {}
function pokeStats() {}
function pokeGames() {}
function pokeMoves() {}

while($offset<140){
	print("Pokemon "."$offset".PHP_EOL);
	//makes a call to pokeAPI for 20 pokemon
	$pokes = json_decode($api->resourceList('pokemon', '20', $offset));
	//parsing through each pokemon the above line above returns
	foreach($pokes->results as $poke){
		$name = $poke->name;
		$typetxt = "";
		
		$pokedata = json_decode($api->pokemon($name));
		
		print("------POKEMON: " . $name . "-------" . PHP_EOL);
		
		echo "--------TYPES--------\n";
		foreach($pokedata->types as $type){
			echo $type->type->name . PHP_EOL;

			$typeData = json_decode($api->pokemonType($type->type->name));
			foreach($typeData->damage_relations->double_damage_to as $strength) {
				echo "STRONG TO: " . $strength->name . PHP_EOL;
			}
			foreach($typeData->damage_relations->double_damage_from as $weakness) {
				echo "WEAK TO: " . $weakness->name . PHP_EOL;
			}
		}
		
		echo "--------SPRITE--------\n";
		echo $pokedata->sprites->front_default . PHP_EOL;

		echo "--------STATS--------\n";		
		foreach($pokedata->stats as $pokestat) {
			echo $pokestat->stat->name . " " . $pokestat->base_stat . PHP_EOL;
		}

		echo "--------GAMES--------\n";
		foreach($pokedata->game_indices as $game) {
			echo $game->version->name . PHP_EOL; 		
		}

		echo "--------MOVES LEARNED BY TM/HM--------\n";
		
		foreach($pokedata->moves as $move) {
			$gen1flag = false;
			$gen2flag = false;
			foreach($move->version_group_details as $version) {
				if($version->move_learn_method->name == "machine") {
					switch($version->version_group->name) {
						case 'red-blue':
						case 'yellow':
							if(!$gen1flag){
								echo $move->move->name . " is a machine in: " . $version->version_group->name . " (GEN 1)". PHP_EOL;
								$gen1flag = true;
							}
							break;
						case 'gold-silver':
						case 'crystal':
							if(!$gen2flag){
								echo $move->move->name . " is a machine in: " . $version->version_group->name . " (GEN 2) " . PHP_EOL;
								$gen2flag = true;
							}
							break;
						default:
							break;
					}					
				}			
			}		
		}
	echo "\n\n\n\n";
	}
	$offset += 20;
}


?>
