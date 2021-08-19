<?php

/**
 * ===================================================
 * 
 * PHP Framework "Mk2" - Mk2-ORM
 *
 * OrmDelete
 * 
 * URL : https://www.mk2-php.com/
 * 
 * Copylight : Nakajima-Satoru 2021.
 *           : Sakaguchiya Co. Ltd. (https://www.teastalk.jp/)
 * 
 * ===================================================
 */

namespace Mk2\Orm;

class OrmDelete extends OrmBase{

    /**
     * surrogateSelect
     * @param $ids
     */
    public function surrogateSelect($ids){
        
        if(!empty($this->context->surrogateKey)){
            $surrogateKey=$this->context->surrogateKey;

            $suId="id";
            if(!empty($surrogateKey["field"])){
                $suId=$surrogateKey["field"];
            }
        }
        else{
            return;
        }

        if(!is_array($ids)){
            $ids=[$ids];
        }

        $this->where($suId,"IN",$ids);

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
    public function where($field,$operand,$value, $conditions = null,$index = 0){

        if(empty($this->params["option"])){
            $this->params["option"]=[];
        }

        $buff=[
            "command"=>"where",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
            "conditions"=>$conditions,
            "index"=>$index,
        ];

        $this->params["option"][]=$buff;

        return $this;
    }

    /**
     * delete
     * @param boolean $deleteResponsed = false
     * @param boolean $directDelete = false
     */
    public function delete($deleteResponsed = false,$directDelete = false){

        $this->context->getCallback("deleteBefore");

        list($sql,$deleteKeyValue)=$this->_sql($deleteResponsed,$directDelete);

        if($deleteResponsed){

            if(!empty($this->context->surrogateKey["enable"])){

                if(empty($this->context->logicalDelete)){

                    $surrogateKey=$this->context->surrogateKey;

                    $suId="id";
                    if(!empty($surrogateKey["field"])){
                        $suId=$surrogateKey["field"];
                    }

                    $selectObj=new OrmSelect($this->context);

                    if(is_string($deleteKeyValue)){
                        $deleteKeyValue=[$deleteKeyValue];
                    }

                    $getDeletedData=$selectObj
                        ->where($suId,"IN",$deleteKeyValue)
                        ->get()
                    ;

                }
            }
        }

        $this->query($sql);

        if($deleteResponsed){

            if(!empty($this->context->surrogateKey["enable"])){

                if(!empty($this->context->logicalDelete)){
                                            
                    $surrogateKey=$this->context->surrogateKey;

                    $suId="id";
                    if(!empty($surrogateKey["field"])){
                        $suId=$surrogateKey["field"];
                    }

                    $selectObj=new OrmSelect($this->context);

                    if(is_string($deleteKeyValue)){
                        $deleteKeyValue=[$deleteKeyValue];
                    }

                    $getDeletedData=$selectObj
                        ->where($suId,"IN",$deleteKeyValue)
                        ->deleteFlgOn(true)
                        ->get()
                    ;
                }

            }
        }
        
        if(!empty($getDeletedData)){
            return $getDeletedData;
        }

        return true;
    
    }

    /**
     * sql
     * @param boolean $deleteResponsed = false
     */
    public function sql($deleteResponsed=false){
        list($sql,$deleteKeyValue)=$this->_sql($deleteResponsed,false);
        return $sql;
    }

    /**
     * _sql
     * @param boolean $deleteResponsed
     */
    private function _sql($deleteResponsed,$directDelete = false){

        $opt=$this->params["option"];

        $wheres=OrmSqlBuild::convertWhere($opt);

        if(!empty($this->context->logicalDelete)){

            if(!$directDelete){

                $logicalDeleteField=self::DELETE_FLG;
                if(!empty($this->context->logicalDelete["field"])){
                    $logicalDeleteField=$this->context->logicalDelete["field"];
                }
    
                $stampType=1;
                if(!empty($this->context->logicalDelete["stampType"])){
                    $stampType=$this->context->logicalDelete["stampType"];
                }
    
                if($stampType=="date"){
                    $deleteStamp=date_format(date_create("now"),"Y-m-d H:i:s");
                }
                else if($stampType==1){
                    $deleteStamp=1;
                }
                else{
                    $deleteStamp=$stampType;
                }                
    
                $updateObj=new OrmSave($this->context);
    
                $sql=$updateObj->updateSql([
                    $logicalDeleteField=>$deleteStamp,
                ]);

            }
            else{
                $sql=OrmSqlBuild::convertDelete($this->context,$opt);
            }

        }
        else{
            $sql=OrmSqlBuild::convertDelete($this->context,$opt);
        }

        $deleteKeyValue=null;
        if($deleteResponsed){

            if(!empty($this->context->surrogateKey["enable"])){

                $surrogateKey=$this->context->surrogateKey;

                $suId="id";
                if(!empty($surrogateKey["field"])){
                    $suId=$surrogateKey["field"];
                }
                
                foreach($this->params["option"] as $o_){
                    if($o_["command"]=="where"){
                        if($o_["field"]==$suId && ($o_["operand"]=="=" || $o_["operand"]=="IN")){
                            $deleteKeyValue=$o_["value"];
                        }
                    }
                }
            }
            
        }

        return [$sql.$wheres,$deleteKeyValue];

    }

    /**
     * revert
     * @param bool $responsed = false
     */
    public function revert($Responsed=false){
    	
        $this->context->getCallback("revertBefore");
        
        if(empty($this->context->surrogateKey["enable"])){
            return;
        }

        if(empty($this->context->logicalDelete)){
            return;
        }

        $logicalDeleteField=self::DELETE_FLG;
        if(!empty($this->context->logicalDelete["field"])){
            $logicalDeleteField=$this->context->logicalDelete["field"];
        }

        $stampType=1;
        if(!empty($this->context->logicalDelete["stampType"])){
            $stampType=$this->context->logicalDelete["stampType"];
        }

        if($stampType=="date"){
            $revertStamp=null;
        }
        else{
            $revertStamp=0;
        }

        $data=[
            $logicalDeleteField=>$revertStamp,
        ];


        $obj=new OrmSave($this->context);

        $opt=$this->params["option"];
        foreach($opt as $o_){
            if($o_["command"]=="where"){
                $obj->where($o_["field"],$o_["operand"],$o_["value"]);
            }
        }

        $res=$obj->update($data,$Responsed);

        return $res;

    }

    /**
     * physicalDelete
     * @param bool $Responsed = false
     */
    public function physicalDelete($Responsed=false){
    	
        $this->context->getCallback("physicalDeleteBefore");

        if(empty($this->context->surrogateKey["enable"])){
            return;
        }

        if(empty($this->context->logicalDelete)){
            return;
        }

        $surrogateKey=$this->context->surrogateKey;

        $suId="id";
        if(!empty($surrogateKey["field"])){
            $suId=$surrogateKey["field"];
        }

        $logicalDeleteField=self::DELETE_FLG;
        if(!empty($this->context->logicalDelete["field"])){
            $logicalDeleteField=$this->context->logicalDelete["field"];
        }

        $select=new OrmSelect($this->context);

        $getDeleteList=$select
            ->deleteFlgOn(true)
            ->lists($suId)
            ->row()
        ;

        if(!$getDeleteList){
            return;
        }

        $this->context->logicalDelete=false;

        $res=$this
            ->where($suId,'IN',$getDeleteList)
            ->delete($Responsed)
        ;

        $this->context->logicalDelete=true;

        return $res;

    }
    
}