<?php

namespace Mk2\Orm;

class OrmSave extends OrmBase{

    public function values($data){

        if(empty($this->params["option"])){
            $this->params["option"]=[];
        }

        $this->params["option"][]=[
            "command"=>"values",
            "value"=>$data,
        ];

        return $this;
    }

    public function where($field,$operand,$value){

        if(empty($this->params["option"])){
            $this->params["option"]=[];
        }

        $buff=[
            "command"=>"where",
            "field"=>$field,
            "operand"=>$operand,
            "value"=>$value,
        ];

        $this->params["option"][]=$buff;

        return $this;
    }

    public function insert($data=null,$insertResponsed=false){

        list($sql,$data)=$this->_insertSql($data,$insertResponsed);

        $this->query($sql);

        if($insertResponsed){

            if(!empty($this->context->surrogateKey["enable"])){

                $surrogateKey=$this->context->surrogateKey;

                $suId="id";
                if(!empty($surrogateKey["field"])){
                    $suId=$surrogateKey["field"];
                }

                $selectObj=new OrmSelect($this->context);
                $insertId=$selectObj->max($suId);

                $getInsertData=$selectObj
                    ->where($suId,"=",$insertId->row())
                    ->first()
                ;

                return $getInsertData;
            }
        }

        return true;
    }

    public function insertSql($data){
        list($sql)=$this->_insertSql($data);
        return $sql;
    }

    private function _insertSql($data=null){

        $data=$this->_setCreateColum($data);

        $data=$this->_setUpdateColum($data);

        if(!empty($this->context->logicalDelete)){
            $logicalDeleteField=self::DELETE_FLG;
            if(!empty($this->context->logicalDelete["field"])){
                $logicalDeleteField=$this->context->logicalDelete["field"];
            }

            $stampType=1;
            if(!empty($this->context->logicalDelete["stampType"])){
                $stampType=$this->context->logicalDelete["stampType"];
            }

            if($stampType=="date"){
                $unDeleteStamp=null;
            }
            else{
                $unDeleteStamp=0;
            }

            $data[$logicalDeleteField]=$unDeleteStamp;
        }


        if($data){
            $this->values($data);
        }

        if(empty($this->params["option"])){
            throw new ORMappingException("The content to be inserted is not specified.");
        }
        
        $opt=$this->params["option"];

        $sqls=OrmSqlBuild::convertInsert($opt);

        $tableName=$this->context->prefix.$this->context->table;
        $sql="INSERT INTO `".$tableName."` (".$sqls["fields"].") VALUES (".$sqls["values"].")";
      
        return [$sql,$data];
    }

    public function update($data=null,$updateResponsed=false,$changeOnlyRewrite=false){

        list($sql,$updateKeyValue,$data)=$this->_updateSql($data,$updateResponsed,$changeOnlyRewrite);

        $this->query($sql);

        if($updateResponsed){

            if(!empty($this->context->surrogateKey["enable"])){

                $surrogateKey=$this->context->surrogateKey;

                $suId="id";
                if(!empty($surrogateKey["field"])){
                    $suId=$surrogateKey["field"];
                }

                $selectObj=new OrmSelect($this->context);

                $getUpdateData=$selectObj
                    ->where($suId,"=",$updateKeyValue)
                    ->first()
                ;

                return $getUpdateData;
            }
        }

        return true;
    }

    public function updateSql($data=null,$changeOnlyRewrite=false){
        list($sql)=$this->_updateSql($data,$changeOnlyRewrite);
        return $sql;
    }

    private function _updateSql($data=null,$updateResponsed=false,$changeOnlyRewrite=false){

        $data=$this->_setUpdateColum($data);

        $updateKeyValue=null;
        if(!empty($this->context->surrogateKey["enable"])){

            $surrogateKey=$this->context->surrogateKey;

            $suId="id";
            if(!empty($surrogateKey["field"])){
                $suId=$surrogateKey["field"];
            }

            if(!empty($data[$suId])){
                $this->where($suId,"=",$data[$suId]);
                $updateKeyValue=$data[$suId];
                unset($data[$suId]);
            }
        }

        if($changeOnlyRewrite){
            $select=new OrmSelect($this->context);
            foreach($this->params["option"] as $o_){
                if($o_["command"]=="where"){
                    $select->where($o_["field"],$o_["operand"],$o_["value"]);
                }
            }
            $sql0=$select->sql();
            print_r($sql0);

            /**
             * 
             * 
             * 
             */
        }

        if($data){
            $this->values($data);
        }

        if(empty($this->params["option"])){
            throw new ORMappingException("The content to be updated is not specified.");
        }

        if($updateResponsed){

            if(!empty($this->context->surrogateKey["enable"])){

                $surrogateKey=$this->context->surrogateKey;

                $suId="id";
                if(!empty($surrogateKey["field"])){
                    $suId=$surrogateKey["field"];
                }

                foreach($this->params["option"] as $o_){
                    if($o_["command"]=="where"){
                        if($o_["field"]==$suId && $o_["operand"]=="="){
                            $updateKeyValue=$o_["value"];
                        }
                    }
                }
            }
            
        }

        $opt=$this->params["option"];

        $wheres=OrmSqlBuild::convertWhere($opt);
        $values=OrmSqlBuild::convertUpdate($opt);

        $sql="UPDATE ".$this->context->prefix.$this->context->table." SET ".$values.$wheres;

        return [$sql,$updateKeyValue,$data];
    }

    private function _setCreateColum($data){

        if(!empty($this->context->timeStamp["create"])){

            $createKey="created";
            if(!empty($this->context->timeStamp["create"]["field"])){
                $createKey=$this->context->timeStamp["create"]["field"];
            }

            $createDateFormt="Y-m-d H:i:s";
            if(!empty($this->context->timeStamp["create"]["dateFormat"])){
                $createDateFormt=$this->context->timeStamp["create"]["dateFormat"];
            }

            $data[$createKey]=date_format(date_create("now"),$createDateFormt);
        }

        return $data;
    }
    private function _setUpdateColum($data){

        if(!empty($this->context->timeStamp["update"])){

            $updateKey="updated";

            if(!empty($this->context->timeStamp["update"]["field"])){
                $updateKey=$this->context->timeStamp["update"]["field"];
            }

            $updateDateFormt="Y-m-d H:i:s";
            if(!empty($this->context->timeStamp["update"]["dateFormat"])){
                $updateDateFormt=$this->context->timeStamp["update"]["dateFormat"];
            }

            $data[$updateKey]=date_format(date_create("now"),$updateDateFormt);

        }

        return $data;
    }
}