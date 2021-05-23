<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StuffPropertyModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }


   function get_property($StuffId)
   { 
        $property='';
        
        $sql    = "SELECT Property FROM  stuffproperty WHERE StuffId=? ORDER BY Property";
        $query=$this->db->query($sql,array($StuffId));
        
         foreach ($query->result_array() as $row){
	          $propertyOne = $row['Property'];
	          if ($propertyOne==6 || $propertyOne==8 || $propertyOne==12 ||$propertyOne==13 || $propertyOne==14 ||$propertyOne>=16)
	          {

	          }
	         else 
              $property.=$property==''?$row['Property']:',' . $row['Property'];
         }
         
         return $property;
    }
    
	function get_property_array($StuffId)
	{
        $propertys=array();
        
        $sql    = "SELECT Property FROM  stuffproperty WHERE StuffId=? ORDER BY Property";
        $query=$this->db->query($sql,array($StuffId));
        
         foreach ($query->result_array() as $row){
              $propertys[]=$row['Property'];
         }
         
         return $propertys;
    }
    
}