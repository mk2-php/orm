<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-ORM
 *
 * OrmSelect
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
 */

namespace Mk2\Orm;

use PDO;
use stdClass;

class OrmSelect extends OrmBase{

    private $params=null;
    
    private const OUTPUT_ALL="all";
    private const OUTPUT_FIRST="first";
    private const OUTPUT_ONE="one";
    private const OUTPUT_LIST="list";
    private const OUTPUT_COUNT="count";
    private const OUTPUT_SQL="sql";

    protected $deleteFlgAlso=false;
    protected $deleteFlgOnly=false;
    protected $continue=false;

    /**
     * select
     * @param $option
     */
    public function select($option){

        $option=(array)$option;

        if(!empty($option["where"])){
            foreach($option["where"] as $o_){
                $this->where(...$o_);
            }
        }
        
        if(!empty($option["whereOR"])){
            foreach($option["whereOR"] as $o_){
                $this->whereOR(...$o_);
            }
        }
        
        if(!empty($option["having"])){
            foreach($option["having"] as $h_){
                $this->having(...$h_);
            }
        }
        
        if(!empty($option["havingOR"])){
            foreach($option["havingOR"] as $o_){
                $this->havingOR(...$o_);
            }
        }
        
        if(!empty($option["fields"])){
            $this->fields($option["fields"]);
        }
        
        if(!empty($option["join"])){
            foreach($option["join"] as $o_){
                $this->join(...$o_);
            }
        }
        
        if(!empty($option["leftJoin"])){
            foreach($option["leftJoin"] as $o_){
                $this->leftJoin(...$o_);
            }
        }
        
        if(!empty($option["rightJoin"])){
            foreach($option["rightJoin"] as $o_){
                $this->rightJoin(...$o_);
            }
        }

        if(!empty($option["brige"])){
            foreach($option["brige"] as $o_){
                $this->brige(...$o_);
            }
        }

        if(!empty($option["leftBrige"])){
            foreach($option["leftBrige"] as $o_){
                $this->leftBrige(...$o_);
            }
        }
        
        if(!empty($option["rightBrige"])){
            foreach($option["rightBrige"] as $o_){
                $this->rightBrige(...$o_);
            }
        }
        
        if(!empty($option["limit"])){
            foreach($option["limit"] as $o_){
                $this->limit(...$o_);
            }
        }

        if(!empty($option["paging"])){
            foreach($option["paging"] as $o_){
                $this->paging(...$o_);
            }
        }
            
        if(!empty($option["orderBy"])){
            foreach($option["orderBy"] as $ob_){
               $this->orderBy(...$ob_);
            }
        }

        if(!empty($option["deleteFlgAlso"])){
            $this->deleteFlgAlso($option["deleteFlgAlso"]);
        }

        if(!empty($option["deleteFlgOnly"])){
            $this->deleteFlgOnly($option["deleteFlgOnly"]);
        }

        if(!empty($option["type"])){

            if($option["type"]==self::OUTPUT_ALL){
                return $this->all();
            }
            else if($option["type"]==self::OUTPUT_FIRST){
                return $this->first();
            }
            else if($option["type"]==self::OUTPUT_COUNT){
                return $this->count();
            }
            else if($option["type"]==self::OUTPUT_SQL){
                return $this->sql();
            }
        }

        return $this;

    }

    /**
     * where
     * @param $field
     * @param $operand
     * @param $value
     * @param $conditions = null
     * @param $index = 0
     */
    public function where($field,$operand,$value,$conditions = null,$index = 0){

        $this->_addCommand([
            "command"=>"where",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>$conditions,
            "index"=>$index,
        ]);

        return $this;
    }

    /**
     * whereAnd
     * @param $field
     * @param $operand
     * @param $value
     * @param $index = 0
     */
    public function whereAnd($field,$operand,$value,$index = 0){

        $this->_addCommand([
            "command"=>"where",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>"AND",
            "index"=>$index,
        ]);

        return $this;
    }

    /**
     * whereOr
     * @param $field
     * @param $operand
     * @param $value
     * @param $index = 0
     */
    public function whereOr($field,$operand,$value,$index = 0){

        $this->_addCommand([
            "command"=>"where",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>"OR",
            "index"=>$index,
        ]);

        return $this;
    }

    /**
     * having
     * @param $field
     * @param $operand
     * @param $value
     * @param $conditions = null
     * @param $index = 0
     */
    public function having($field,$operand,$value,$conditions=null ,$index = 0){

        $this->_addCommand([
            "command"=>"having",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>$conditions,
            "index"=>$index,
        ]);

        return $this;
    }

