<?php

namespace Mk2\Orm;

class OrmMigration extends OrmBase{

    protected $sqls=[];

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
     * createTable
     * @param string $tableName
     * @param array $tableOption = null
     */
    public function createTable($tableName,$tableOption=null){

        $sql=OrmSqlBuild::convertCreateTable($tableName,$tableOption);

        $this->sqls[]=$sql;

        return $this;
    }

    /**
     * createView
     * @param string $viewName
     * @param array $viewSql = null
     */
    public function createView($viewName,$viewSql){

        $sql=OrmSqlBuild::convertCreateView($viewName,$viewSql);

        $this->sqls[]=$sql;

        return $this;
    }

    public function alterTable($tableName,$fields){


    }

    /**
     * dropTable
     * @param string $tableName
     * @param array $option = null
     */
    public function dropTable($tableName,$option=null){

        $sql=OrmSqlBuild::convertDropTable($tableName,$option);

        $this->sqls[]=$sql;

        return $this;

    }
    
    /**
     * dropView
     * @param string $viewName
     * @param array $option = null
     */
    public function dropView($viewName,$option=null){

        $sql=OrmSqlBuild::convertDropView($viewName,$option);

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

        foreach($this->sqls as $s_){
            $this->query($s_);
        }

    }
}