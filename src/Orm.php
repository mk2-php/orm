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

    /**
     * setConnection
     * @param $dbConnection
     */
    public function setConnection($dbConnection){
        $this->connection=$dbConnection;
    }

    /**
     * getConnection
     * @param $name = null
     */
    public function getConnection($name=null){
        if($name){
            if(!empty($this->connection[$name])){
                return $this->connection[$name];
            }
        }
        return $this->connection;
    }
    
    /**
     * connectStart
     */
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

    /**
     * sqlLog
     */
    public function sqlLog(){
        return OrmLog::get();
    }

    /**
     * getPdo
     */
    public function getPdo(){
        return $this->_pdo;
    }

    /**
     * connectCheck
     */
    public function connectCheck(){

        try{

            $this->connectStart();
            return true;

        }catch(Exception $e){
            return false;
        }
    }

    /**
     * tableExists
     */
    public function tableExists(){

        try{

            $this->connectStart();

            $tablename=$this->context->prefix.$this->context->table;

            $sql="SELECT 1 FROM ".$tablename." LIMIT 1;";
            $res=$this->_pdo->query($sql);

            return true;
        }catch(Exception $e){
            return false;
        }
    }

    /**
     * query
     * @param $sql
     */
    public function query($sql){

        $obj=new OrmBase($this);
        return $obj->query($sql);
    }

    /**
     * select
     * @param $option = null
     */
    public function select($option=null){

        $this->connectStart();

        $obj=new OrmSelect($this);
        
        if($option){
            return $obj->select($option);
        }

        return $obj;
    }

    /**
     * show
     * @param $option = null
     */
    public function show($option=null){
        
        $this->connectStart();

        $obj=new OrmShow($this);
        return $obj;

    }

    /**
     * save
     * @param $option = null
     * @param boolean $responsed = false
     * @param boolean $changeOnlyRewrite = false
     */
    public function save($option=null,$responsed=false,$changeOnlyRewrite=false){
        
        $this->connectStart();

        $obj=new OrmSave($this);

        if($option){
            return $obj->auto($option,$responsed,$changeOnlyRewrite);
        }

        return $obj;        
    }

    /**
     * insert
     * @param $params
     * @param boolean $insertResponsed = false
     */
    public function insert($params=null,$insertResponsed=false){
        
        $this->connectStart();

        $obj=new OrmSave($this);
        return $obj->insert($params,$insertResponsed);
    }

    /**
     * update
     * @param $params
     * @param boolean $updateResponsed = false
     * @param boolean $changeOnlyRewrite = false
     */
    public function update($params,$updateResponsed=false,$changeOnlyRewrite=false){
        $this->connectStart();

        $obj=new OrmSave($this);
        return $obj->update($params,$updateResponsed,$changeOnlyRewrite);
    }

    /**
     * delete
     * @param $params
     * @param $deleteResponsed = false
     */
    public function delete($params,$deleteResponsed=false){
        
        $this->connectStart();

        $obj=new OrmDelete($this);

        if($params){
            return $obj->surrogateSelect($params)->delete($deleteResponsed);
        }

        return $obj;        
    }

    /**
     * transaction
     * @param $params = null
     */
    public function transaction($params=null){

        $this->connectStart();

        $obj=new OrmTransaction($this);

        if($params){
            return $obj->section($params);
        }

        return $obj;
    }

    /**
     * migration
     * @param $option = null
     */
    public function migration($option=null){
        
        $this->connectStart();
        
        $obj=new OrmMigration($this);
        return $obj;
    }

    /**
     * hasMany
     * @param $name
     * @param $option = null
     */
    public function hasMany($name,$object=null){

        if(empty($this->associated['hasMany'])){
            $this->associated['hasMany']=[];
        }

        $this->associated['hasMany'][$name]=$object;

    }

    /**
     * hasOne
     * @param $name
     * @param $option = null
     */
    public function hasOne($name,$object=null){

        if(empty($this->associated['hasOne'])){
            $this->associated['hasOne']=[];
        }

        $this->associated['hasOne'][$name]=$object;

    }

    /**
     * belongsTo
     * @param $name
     * @param $option = null
     */
    public function belongsTo($name,$object=null){

        if(empty($this->associated['belongsTo'])){
            $this->associated['belongsTo']=[];
        }

        $this->associated['belongsTo'][$name]=$object;

    }

    /**
     * getCallback
     * @param $name
     * @param $request = null
     */
    public function getCallback($name,$request=null){
        if(method_exists($this->context,$name)){
            if($request){
                return $this->context->{$name}(...$request);
            }
            else{
                return $this->context->{$name}();
            }
        }
    }
}