    /**
     * havingAnd
     * @param $field
     * @param $operand
     * @param $value
     * @param $index = 0
     */
    public function havingAnd($field,$operand,$value, $index = 0){

        $this->_addCommand([
            "command"=>"having",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>"AND",
            "index"=>$index,
        ]);

        return $this;

    }

    /**
     * havingOr
     * @param $field
     * @param $operand
     * @param $value
     * @param $index = 0
     */
    public function havingOr($field,$operand,$value, $index = 0){

        $this->_addCommand([
            "command"=>"having",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>"OR",
            "index"=>$index,
        ]);

        return $this;

    }

    /**
     * fields
     * @param $fields
     */
    public function fields($fields){

        if(is_string($fields)){
            $fields=[$fields];
        }

        $this->_addCommand([
            "command"=>"fields",
            "fields"=>$fields,
        ]);
        
        return $this;
    }

    /**
     * join
     * @param $argv
     */
    public function join(...$argv){

        $this->_addCommand([
            "command"=>"join",
            "argv"=>$argv,
        ]);

        return $this;
    }

    /**
     * innerjoin
     * @param $argv
     */
    public function innerjoin(...$argv){

        $this->_addCommand([
            "command"=>"join",
            "argv"=>$argv,
        ]);

        return $this;

    }

    /**
     * leftJoin
     * @param $argv
     */
    public function leftJoin(...$argv){

        $this->_addCommand([
            "command"=>"leftJoin",
            "argv"=>$argv,
        ]);

        return $this;
    }

    /**
     * outerLeftJoin
     * @param $argv
     */
    public function outerLeftJoin(...$argv){

        $this->_addCommand([
            "command"=>"leftJoin",
            "argv"=>$argv,
        ]);

        return $this;

    }

    /**
     * rightJoin
     * @param $argv
     */
    public function rightJoin(...$argv){

        $this->_addCommand([
            "command"=>"rightJoin",
            "argv"=>$argv,
        ]);

        return $this;
    }

    /**
     * outerRightJoin
     * @param $argv
     */
    public function outerRightJoin(...$argv){

        $this->_addCommand([
            "command"=>"rightJoin",
            "argv"=>$argv,
        ]);

        return $this;
        
    }

    /**
     * limit
     * @param $limit
     * @param $offset = 0
     */
    public function limit($limit,$offset=0){

        $this->_addCommand([
            "command"=>"limit",
            "limit"=>$limit,
            "offset"=>$offset,
        ]);

        return $this;
    }

    /**
     * paging
     * @param $limit
     * @param $page = 1
     */
    public function paging($limit,$page=1){
        $offset=($page-1)*$limit;
        return $this->limit($limit,$offset);
    }

    /**
     * orderBy
     * @param $fieldName
     * @param $sort
     */
    public function orderBy($fieldName,$sort){

        $this->_addCommand([
            "command"=>"orderBy",
            "fieldName"=>$fieldName,
            "sort"=>$sort,
        ]);

        return $this;
    }

    /**
     * deleteFlgAlso
     */
    public function deleteFlgAlso(){
        $this->deleteFlgAlso=true;
        return $this;
    }

    /**
     * deleteFlgOn
     */
    public function deleteFlgOn(){
        $this->deleteFlgAlso=true;
        $this->deleteFlgOnly=true;
        return $this;
    }

    /**
     * deleteFlgOn
     */
    public function deleteFlgOff(){
        $this->deleteFlgAlso=false;
        $this->deleteFlgOnly=false;
        return $this;        
    }

    /**
     * continue
     * @param $enable
     */
    public function continue($enable){
        $this->continue=$enable;
        return $this;
    }

