<?php

namespace Mk2\Orm;

class OrmSqlBuild{

    public static function convertField($option){

        $sql="";
        $ind=0;
        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]=="fields"){
                    foreach($o_["fields"] as $f_){
                         if($ind){
                            $sql.=",";           
                        }
                        $sql.=$f_;
                        $ind++;
                    }
                }
            }    
        }

        if(!$sql){
            $sql="*";
        }

        return $sql;

    }

    public static function convertTables($context,$option){

        $sql=$context->prefix.$context->table;

        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]=="join"){
                    $sql.=self::convertJoin("inner",$o_["argv"],$context);
                }
                else if($o_["command"]=="leftJoin"){
                    $sql.=self::convertJoin("left",$o_["argv"],$context); 
                }
                else if($o_["command"]=="rightJoin"){
                    $sql.=self::convertJoin("right",$o_["argv"],$context); 
                }

            }
        }

        return $sql;
    }

    private static function convertJoin($joinType,$option,$context){

        if($joinType=="inner"){
            $joinName=" INNER JOIN ";
        }
        else if($joinType=="left"){
            $joinName=" LEFT OUTER JOIN ";
        }
        else if($joinType=="right"){
            $joinName=" RIGHt OUTER JOIN "; 
        }

        $tableName=$option[0];

        if(count($option)==1){

            if(!empty($context->surrogateKey['enable']) && !empty($context->surrogateKey['field'])){

                $onString=$tableName.'.'.$context->prefix.$context->table.'_'.$context->surrogateKey['field'].' = '.$context->prefix.$context->table.'.'.$context->surrogateKey['field'];
                $sql=$joinName.$tableName." ON ".$onString;  

            }

        }
        else if(count($option)==2){
            if(is_string($option[1])){
                $option[1]=[$option[1]];
            }
            $onString="";
            $ind=0;
            foreach($option[1] as $o_){
                if($ind){
                    $onString.=" AND ";
                }
                $onString.=$o_;
                $ind++;
            }
            $sql=$joinName.$tableName." ON ".$onString;
        }
        else if(count($option)==3){
            if(is_string($option[2])){
                $option[2]=[$option[2]];
            }
            $onString="";
            $ind=0;
            foreach($option[2] as $o_){
                if($ind){
                    $onString.=" AND ";
                }
                $onString.=$o_;
                $ind++;
            }
            $sql=$joinName.$tableName." ".$option[1]." ON ".$onString;
        }

        return $sql;
    }

    public static function convertWhere($option){
        return self::convertWhereHaving("where",$option);
    }

    public static function convertHaving($option){
        return self::convertWhereHaving("having",$option);
    }
    
    public static function convertWhereHaving($type, $option){

        $sql="";
        $ind=0;

        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]==$type){

                    if($ind){
                        $operand="AND";
                        if(!empty($o_["conditions"])){
                            $operand=$o_["conditions"];
                        }
                        $sql.=" ".$operand." ";
                    }

                    $field=$o_["field"];
                    $operand=$o_["operand"];
                    $value=$o_["value"];

                    if($value){
                        if(is_array($value)){
                            $buffer="(";
                            $ind=0;
                            foreach($value as $ind=>$v_){
                                if($ind){
                                    $buffer.=",";
                                }

                                if($v_){
                                    $v_=self::escape($v_);
                                }
                                else{
                                    if($v_===""){
                                        $v_="``";
                                    }
                                    else if($v_===0){
                                        $v_="0";
                                    }
                                    else if($v_===null){
                                        $v_="NULL";
                                    }    
                                }
                                $buffer.="'".$v_."'";
                                $ind++;
                            }
                            $buffer.=")";
                            $value=$buffer;
                        }
                        else{
                            $value="'".self::escape($value)."'";
                        }
                    }
                    else{
                        if($value===""){
                            $value="``";
                        }
                        else if($value===0){
                            $value="0";
                        }
                        else if($value===null){
                            $value="NULL";
                        }
                    }

                    $sql.=$field." ".$operand." ".$value;
                    $ind++;
                }
            }
        }

        if($sql){
            $ku=strtoupper($type);
            $sql=" ".$ku." ".$sql;
        }
        return $sql;

    }

    public static function convertOrderBy($option){

        $sql="";
        $ind=0;
        
        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]=="orderBy"){
                    if($ind){
                        $sql.=",";
                    }
                    $sql.=$o_["fieldName"]." ".$o_["sort"];
                    $ind++;
                }
            }
        }

        if($sql){
            $sql=" ORDER BY ".$sql;
        }

        return $sql;
    }

    
    public static function convertLimit($option){

        $sql="";
        $ind=0;
        
        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]=="limit"){
                    $sql.=$o_["offset"].",".$o_["limit"];
                }
            }
        }

        if($sql){
            $sql=" LIMIT ".$sql;
        }
        
        return $sql;
    }

    public static function convertInsert($option){

        $ind=0;

        $fields="";
        $values="";
        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]=="values"){
                    foreach($o_["value"] as $field=>$value){
                        if($ind){
                            $fields.=", ";
                            $values.=", ";                    
                        }
                        if($value){
                            $value=self::escape($value);
                        }
                        else{
                            if($value===0){
                                $value="0";
                            }
                            else if($value===""){
                                $value="''";
                            }
                            else if($value===null){
                                $value=null;
                            }
                        }
                        $values.="'".$value."'";
                        $fields.=$field;
                        $ind++;
                    }
                }
            }
        }

        return [
            "fields"=>$fields,
            "values"=>$values,
        ];

    }

    public static function convertUpdate($option){

        $ind=0;

        $sql="";
        if(is_array($option)){
            foreach($option as $o_){
                if($o_["command"]=="values"){
                    foreach($o_["value"] as $field=>$value){
                        if($ind){
                            $sql.=", ";                 
                        }
                        if($value){
                            $value=self::escape($value);
                        }
                        else{
                            if($value===0){
                                $value="0";
                            }
                            else if($value===""){
                                $value="''";
                            }
                            else if($value===null){
                                $value=null;
                            }
                        }
                        $sql.=$field."='".$value."'";
                        $ind++;
                    }
                }
            }
        }

        return $sql;

    }

    public static function convertDelete($context,$option){

        $sql="DELETE FROM ".$context->prefix.$context->table;

        return $sql;
    }

    public static function convertCreateDatabase($database,$option){

        $sql="CREATE DATABASE";
        
        if(!empty($option["ifNotExists"])){
            $sql.=" IF NOT EXISTS";
        }
        
        $sql.=" ".$database;

        if(!empty($option["charset"])){
            $sql.=" CHARACTER SET ".$option["charset"];        
        }
        if(!empty($option["collation"])){
            $sql.=" COLLATE ".$option["collation"];        
        }

        return $sql;
    }

    public static function convertCreateTable($tableName,$tableOption){

        $sql="CREATE TABLE";
        
        if(!empty($option["ifNotExists"])){
            $sql.=" IF NOT EXISTS";
        }
        
        $sql.=" ".$tableName;

        if(!empty($tableOption["fields"])){
            $sql.=" (".self::convertCreateTableField($tableOption["fields"]).") ";
        }

        if(!empty($tableOption["engine"])){
            $sql.="\n ENGINE = ".$tableOption["engine"];
        }

        if(!empty($tableOption["charset"])){
            $sql.="\n CHARACTER SET ".$tableOption["charset"];        
        }
        if(!empty($tableOption["collation"])){
            $sql.="\n COLLATE ".$tableOption["collation"];        
        }

        if(!empty($tableOption["comment"])){
            $sql.="\n COMMENT = '".$tableOption["comment"]."'";
        }

        return $sql;
    }

    private static function convertCreateTableField($params){
        
        $sql="\n";
        $ind=0;
        foreach($params as $name=>$value){
            if($ind!=0){
                $sql.=",";
            }

            $sql.=$name;
            
            $type=$value["type"];
            if(is_array($value["type"])){
                
                $_type=$value["type"];

                if(count($_type)==2){
                    $type=$_type[0]."(".$_type[1].")";
                }
                else if(count($_type)==3){
                    $type=$_type[0]."(".$_type[1].",".$_type[2].")";
                }
                else{
                    $type=$_type[0];
                }
            }

            $sql.=" ".strtoupper($type);

            if(!empty($value["autoIncrement"])){
                $sql.=" AUTO_INCREMENT";
            }
            
            if(!empty($value["notNull"])){
                $sql.=" NOT NULL";
            }

            if(!empty($value["primaryKey"])){
                $primaryKey=$name;
            }

            if(isset($value["default"])){
                $sql.=" DEFAULT '".$value["default"]."'";
            }

            if(!empty($value["comment"])){
                $sql.=" COMMENT '".$value["comment"]."'";
            }

            $sql.="\n";
            $ind++;
        }

        if(!empty($primaryKey)){
            $sql.=",PRIMARY KEY(".$primaryKey.")";
        }

        return $sql."\n";
    }

    public static function convertCreateView($viewName,$viewSql){

        $sql="CREATE VIEW";
        
        if(!empty($option["ifNotExists"])){
            $sql.=" IF NOT EXISTS";
        }

        $sql.=" ".$viewName." AS \n ".$viewSql;

        return $sql;

    }

    public static function convertDropTable($tableName,$option){

        $sql="DROP TABLE";

        if(!empty($option["ifExists"])){
            $sql.=" IF EXISTS";
        }

        $sql.=" ".$tableName;

        return $sql;

    }

    public static function convertDropView($viewName,$option){

        $sql="DROP View";

        if(!empty($option["ifExists"])){
            $sql.=" IF EXISTS";
        }

        $sql.=" ".$viewName;

        return $sql;

    }

    public static function convertGetField($context){

        $tableName=$context->prefix.$context->table;

        if($context->getConnection("driver")=="mysql"){
			$sql="SHOW COLUMNS FROM ".$tableName.";";
		}
		else if($context->getConnection("driver")=="sqlite"){
			$sql="PRAGMA table_info('".$tableName."');";
        }

        return $sql;
        
    }

    public static function convertGetIndex($context){

        $tableName=$context->prefix.$context->table;

        if($context->getConnection("driver")=="mysql"){
			$sql="SHOW INDEX FROM ".$tableName.";";
		}
		else if($context->getConnection("driver")=="sqlite"){
			$sql="select * from sqlite_master where name='".$tableName."';";
        }
     
        return $sql;   

    }

    public static function convertGetDatabases($context){
        $sql="SHOW DATABASES";
        return $sql;   
    }

    public static function convertGetTables($context){
		$sql="SHOW TABLES;";
        return $sql;   
    }

    public static function convertGetVariables($context){
		$sql="SHOW VARIABLES;";
        return $sql;
    }

    public static function convertGetProcessList($context){
        $sql="SHOW PROCESSLIST;";
        return $sql;
    }

    private static function escape($value){
		$value=str_replace("\\","\\\\",$value);
		$value=str_replace("'","\\'",$value);
		return $value;
    }
}