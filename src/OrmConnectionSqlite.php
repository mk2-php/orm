<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-ORM
 *
 * OrmConnectionSqlite
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
 */

namespace Mk2\Orm;

use PDO;

class OrmConnectionSqlite{

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
	
		$pdoStr="sqlite";
	
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