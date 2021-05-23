<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  YwclientOutDataModel extends MC_Model {

    function __construct()
    {
        parent::__construct();
    }
    
    function toout_name($POrderId) {
	    
	    
	    $OutResult = $this->db->query("SELECT D.ToOutName  FROM yw7_clientOutData O
									  LEFT JOIN yw7_clientToOut D ON O.ToOutId=D.Id
									  WHERE  O.POrderId='$POrderId' AND O.Mid=0 ");
		
		if ($OutResult->num_rows() >0) {
			return  $OutResult->row()->ToOutName;
		}
		return '';
    }
}