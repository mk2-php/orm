<?php

namespace Mk2\Orm;

class OrmSelectResponse{

	protected $type;
	protected $sql=null;
	protected $bindSql;
	protected $saveSql;
	protected $deleteSql;
	protected $item;
	protected $paging;

	/**
     * __construct
     * @param $input
     */
	public function __construct($input){
				
		foreach($input as $field=>$value){
			$this->{$field}=$value;
		}
	}

    /**
     * setPaginate
     * @param $total
     * @param $limit
     * @param $page
     */
	public function setPaginate($total,$limit,$page){

		$this->paging=[
			"total"=>$total,
			"totalPage"=>ceil($total/$limit),
			"limit"=>$limit,
			"page"=>$page,
		];

	}

	/**
     * setSaveSql
     * @param $sql
     */
	public function setSaveSql($sql){
		$this->saveSql=$sql;
	}

	public function row(){
		return $this->item;
	}

	public function get($name){
		if(!empty($this->item->{$name})){
			return $this->item->{$name};
		}
	}

	public function toArray(){

		$result=(array)$this->item;

		if($this->type=="all" || $this->type=="paginate"){
			foreach($result as $ind=>$r_){
				$result[$ind]=(array)$r_;
			}
		}

		return (array)$result;
	}

	public function toSql(){
		return $this->sql;
	}

	public function toBindSql(){
		return $this->bindSql;
	}

	public function toSaveSql(){
		return $this->saveSql;
	}

	public function toPaginate(){
		return $this->paging;
	}

}