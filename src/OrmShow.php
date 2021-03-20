<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * OrmShow Class
 * 
 * Copylight : Nakajima Satoru.
 * 
 */

namespace Mk2\Orm;

use PDO;

class OrmShow extends OrmBase{
    
    /**
     * getField 
     */
	public function getField(){
        $sql=OrmSqlBuild::convertGetField($this->context);
        return $this->_query($sql);
    }

	/**
     *  getIndex
     */
	public function getIndex(){
        $sql=OrmSqlBuild::convertGetIndex($this->context);
        return $this->_query($sql);
    }

    /**
     * getDatabases
     */
	public function getDatabases(){
        $sql=OrmSqlBuild::convertGetDatabases($this->context);
        return $this->_query($sql);
    }

    /**
     * getTables
     */
    public function getTables(){
        $sql=OrmSqlBuild::convertGetTables($this->context);
        return $this->_query($sql);
    }

    /**
     * getVariables
     */
	public function getVariables(){
        $sql=OrmSqlBuild::convertGetVariables($this->context);
        return $this->_query($sql);
    }
    
    /**
     * getProcessList
     */
	public function getProcessList(){
        $sql=OrmSqlBuild::convertGetProcessList($this->context);
        return $this->_query($sql);
    }

    /**
     * _query
     * @param string $sql
     */
    private function _query($sql){

        $res=$this->query($sql);

        $output=[];
        while($row=$res->fetch(PDO::FETCH_OBJ)){
            $output[]=$row;
        }

        return $output;
    }
    
}