    /**
     * get
     * @param string $type
     */
    public function get($type=self::OUTPUT_ALL){

        $this->context->getCallback("selectBefore",[$this]);

        if(!$this->deleteFlgAlso){
        
            if(!empty($this->context->logicalDelete)){
                $logicalDelete=$this->context->logicalDelete;

                $logicalDeleteField=self::DELETE_FLG;
                if(!empty($this->context->logicalDelete["field"])){
                    $logicalDeleteField=$this->context->logicalDelete["field"];
                }

                $stampType=1;
                if(!empty($this->context->logicalDelete["stampType"])){
                    $stampType=$this->context->logicalDelete["stampType"];
                }

                if($stampType=="date"){
                    $stampValue=null;
                }
                else{
                    $stampValue=0;
                }
   
                $this->where($logicalDeleteField,"=",$stampValue);

            }
        }

        if($this->deleteFlgOnly){
            if(!empty($this->context->logicalDelete)){
                $logicalDelete=$this->context->logicalDelete;

                $logicalDeleteField=self::DELETE_FLG;
                if(!empty($this->context->logicalDelete["field"])){
                    $logicalDeleteField=$this->context->logicalDelete["field"];
                }

                $stampType=1;
                if(!empty($this->context->logicalDelete["stampType"])){
                    $stampType=$this->context->logicalDelete["stampType"];
                }

                if($stampType=="date"){
                   $this->where($logicalDeleteField,"IS NOT",null);
                }
                else{
                    $this->where($logicalDeleteField,"=",$stampType);
                }
            }
        }

        if($type==self::OUTPUT_COUNT){
            if(!empty($this->params["option"])){
                foreach($this->params["option"] as $ind=>$p_){
                    if($p_["command"]=="fields"){
                        unset($this->params["option"][$ind]);
                    }
                }    
            }
            $this->fields(["COUNT(*) AS count"]);
        }
        else if($type==self::OUTPUT_ONE){

            foreach($this->params["option"] as $ind=>$p_){
                if($p_["command"]=="fields"){
                    $oneTargetField=$p_["fields"][0];
                    break;
                }
            }

        }
        else if($type==self::OUTPUT_LIST){

            foreach($this->params["option"] as $ind=>$p_){
                if($p_["command"]=="fields"){
                    $listTargetField1=$p_["fields"][0];
                    if(!empty($p_["fields"][1])){
                        $listTargetField2=$p_["fields"][1];
                    }
                    break;
                }
            }

        }

        $opt=null;
        if(!empty($this->params["option"])){
            $opt=$this->params["option"];
        }

        $fields=OrmSqlBuild::convertField($opt);
        $tables=OrmSqlBuild::convertTables($this->context,$opt);
        $wheres=OrmSqlBuild::convertWhere($opt);
        $havings=OrmSqlBuild::convertHaving($opt);
        $orderBys=OrmSqlBuild::convertOrderBy($opt);
        $limits=OrmSqlBuild::convertLimit($opt);

        $sql="SELECT ".$fields." FROM ".$tables.$wheres.$havings.$orderBys.$limits;

        if($type==self::OUTPUT_SQL){
            return $sql;
        }

        $res=$this->query($sql);

        $output=[];
        while($row=$res->fetch(PDO::FETCH_OBJ)){
            if($type==self::OUTPUT_ALL){
                $output[]=$row;
            }
            else if($type==self::OUTPUT_FIRST){
                $output=$row;
            }
            else if($type==self::OUTPUT_ONE){
                $output=$row->{$oneTargetField};
            }
            else if($type==self::OUTPUT_LIST){
                if(!empty($listTargetField2)){
                    $output[$row->{$listTargetField2}]=$row->{$listTargetField1};
                }
                else{
                    $output[]=$row->{$listTargetField1};
                }
            }
            else if($type==self::OUTPUT_COUNT){
                $output=$row->count;
            }
        }

        $output2=$this->context->getCallback("selectAfter",[$type,$output]);
        if($output2){
            $output=$output2;
        }

        $response=new OrmSelectResponse([
            "type"=>$type,
            "item"=>$output,
            "sql"=>$sql,
        ]);

        // associated 
        $response=$this->_associateHasMany($type,$response);
        $response=$this->_associateHasOne($type,$response);
        $response=$this->_associateBelongsTo($type,$response);
        
        // continue
        if(empty($this->continue)){
            $this->params=null;
            $this->deleteFlgAlso=false;
            $this->deleteFlgOnly=false;
        }

        return $response;
    }

    /**
     * all
     */
    public function all(){
        return $this->get();
    }

    /**
     * first
     */
    public function first(){
        return $this->get(self::OUTPUT_FIRST);
    }

    /**
     * one
     * @param $fieldName
     */
    public function one($fieldName){
        if(!empty($this->params["option"])){
            foreach($this->params["option"] as $ind=>$p_){
                if($p_["command"]=="fields"){
                    unset($this->params["option"][$ind]);
                }
            }
        }
        $this->limit(1);
        return $this->fields([$fieldName])
            ->get(self::OUTPUT_ONE)
        ;
    }

    /**
     * value
     * @param $fieldName
     */
    public function value($fieldName){
        return $this->one($fieldName);
    }
    
    /**
     * max
     * @param $fieldName
     */
    public function max($fieldName){
        return $this->one('MAX('.$fieldName.')');
    }

    /**
     * min
     * @param $fieldName
     */
    public function min($fieldName){
        return $this->one('MIN('.$fieldName.')');
    }

