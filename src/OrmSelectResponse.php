<?php

/**
 * 
 * Mk2 OR - Mapping(ORM)
 * 
 * OrmSelectResponse Class
 * 
 * Copylight : Nakajima Satoru.
 * 
 */

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

	/**
     * row
     */
	public function row(){
		if($this->item){
			return $this->item;
		}
	}

	/**
     * item
     */
	public function item(){
		if($this->item){
			return $this->item;
		}
	}

	/**
     * get
	 * @param $name
     */
	public function get($name){
		if(!empty($this->item->{$name})){
			return $this->item->{$name};
		}
	}

	/**
     * value
	 * @param $name
     */
	public function value($name){
		if(!empty($this->item->{$name})){
			return $this->item->{$name};
		}
	}

	/**
     * toArray
     */
	public function toArray(){

		$result=(array)$this->item;

		if($this->type=="all" || $this->type=="paginate"){
			foreach($result as $ind=>$r_){
				$result[$ind]=(array)$r_;
			}
		}

		return (array)$result;
	}

	/**
     * toSql
     */
	public function toSql(){
		return $this->sql;
	}

	/**
     * toBindSql
     */
	public function toBindSql(){
		return $this->bindSql;
	}

	/**
     * toSaveSql
     */
	public function toSaveSql(){
		return $this->saveSql;
	}
	
	/**
     * toPaginate
     */
	public function toPaginate(){
		return $this->paging;
	}

}