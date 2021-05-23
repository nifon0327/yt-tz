<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ckBasketTypeModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

    function getBasketType(){

        $sql = "Select * From ck_baskettype Where Estate=1";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
           return $query->result_array();
       }
       return  null;
    }

}

?>