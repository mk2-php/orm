<?php

namespace Mk2\Orm;

use Exception;

class OrmTransaction extends OrmBase{

    /**
     * begin
     */
    public function begin(){
        $this->query('BEGIN');
    }

    /**
     * commit
     */
    public function commit(){
        $this->query('COMMIT');
    }

    /**
     * rollback
     */
    public function rollback(){
        $this->query('ROLLBACK');
    }

    /**
     * section
     */
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

    /**
     * save
     */
    public function save($data=null, $responsed=false, $changeOnlyRewrite=false){
        $obj=new OrmSave($this->context);

        if($data){
            return $obj->auto($data,$responsed,$changeOnlyRewrite);
        }

        return $obj;
    }

    /**
     * insert
     * @param $data
     * @param $insertResponsed = false
     */
    public function insert($data, $insertResponsed = false){
        $obj=new OrmSave($this->context);
        return $obj->insert($data,$insertResponsed);
    }

    /**
     * update
     * @param $data
     * @param $updateResponsed = false
     * @param $changeOnlyRewrite = false
     */
    public function update($data, $updateResponsed=false, $changeOnlyRewrite=false){
        $obj=new OrmSave($this->context);
        return $obj->update($data,$updateResponsed,$changeOnlyRewrite);
    }

    /**
     * delete
     * @param $data=null
     * @param $deleteResponsed = false
     */
    public function delete($data=null, $deleteResponsed=false){
        $obj=new OrmDelete($this->context);
        
        if($data){
            return $obj->surrogateSelect($data)->delete($deleteResponsed);
        }

        return $obj;
    }

    /**
     * migration
     */
    public function migration(){
        $obj=new OrmMigration($this->context);
        return $obj;
    }

}