    /**
     * sum
     * @param $fieldName
     */
    public function sum($fieldName){
        return $this->one('SUM('.$fieldName.')');
    }

    /**
     * avg
     * @param $fieldName
     */
    public function avg($fieldName){
        return $this->one('AVG('.$fieldName.')');
    }

    /**
     * lists
     * @param $fieldName
     * @param $valueName = null
     */
    public function lists($fieldName,$valueName=null){
        if(!empty($this->params["option"])){
            foreach($this->params["option"] as $ind=>$p_){
                if($p_["command"]=="fields"){
                    unset($this->params["option"][$ind]);
                }
            }    
        }

        if($valueName){
            $this->fields([$fieldName,$valueName]);
        }
        else{
            $this->fields([$fieldName]);
        }
        
        return $this->get(self::OUTPUT_LIST);
    }

    /**
     * count
     */
    public function count(){
        return $this->get(self::OUTPUT_COUNT);
    }

    /**
     * sql
     */
    public function sql(){
        return $this->get(self::OUTPUT_SQL);
    }
    
    /**
     * paginate
     * @param $limit
     * @param $page
     */
    public function paginate($limit,$page){
        
        $paramsBuff=$this->params;
        $this->continue(true);
        $getTotalCount=$this->count();

        $this->params=$paramsBuff;
        $this->paging($limit,$page);
        $buff=$this->get();

        $paging=new stdClass();
        $paging->total=$getTotalCount->row();
        $paging->totalPage=ceil($paging->total/$limit);
        $paging->limit=$limit;
        $paging->page=$page;
        
        $response=new OrmSelectResponse([
            "type"=>"paginate",
            "item"=>$buff->row(),
            "sql"=>$buff->toSql(),
            "paging"=>$paging,
        ]);

        $this->continue(false);

        return $response;

    }

    /**
     * _addCommand
     * @param $params
     */
    private function _addCommand($params){

        if(empty($this->params["option"])){
            $this->params["option"]=[];
        }

        $this->params["option"][]=$params;

    }

    /**
     * _associateHasMany
     * @param $type
     * @param $response
     */
    private function _associateHasMany($type,$response){

        if(empty($this->context->associated['hasMany'])){
            return $response;
        }
        if(empty($this->context->surrogateKey['enable']) || empty($this->context->surrogateKey['field'])){
            return $response;
        }

        $foreignKey=$this->context->prefix.$this->context->table.'_'.$this->context->surrogateKey['field'];
        $surrogateKey=$this->context->surrogateKey['field'];

        $suIdList=[];
        $rows=$response->row();

        if($type==self::OUTPUT_ALL){
            foreach($rows as $r_){
                $suIdList[]=$r_->{$surrogateKey};
            }
        }
        else if($type==self::OUTPUT_FIRST){
            $suIdList[]=$rows->{$surrogateKey};
        }
        
        $buffer=[];
        $bindSql=[];
        foreach($this->context->associated['hasMany'] as $className=>$object){

            if(!empty($object->getForeignKey())){
                $foreignKey = $object->getForeignKey();
            }
            
            $o_=$object->select($object);

            $o_=$o_
                ->where($foreignKey,'IN',$suIdList)
                ->get()
            ;

            $bindRows=$o_->row();
            $bindSql[]=$o_->toSql();

            if($bindRows){
                foreach($bindRows as $r_){
                    if(empty($buffer[$className])){
                        $buffer[$className]=[];
                    }
                    if(empty($buffer[$className][$r_->{$foreignKey}])){
                        $buffer[$className][$r_->{$foreignKey}]=[];
                    }
                    $buffer[$className][$r_->{$foreignKey}][]=$r_;
                }
            }
        }

        if($type==self::OUTPUT_ALL){
            foreach($rows as $index=>$r_){
                foreach($buffer as $className=>$b_){
                    foreach($b_ as $target=>$bb_){
                        if($target==$r_->{$surrogateKey}){
                            $rows[$index]->{$className}=$bb_;
                        }
                    }
                }
            }
        }
        else if($type==self::OUTPUT_FIRST){
            $rows->{$className} = $buffer[$className][$suIdList[0]];
        }

        $responseOut=new OrmSelectResponse([
            "item"=>$rows,
            "sql"=>$response->toSql(),
            'bindSql'=>$bindSql,
        ]);

        $this->context->associated['hasMany']=null;

        return $responseOut;
    }

