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

    public function section($callback,$forceCommit=false){

        $this->begin();

        try{

            $response=$callback($this);

        }catch(Exception $e){
            if(!$forceCommit){
                $this->rollback();
                throw new Exception($e);
            }
        }

        $this->commit();

        return $response;
        
    }

}