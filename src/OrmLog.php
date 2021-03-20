<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * OrmLog Class
 * 
 * Copylight : Nakajima Satoru.
 * 
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