    /**
     * _associateHasOne
     * @param $type
     * @param $response
     */
    private function _associateHasOne($type,$response){

        if(empty($this->context->associated['hasOne'])){
            return $response;
        }
        if(empty($this->context->surrogateKey['enable']) || empty($this->context->surrogateKey['field'])){
            return $response;
        }

        $foreignKey=$this->context->prefix.$this->context->table.'_'.$this->context->surrogateKey['field'];
        $surrogateKey=$this->context->surrogateKey['field'];

        $suIdList=[];
        $rows=$response->row();

        if($type==self::OUTPUT_ALL){
            foreach($rows as $r_){
                $suIdList[]=$r_->{$surrogateKey};
            }
        }
        else if($type==self::OUTPUT_FIRST){
            $suIdList[]=$rows->{$surrogateKey};
        }
        
        $buffer=[];
        $bindSql=[];
        foreach($this->context->associated['hasOne'] as $className=>$object){

            if(!empty($object->getForeignKey())){
                $foreignKey = $object->getForeignKey();
            }
            
            $o_=$object->select($object);

            $o_=$o_
                ->where($foreignKey,'IN',$suIdList)
                ->get()
            ;

            $bindRows=$o_->row();
            $bindSql[]=$o_->toSql();

            if($bindRows){
                if(empty($buffer[$className])){
                    $buffer[$className]=[];
                }
                foreach($bindRows as $r_){
                    if(empty($buffer[$className][$r_->{$foreignKey}])){
                        $buffer[$className][$r_->{$foreignKey}]=$r_;
                    }
                }
            }
        }

        if($type==self::OUTPUT_ALL){
            foreach($rows as $index=>$r_){
                foreach($buffer as $className=>$b_){
                    foreach($b_ as $target=>$bb_){
                        if($target==$r_->{$surrogateKey}){
                            $rows[$index]->{$className}=$bb_;
                        }
                    }

                }
            }

        }
        else if($type==self::OUTPUT_FIRST){
            $rows->{$className} = $buffer[$className][$suIdList[0]];
        }

        $responseOut=new OrmSelectResponse([
            "item"=>$rows,
            "sql"=>$response->toSql(),
            'bindSql'=>$bindSql,
        ]);

        $this->context->associated['hasOne']=null;

        return $responseOut;
    }

    /**
     * _associateBelongsTo
     * @param $type
     * @param $response
     */
    private function _associateBelongsTo($type,$response){

        if(empty($this->context->associated['belongsTo'])){
            return $response;
        }

        $suIdList=[];
        $rows=$response->row();

        $buffer=[];
        $bindSql=[];
        foreach($this->context->associated['belongsTo'] as $className=>$object){

            if(empty($object->surrogateKey['enable']) || empty($object->surrogateKey['field'])){
                continue;
            }

            $foreignKey=$object->prefix.$object->table.'_'.$object->surrogateKey['field'];
            if(!empty($object->getForeignKey())){
                $foreignKey = $object->getForeignKey();
            }
            $surrogateKey=$object->surrogateKey['field'];
            if($type==self::OUTPUT_ALL){
                if($rows){
                    foreach($rows as $r_){
                        if(!empty($r_->{$foreignKey})){
                            $suIdList[]=$r_->{$foreignKey};
                        }
                    }            
                    $suIdList=array_unique($suIdList);    
                }
            }
            else if($type==self::OUTPUT_FIRST){
                if(!empty($rows->{$foreignKey})){
                    $suIdList[]=$rows->{$foreignKey};
                }
            }

            $o_=$object->select($object);

            $o_=$o_
                ->where($surrogateKey,'IN',$suIdList)
                ->get()
            ;

            $bindRows=$o_->row();
            $bindSql[]=$o_->toSql();

            if($bindRows){
                if(empty($buffer[$className])){
                    $buffer[$className]=[];
                }
                foreach($bindRows as $r_){
                    if(empty($buffer[$className][$r_->{$surrogateKey}])){
                        $buffer[$className][$r_->{$surrogateKey}]=$r_;
                    }
                }
            }
        }

        if($type==self::OUTPUT_ALL){
            if($rows){
                foreach($rows as $index=>$r_){
                    foreach($buffer as $className=>$b_){
                        foreach($b_ as $target=>$bb_){
                            if($target==$r_->{$foreignKey}){
                                $rows[$index]->{$className}=$bb_;
                            }
                        }
    
                    }
                }    
            }
        }
        else if($type==self::OUTPUT_FIRST){

        }

        $responseOut=new OrmSelectResponse([
            "item"=>$rows,
            "sql"=>$response->toSql(),
            'bindSql'=>$bindSql,
        ]);

        $this->context->associated['belongsTo']=null;

        return $responseOut;
    }

}