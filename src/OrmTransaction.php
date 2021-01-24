<?php

namespace Mk2\Orm;

use Exception;

class OrmTransaction extends OrmBase{

    public function begin(){
        $this->query('BEGIN');
    }

    public function commit(){
        $this->query('COMMIT');
    }

    public function rollback(){
        $this->query('ROLLBACK');
    }

    public function section($callback){

        $this->begin();

        try{

            $response=$callback();

        }catch(Exception $e){
            $this->rollback();
        }

        $this->commit();

        return $response;
        
    }

}