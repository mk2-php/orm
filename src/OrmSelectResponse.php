<?php

namespace Mk2\Orm;

class OrmSelectResponse{

	protected $sql=null;
	protected $bindSql;
	protected $item;
	protected $paging;

	public function __construct($input){
		foreach($input as $field=>$value){
			$this->{$field}=$value;
		}
	}

	public function setPaginate($total,$limit,$page){

		$this->paging=[
			"total"=>$total,
			"totalPage"=>ceil($total/$limit),
			"limit"=>$limit,
			"page"=>$page,
		];

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
		return (array)$this->item;
	}

	public function toSql(){
		return $this->sql;
	}

	public function toBindSql(){
		return $this->bindSql;
	}

	public function toPaginate(){
		return $this->paging;
	}

}