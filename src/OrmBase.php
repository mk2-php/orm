<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-ORM
 *
 * OrmBase
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
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
    public function query($sql, $bindValues=null){

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