<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-ORM
 *
 * OrmLog
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
 */

namespace Mk2\Orm;

class OrmLog{

	protected static $log;

	/**
	 * add
	 * @param $sql
	 */
	public static function add($sql){
		if(empty(self::$log)){
			self::$log=[];
		}
		self::$log[]=$sql;
	}

	/**
	 * get
	 */
	public static function get(){
		return self::$log;
	}

	/**
	 * reset
	 * @param $sql
	 */
	public static function reset(){
		self::$log=null;
	}
}