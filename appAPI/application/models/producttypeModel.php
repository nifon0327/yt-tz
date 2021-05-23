<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ProducttypeModel extends MC_Model {
	
	
	public function get_stufftypes() {
		
		$sql = "select T.TypeId,T.TypeName from stufftype T 
		where T.Estate>=1 and T.mainType=1
		and T.TypeId not in (9002,9005,9031,9033,9040,9049,9066,9109,9110,9116,9120,9137,9047)
		;";
		return $this->db->query($sql);
	}	
    


}