<?php

namespace Mk2\Orm;

use Exception;

class OrmBase{

    protected const DELETE_FLG="delete_flg";
    protected $context=null;

    public function __construct(&$context){
        $this->context=$context;
    }

    public function query($sql){

        try{

            OrmLog::add($sql);
            
            $res=$this->context->getPdo()->query($sql);
            return $res;
    
        }catch(Exception $e){
            
            throw new Exception($e->getMessage()."\n"."[SQL]:".$sql."\n".$e->getFile()."(".$e->getLine().")");
        }

    }

}