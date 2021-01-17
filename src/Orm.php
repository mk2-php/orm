<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * Copylight : Nakajima Satoru.
 * 
 */

namespace Mk2\Orm;

use Exception;

class Orm{

    public $table;
    public $prefix;

    protected $context=null;
    protected $params=null;
    protected $_pdo=null;
    protected $connection=null;
    public $associated=[];

    private const TYPE_SELECT="select";
    private const TYPE_UPDATE="update";
    private const TYPE_DELETE="delete";
    
    public function __construct($option=null){}

    public function setContext($context){
        $this->context=$context;
    }

    public function setConnection($dbConnection){
        $this->connection=$dbConnection;
    }

    public function getConnection($name=null){
        if($name){
            if(!empty($this->connection[$name])){
                return $this->connection[$name];
            }
        }
        return $this->connection;
    }

    
    public function connectStart(){

        if(empty($this->_pdo)){
            if($this->connection["driver"]=="mysql"){
                $this->_pdo=OrmConnectionMysql::connect($this,$this->connection);
            }
            else if($this->connection["driver"]=="sqlite"){
                $this->_pdo=OrmConnectionSqlite::connect($this,$this->connection);
            }
            else if($this->connection["driver"]=="pgsql"){
                $this->_pdo=OrmConnectionPostgreSql::connect($this,$this->connection);    
            }    

            if(!empty($this->connection["prefix"])){
                $this->prefix=$this->connection["prefix"];
            }
        }

        return true;
    }

    public function sqlLog(){
        return OrmLog::get();
    }

    public function getPdo(){
        return $this->_pdo;
    }

    public function connectCheck(){

        try{

            $this->connectStart();
            return true;

        }catch(Exception $e){
            return false;
        }
    }

    public function tableExists(){

        $this->connectStart();

        $tablename=$this->context->prefix.$this->context->table;

        try{
            $sql="SELECT 1 FROM ".$tablename." LIMIT 1;";
            $res=$this->_pdo->query($sql);

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    public function query($sql){

        $obj=new OrmBase($this);
        return $obj->query($sql);
    }

    public function select($option=null){

        $this->connectStart();

        $obj=new OrmSelect($this);
        
        if($option){
            return $obj->select($option);
        }

        return $obj;
    }
    
    public function show($option=null){
        
        $this->connectStart();

        $obj=new OrmShow($this);
        return $obj;

    }

    public function save($option=null){
        
        $this->connectStart();

        $obj=new OrmSave($this);
        return $obj;
        
    }

    public function insert($option=null){
        
        $this->connectStart();

        $obj=new OrmSave($this);
        return $obj->insert($option);
        
    }

    public function update($option=null){
        
        $this->connectStart();

        $obj=new OrmSave($this);
        return $obj->update($option);
        
    }

    public function delete($option=null){
        
        $this->connectStart();

        $obj=new OrmDelete($this);
        return $obj;
        
    }

    public function transaction($params=null){

        $this->connectStart();

        $obj=new OrmTransaction($this);

        if($params){
            return $obj->section($params);
        }

        return $obj;
    }

    public function migration($option=null){
        
        $this->connectStart();
        
        $obj=new OrmMigration($this);
        return $obj;

    }

    public function hasMany($name,$object=null){

        if(empty($this->associated['hasMany'])){
            $this->associated['hasMany']=[];
        }

        $this->associated['hasMany'][$name]=$object;

    }

    public function hasOne($name,$object=null){

        if(empty($this->associated['hasOne'])){
            $this->associated['hasOne']=[];
        }

        $this->associated['hasOne'][$name]=$object;

    }
    public function belongsTo($name,$object=null){

        if(empty($this->associated['belongsTo'])){
            $this->associated['belongsTo']=[];
        }

        $this->associated['belongsTo'][$name]=$object;

    }
}