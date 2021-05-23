<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class attendanceipadsheetModel extends MC_Model {

    function get_ipadInfo($identifer){
        $sql = "SELECT Name, Id, Floor From attendanceipadsheet Where Identifier='$identifer' and Estate=1"; 
        $query = $this->db->query($sql);

        if($query->num_rows()> 0){
            return $query->row_array(0);
        }else{
            return '';
        }
    }
}



?>