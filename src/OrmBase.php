<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * OrmBase Class
 * 
 * Copylight : Nakajima Satoru.
 * 
 */

namespace Mk2\Orm;

use Exception;

class OrmBase{

    protected const DELETE_FLG="delete_flg";
    protected $context=null;

    /**
     * __construct
     * @param &$context
     */
    public function __construct(&$context){
        $this->context=$context;
    }

    /**
     * query
     * @param $sql
     * @param $bindValues = null
     */
    public function query($sq, $bindValues=null){

        try{

            OrmLog::add($sql);
            
            if($bindValues){

                $sth = $this->context->getPdo()->prepare($sql);
                $res = $sth->execute($bindValues);
    
                if($res === null){
                    throw new Exception($sth->errorInfo());
                }

            }
            else{
                $res=$this->context->getPdo()->query($sql);
            }

            return $res;

        }catch(Exception $e){
            
            throw new Exception($e->getMessage()."\n"."[SQL]:".$sql."\n".$e->getFile()."(".$e->getLine().")");
        }

    }

}