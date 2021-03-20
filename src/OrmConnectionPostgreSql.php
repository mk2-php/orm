<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * OrmConnectionPostgreSql Class
 * 
 * Copylight : Nakajima Satoru.
 * 
 */

namespace Mk2\Orm;

use PDO;

class OrmConnectionPostgreSql{

	/**
	 * connect
	 * @param $context
	 * @param $dbData
	 */
	public static function connect($context,$dbData){
	
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
	
		$pdoStr="pgsql:";
	
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