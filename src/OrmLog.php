<?php

namespace Mk2\Orm;

class OrmLog{

	protected static $log;

	public static function add($sql){
		if(empty(self::$log)){
			self::$log=[];
		}
		self::$log[]=$sql;
	}

	public static function get(){
		return self::$log;
	}

	public static function reset(){
		self::$log=null;
	}
}