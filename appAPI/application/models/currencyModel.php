<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  CurrencyModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

	

    public function get_item($params=array()) {
		$idSingle = element('selectid',$params,'-1');
		if ($idSingle > 0 ) {
			$this->db->where('Id',$idSingle);
		}
		$query = $this->db->get('currencydata');
        return $query;
    }
	
	
	 
	

	 
}