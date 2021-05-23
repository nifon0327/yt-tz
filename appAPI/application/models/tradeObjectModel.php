<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  TradeObjectModel extends MC_Model {
    function __construct()
    {
        parent::__construct();
    }
    
    
    function get_all_providers() {
		

		$sql = "SELECT CompanyId,Forshort,Letter FROM trade_object WHERE (ObjectSign=1 OR ObjectSign=3 ) AND Estate=1 ORDER BY Letter";
		$query=$this->db->query($sql);
		if ($query->num_rows()>0){
		    return $query->result_array();
	    }
	    return null;
		
	}

    
    function get_records($CompanyId)
    {
	    $sql = 'SELECT S.CompanyId,S.Forshort,S.Letter,C.Address,S.ChinaSafe,S.PayMode  
	            FROM trade_object S 
	            LEFT JOIN companyinfo C ON C.CompanyId=S.CompanyId AND C.Type=8
	            WHERE S.CompanyId=? LIMIT 1';
	            
	   $query=$this->db->query($sql,array($CompanyId));
	   if ($query->num_rows() > 0) 
	   return  $query->first_row('array');  
	   
	   return null;      
    }
    
    
    function get_prechar($CompanyId) {
	     $sql = 'SELECT  C.PreChar  
	            FROM trade_object S 
	            LEFT JOIN currencydata C ON S.Currency = C.Id 
	            WHERE S.CompanyId=? LIMIT 1';
	            
	   $query=$this->db->query($sql,$CompanyId);
	   if ($query->num_rows() > 0)  {
		   $row= $query->row();
		   return $row->PreChar;
	   }
	   return '';      
    }
    
    function get_forshort($CompanyId) {
	     $sql = 'SELECT  S.Forshort   
	            FROM trade_object S 
	            WHERE S.CompanyId=? LIMIT 1';
	            
	   $query=$this->db->query($sql,$CompanyId);
	   if ($query->num_rows() > 0)  {
		   $row= $query->row();
		   return $row->Forshort;
	   }
	   return '';      
    }
     //配件图片路径
   function get_logo_path()
   {
	   return  $this->config->item('download_path') . "/tradelogo/";
   }
	

    //以下为旧代码

    public function get_list_kd() {
		$sql = "SELECT A.* FROM(
				    SELECT S.CompanyId,P.Forshort,P.Letter 
					FROM cg1_stocksheet S 
		           	LEFT JOIN stuffdata D ON D.StuffId=S.StuffId
			        LEFT JOIN stufftype T ON T.TypeId=D.TypeId 
		            LEFT JOIN stuffmaintype TM ON TM.Id=T.mainType 
					LEFT JOIN trade_object P ON P.CompanyId=S.CompanyId 		
					WHERE  S.rkSign>0  AND TM.blSign=1 
		            GROUP BY S.CompanyId 
		       UNION ALL 
					SELECT M.CompanyId,P.Forshort,P.Letter 
					FROM ck2_thmain M
					LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId 			
					WHERE DATEDIFF(CURDATE(),M.Date)<90 GROUP BY M.CompanyId 
	        )A WHERE A.CompanyId>0 group by A.CompanyId  ORDER BY A.Letter";
        	return $this->db->query($sql);
	}


	public function get_item_psum() {
		/*array("stuff_count"=>"$stuff_count","typename"=>"$typename","typeid"=>"$typeid","prechar"=>"$PreChar");
*/
		$sql = 'SELECT M.CompanyId typeid, M.Forshort typename,Count(*)  stuff_count, C.PreChar prechar
					FROM trade_object M 
					LEFT JOIN productdata P ON P.CompanyId = M.CompanyId 
					LEFT JOIN currencydata C ON M.Currency = C.Id
					WHERE M.Estate = 1 
					AND M.ObjectSign IN (1,2)
					AND P.Estate = 1 
					GROUP BY M.CompanyId ORDER BY stuff_count DESC'	;
		return $this->db->query($sql);
	}

    public function get_item($params=array()) {
		/*$estate = 0;
		$objectSign = element('objectsign',$params,-1);
		$objectSignArray = array('1');
		if ($objectSign > -1) {
			$conditions = array(
						'Estate > '=>$estate
			);
			$objectSignArray = array('1',''.$objectSign);
		} else {
			$conditions = array(
						'Estate > '=>$estate
			);
			$objectSignArray = array('1','2','3');
		}
		$this->db->select('ObjectSign, CompanyId, Forshort');

		$this->db->where_in('ObjectSign', $objectSignArray);*/
		$query = $this->db->query('select O.ObjectSign, O.CompanyId, O.Forshort,C.Prechar,C.Rate from trade_object O left join currencydata C on C.Id=O.Currency where O.Estate >=1');
        return $query;
    }
	 
	 
	
	 
}