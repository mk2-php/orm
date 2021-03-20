<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * OrmConnectionMysql Class
 * 
 * Copylight : Nakajima Satoru.
 * 
 */

namespace Mk2\Orm;

use PDO;

class OrmConnectionMysql{

	/**
	 * connect
	 * @param $context
	 * @param $dbDat
	 */
	public static function connect($context,$dbData){

		$host="127.0.0.1";
		if(!empty($dbData["host"])){
			$host=$dbData["host"];
		}
	
		$port="";
		if(!empty($dbData["port"])){
			$port=$dbData["port"];
		}
	
		$username="";
		if(!empty($dbData["username"])){
			$username=$dbData["username"];
		}
	
		$password="";
		if(!empty($dbData["password"])){
			$password=$dbData["password"];
		}
	
		$database="";
		if(!empty($dbData["database"])){
			$database=$dbData["database"];
		}
	
		$encoding="utf8";
		if(!empty($dbData["charset"])){
			$encoding=$dbData["charset"];
		}
	
		$prefix="";
		if(!empty($dbData["prefix"])){
			$prefix=$dbData["prefix"];
		}
	
		if(!empty($dbData["option"])){
			$options=$dbData["option"];
		}
	
		$pdoStr="mysql:host=".$host;
		if($port){
			$pdoStr.=":".$port;
		}
	
		if($database){
			$pdoStr.=";dbname=".$database;
		}
	
		if($encoding){
			$pdoStr.=";charset=".$encoding;
		}
	
		$makePdo=new PDO($pdoStr,$username,$password);
	
		if(is_array($options)){
			foreach($options as $field=>$value){
				$makePdo->setAttribute($field,$value);
			}
		}
	
		return $makePdo;
	
	}
}