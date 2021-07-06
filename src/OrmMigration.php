<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-ORM
 *
 * OrmMigration
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
 */

namespace Mk2\Orm;

class OrmMigration extends OrmBase{

    protected $sqls=[];

    /**
     * sqlRow
     * @param $sql
     */
    public function sqlRow($sql){
        
        $this->sqls[]=$sql;
        return $this;
    }

    /**
     * createDatabase
     * @param string $databaseName
     * @param array $option = null
     */
    public function createDatabase($databaseName,$option=null){

        $sql=OrmSqlBuild::convertCreateDatabase($databaseName,$option);

        $this->sqls[]=$sql;

        return $this;
    }

    /**
     * useDatabase
     * @param $databaseName
     */
    public function useDatabase($databaseName){

        $sql=OrmSqlBuild::convertUseDatabase($databaseName);

        $this->sqls[]=$sql;

        return $this;

    }

    /**
     * createTable
     * @param string $tableName
     * @param array $tableOption = null
     */
    public function createTable($tableName,$tableOption=null){

        $sql=OrmSqlBuild::convertCreateTable($this->context,$tableName,$tableOption);

        $this->sqls[]=$sql;

        return $this;
    }

    /**
     * createView
     * @param string $viewName
     * @param array $viewSql = null
     */
    public function createView($viewName,$viewSql){

        $sql = OrmSqlBuild::convertCreateView($this->context,$viewName,$viewSql);

        $this->sqls[] = $sql;

        return $this;
    }

    /**
     * alterTable
     * @param string $tableName
     * @param array $option
     */
    public function alterTable($tableName,$mode,$option){

        $sql = OrmSqlBuild::convertAlterTable($this->context,$tableName,$mode,$option);

        $this->sqls[] = $sql;
        return $this;
    }

    /**
     * alterTableRename
     * @param $tableName
     * @param $renameTableName
     */
    public function alterTableRename($tableName,$renameTableName){
        
        $opt=[
            "table" => $renameTableName,
        ];

        return $this->alterTable($tableName,"rename",$opt);
    }

    /**
     * alterTableRenameColumn
     * @param $tableName
     * @param $renameColumnName
     */
    public function alterTableRenameColumn($tableName,$columnList){

        $opt=[
            "option" => $columnList,
        ];

        return $this->alterTable($tableName,"rename column",$opt);
    }

    /**
     * alterTableRenameIndex
     * @param $tableName
     * @param $indexList
     */
    public function alterTableRenameIndex($tableName,$indexList){

        $opt=[
            "option" => $indexList,
        ];

        return $this->alterTable($tableName,"rename index",$opt);
    }

    /**
     * alterTableChangeColumn
     * @param string $tableName
     * @param array $columnOption
     */
    public function alterTableChangeColumn($tableName,$columnOption){

        $opt=[
            "option" => $columnOption,
        ];

        return $this->alterTable($tableName,"change",$opt);
    }

    /**
     * alterTableAddColumn
     * @param string $tableName
     * @param array $columnOption
     */
    public function alterTableAddColumn($tableName,$columnOption){

        $opt=[
            "option" => $columnOption,
        ];

        return $this->alterTable($tableName,"add",$opt);
    }

    /**
     * alterTableModifyColumn
     * @param string $tableName
     * @param array $columnOption
     */
    public function alterTableModifyColumn($tableName,$columnOption){

        $opt=[
            "option" => $columnOption,
        ];

        return $this->alterTable($tableName,"modify",$opt);
    }

     /**
     * alterTableDropColumn
     * @param string $tableName
     * @param array $columnList
     */
    public function alterTableDropColumn($tableName,$columnList){

        if(is_string($columnList)){
            $columnList = [$columnList];
        }

        $opt=[
            "option" => $columnList,
        ];

        return $this->alterTable($tableName,"drop",$opt);
    }

    /**
     * dropTable
     * @param string $tableName
     * @param array $option = null
     */
    public function dropTable($tableName,$option=null){

        $sql=OrmSqlBuild::convertDropTable($this->context,$tableName,$option);

        $this->sqls[]=$sql;

        return $this;

    }
    
    /**
     * dropView
     * @param string $viewName
     * @param array $option = null
     */
    public function dropView($viewName,$option=null){

        $sql=OrmSqlBuild::convertDropView($this->context,$viewName,$option);

        $this->sqls[]=$sql;

        return $this;
    }

    /**
     * insert
     * @param $tableName
     * @param $data
     */
    public function insert($tableName,$data){

        $sql = OrmSqlBuild::convertInsertMigration($this->context,$tableName,$data);

        $this->sqls[]=$sql;

        return $this;
    }

    /**
     * sql
     */
    public function sql(){
        return $this->sqls;
    }

    /**
     * run
     */
    public function run(){

        try{

            $this->query("BEGIN");

            foreach($this->sqls as $s_){
                $this->query($s_);
            }
    
            $this->query("COMMIT");

        }catch(\Exception $e){
            $this->query("ROLLBACK");
            throw new \Exception($e);
        }


    }
}