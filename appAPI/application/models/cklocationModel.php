<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class  CkLocationModel extends MC_Model {
    
    function __construct()
    {
        parent::__construct();
    }
    
    function get_region_keybord() {
	    
	    $sql ="select Region from ck_location group by Region order by Region;";
	     $query=$this->db->query($sql);
	     return $query;
    }
      //返回指定Id的记录
     function get_records($Id)
    {
	    $sql = 'SELECT Id,Mid,WorkAdd,Floor,Region,Location,ShelfSign,Identifier FROM ck_location WHERE Id=? LIMIT 1';
	          
	   $query=$this->db->query($sql,array($Id));
	   return  $query->first_row('array');        
    }
    
    //返回库位信息
    function get_locations($Floor,$StuffId='',$Type=1) {
	    
	    $dataArray = array();
	   
	    
	    $Regions=$this->get_region($Floor,$Type);
	    foreach($Regions as $row)
	    {
	       $qty = '';
	       if ($StuffId>0){
		       
		       
		      if ($Type == 2) {
			      $this->load->model('YwOrderRkModel');
			      $qty = $this->YwOrderRkModel->get_region_productqty($StuffId,$row['Region']);
		      }  else {
			      $this->load->model('CkrksheetModel');
			      $qty = $this->CkrksheetModel->get_region_stuffqty($StuffId,$row['Region']);
		      
		      }
		      
		      
		      $qty = $qty>0?number_format($qty):'';
	       }
	       
	       $subArray=$this->get_region_location($Floor,$row['Region'],$StuffId, $Type);
	       
	       $dataArray[]=array(
			    'Id'   =>$Floor,
			    'title'=>$row['Region'] . '区',
			    'qty'  =>"$qty",
			    'floor'=>$row['Floor'],
			    'sub'  =>$subArray
			);
	       
	    }
	    return $dataArray; 
    }
    
    
     //获取库位区号(按楼层)
    function get_region($Floor='',$Type=1)
    {
	    
	    $sql  = 'SELECT L.Region,L.Floor 
	                FROM ck_location  L 
	                 LEFT JOIN warehouse W ON W.Id=L.WarehouseId 
	                 WHERE  L.Floor=? AND L.Estate=1 AND L.Mid=0  AND W.Type=? GROUP BY L.Region';    
	    if ($Floor=='') {
		     $sql  = 'SELECT L.Region,L.Floor 
		                   FROM ck_location  L 
		                   LEFT JOIN warehouse W ON W.Id=L.WarehouseId  
		                   WHERE 1 AND L.Estate=1 AND L.Mid=0 AND W.Type=? GROUP BY L.Region'; 
		     $query=$this->db->query($sql,array($Type));
	    }  else {
		    $query=$this->db->query($sql,array($Floor,$Type));
	    }
	    
	    
	    
		return $query->result_array();
    }

    
    //获取区号包含库位(按楼层、区号)
    function get_region_location($Floor,$Region,$StuffId='', $Type=1)
    {
	    
	    $sql  = 'SELECT Id,Location,Identifier,ShelfSign FROM ck_location WHERE Floor=? AND Region=? AND Mid=0 AND  Estate=1';   
	    
	    if ($Floor == '') {
		    $sql  = 'SELECT Id,Location,Identifier,ShelfSign FROM ck_location WHERE 1 AND Region=? AND Mid=0 AND  Estate=1';   
	    
			$query=$this->db->query($sql,array($Region));
	    }  else {
		    $query=$this->db->query($sql,array($Floor,$Region));
	    }
	    
	    
	    $records=array();
	    if ($StuffId>0){
		    if ($Type == 2) {
			      $this->load->model('YwOrderRkModel');

		    $records=$this->YwOrderRkModel->get_product_location($StuffId);
		      }  else {
			      $this->load->model('CkrksheetModel');
		    $records=$this->CkrksheetModel->get_stuff_location($StuffId);
		      }
		    
	    }
	    
	    $dataArray=array();
	    foreach($query->result_array() as $row){
	        
	        $subArray=array();
	        if ($row['ShelfSign']==1){
		         $subArray=$this->get_location_shelf($row['Id'],'', $Type);
		         
	        }
	        
	        $qty='';$oldSelect=0;
	        foreach ($records as $record){
	           if ($row['Id']==$record['LocationId']){
		          $qty = number_format($record['rkQty']);
		          $oldSelect=1;
		          break;
	           }
	        }
	        
		    $dataArray[]=array(
			    'Id'   =>$row['Id'],
			    'title'=>$Region . $row['Location'],
			    'qty'  =>"$qty",
			'oldSelect'=>"$oldSelect",
			    'sub'  =>$subArray
			);
		}
		return $dataArray; 
    }
    
    //获取库位货架信息
    function get_location_shelf($Mid,$StuffId='', $Type=1)
    {
	   $sql  = 'SELECT Id,Location,Identifier,ShelfSign FROM ck_location WHERE Mid=? AND  Estate=1';    
	   $query=$this->db->query($sql,array($Mid));
	    
	    $records=array();
	    if ($StuffId>0){
		     if ($Type == 2) {
			      $this->load->model('YwOrderRkModel');

		    $records=$this->YwOrderRkModel->get_product_location($StuffId);
		      }  else {
			      $this->load->model('CkrksheetModel');
		    $records=$this->CkrksheetModel->get_stuff_location($StuffId);
		      }
 
	    }
	    
	    $dataArray=array();
	    foreach($query->result_array() as $row)
	    {
	        $qty='';$oldSelect=0;
	        foreach ($records as $record){
	           if ($row['Id']==$record['LocationId']){
		          $qty = number_format($record['rkQty']);
		          $oldSelect=1;
		          break;
	           }
	        }
	        $idents=explode('-', $row['Identifier']);
	        $title=$idents[count($idents)-1];
		    $dataArray[]=array(
			    'Id'   =>$row['Id'],
			    'title'=>$title,
			    'layer'=>'sub',
			    'qty'  =>"$qty",
			'oldSelect'=>"$oldSelect"    
			);
		}
		return $dataArray;  
    }
    
    function create_newlocation($Action)
    {
	    if ($Action=='Add'){
		    $mainArray =array(1,2,3,4,5,6,78,81);
		    $shelfA=array(84,84,60,36,36,24,56,56);
		    $shelfB=array(84,84,40,60,36, 0,40,40);
		    
		    for ($i=0,$counts=count($mainArray);$i<$counts;$i++)
		    {
			    $Mid  =$mainArray[$i];
			    $Region=$i<6?'A':'C';
			    
			    for ($j=1;$j<=$shelfA[$i];$j++){
			    
			        $idts=$i<6?'48-3F-A' . $Mid . '-A' . $j:'48-3F-C' . $Mid . '-A' . $j;
			        
				    $data=array(
		                'Mid'=>$Mid, 
		            'WorkAdd'=>1,
		              'Floor'=>3,
		             'Region'=>$Region,  
		           'Location'=>$j, 
		          'ShelfSign'=>0,
		         'Identifier'=>$idts,
		             'Estate'=>'1',
		              'Locks'=>'0',
		               'Date'=>$this->DateTime,
		           'Operator'=>$this->LoginNumber,
		            'creator'=>$this->LoginNumber,
		            'created'=>$this->DateTime 
			       );
	       
	              $this->db->insert('ck_location', $data);
			    }
			    
			    for ($j=1;$j<=$shelfB[$i];$j++){
			    
			        $idts=$i<6?'48-3F-A' . $Mid . '-B' . $j:'48-3F-C' . $Mid . '-B' . $j;
			       
				    $data=array(
		                'Mid'=>$Mid, 
		            'WorkAdd'=>1,
		              'Floor'=>3,
		             'Region'=>$Region,
		           'Location'=>$j, 
		          'ShelfSign'=>0,
		         'Identifier'=>$idts,
		             'Estate'=>'1',
		              'Locks'=>'0',
		               'Date'=>$this->DateTime,
		           'Operator'=>$this->LoginNumber,
		            'creator'=>$this->LoginNumber,
		            'created'=>$this->DateTime 
			       );
	       
	              $this->db->insert('ck_location', $data);
			    }
		    }
	    }
    }
    
    
}