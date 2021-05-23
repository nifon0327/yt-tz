<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  scSizeSheetModel extends MC_Model{

    function __construct(){
        parent::__construct();
    }

    function saveSize($ProductId, $sPorderId, $boxId, $size, $estate){
        $datetime = $this->DateTime;
        $sql = "Insert Into sc_sizeSheet (Id, ProductId, sPorderId, BoxId, Size, Date, Estate) Values (Null, $ProductId, '$sPorderId', '$boxId', '$size', '$datetime', $estate)";

        $this->db->trans_begin();

        $this->db->query($sql);

        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $result = "N";
        }else{
            $this->db->trans_commit();
            $result = $this->db->affected_rows();
        }

        return $result;

    }

}


?>