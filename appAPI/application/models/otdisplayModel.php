<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  OtdisplayModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }

    function get_display_tvip($Ids)
    {
	    $sql  = "SELECT IP,Port,ImageSign FROM ot2_display WHERE Id IN ($Ids) AND Estate=1";
        $query=$this->db->query($sql);
        
        return $query->result_array();
    }
    
    function get_packaging_tvip($Line)
    {
	    $identifier='48-4-' . $Line;
	    $sql  = "SELECT IP,Port FROM ot2_display WHERE Identifier ='$identifier' AND Estate=1 LIMIT 1";
	    $query=$this->db->query($sql);
	    return $query->result_array();
    }
    
    
}