<?php

/**
 * @package Open Tibia Information <otinfo>
 * @version 1.0
 * @author Renato Ribeiro <renatoribeiro@tibiaking.com> with a some lines of Robson Dias
 * @copyright 2014 (C) by Renato Ribeiro <http://tibiaking.com/>
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 * 
 * Official repository: https://github.com/renatorib/otinfo
 *
 */

class Otinfo {

	/**
	 * var connect
	 * bool
	 * verify if socket connect
	 * if false, server offline or doest exists
	 *
	 */
	public $connect;

	/**
	 * @serverinfo
	 *
	 */

	public $host;
	public $uptime;
	public $ip;
	public $servername;
	public $port;
	public $location;
	public $url;
	public $server;
	public $version;
	public $client;
	public $motd;
	public $owner_name;
	public $owner_email;
	public $players_online;
	public $players_max;
	public $players_peak;
	public $monsters;
	public $map_name;
	public $map_author;
	public $map_width;
	public $map_height;
	public $rates_experience;
	public $rates_skill;
	public $rates_magic;
	public $rates_loot;
	public $rates_spawn;

	/**
	 * var cache_check
	 * int
	 * time to recache informations
	 * 1 = 1 second
	 *
	 * var cache
	 * bool
	 * allows cache information
	 *
	 */

	public $cache = true; // highly recommended
	public $cache_check = 120; // two minutes to recache

	
	function __construct($host, $port){

		$this->host = $host;

		$ini = @parse_ini_file("cache" . DIRECTORY_SEPARATOR . "{$host}");
		if(!$this->cache || !isset($ini['updated']) || $ini['updated'] + $this->cache_check < time()) {
			$socket = fsockopen($host, $port, $errno, $errstr, 5);
			if($socket){
				$data = '';
				$this->connect = true;
				fwrite($socket, chr(6).chr(0).chr(255).chr(255).'info');
				while(!feof($socket)) {
					$data .= fgets($socket, 8192);
				}
				fclose($socket);
				$object = simplexml_load_string($data); // simplexml_load_string from php 5
				$information = $this->_parseFromObject($object);
				$cache = '';
				foreach ($information as $index => $value){
					$cache .= "{$index} = \"{$value}\"\n";
				}
				file_put_contents("cache" . DIRECTORY_SEPARATOR . "{$host}", $cache); // cache information
				return @parse_ini_file("cache" . DIRECTORY_SEPARATOR . "{$host}");
			} else {
				$this->connect = false;
				return false;
			}
		} else {
			$this->connect = true;
			//var_dump($ini);
			var_dump($this->_parseFromIni($ini));
			return $this->_parseFromIni($ini);
		}
	}

	private function _parseFromObject($object){
		$o = $object;
		$return = array();

		// Check & Loop @serverinfo node
		if(isset($o->serverinfo)){
			foreach($o->serverinfo->attributes() as $name => $info){
				$this->{$name} = $info;
				$return[$name] = $info;
			}
		}

		// Check & set @motd node
		if(isset($this->motd)){
			$this->motd = $o->motd;
			$return['motd'] = $o->motd;
		}

		// Check & Loop @owner node
		if(isset($o->owner)){
			foreach($o->owner->attributes() as $name => $info){
				$this->owner_{$name} = $info;
				$return['owner_'.$name] = $info;
			}
		}

		// Check & Loop @players node
		if(isset($o->players)){
			foreach($o->players->attributes() as $name => $info){
				$this->players_{$name} = $info;
				$return['players_'.$name] = $info;
			}
		}

		// Check & Set @monsters node
		if(isset($this->monsters)){
			$this->monsters = $o->monsters->attributes()['total'];
			$return['monsters'] = $o->monsters->attributes()['total'];
		}

		// Check & Loop @map node
		if(isset($this->map)){
			foreach($o->map->attributes() as $name => $info){
				$this->map_{$name} = $info;
				$return['map_'.$name] = $info;
			}
		}

		// Check & Loop @rates node
		if(isset($o->rates)){
			foreach($o->rates->attributes() as $name => $info){
				$this->rates_{$name} = $info;
				$return['rates_'.$name] = $info;
			}
		}

		// Update time to cache
		$return['updated'] = time();
		return $return; //return array
	}

	private function _parseFromIni($array){
		foreach ($array as $key => $value) {
			$this->{$key} = $value;
		}
		return $array;
	}

}
