<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  ChartColorModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    //获取客户颜色
    function get_chartcolor($CompanyId)
    {
        $this->db->select('ColorCode');
	    $this->db->where('CompanyId',$CompanyId);
	    $query = $this->db->get('chart2_color');
	    $rows=$query->first_row('array');
	    return  $rows['ColorCode'];
    }
}