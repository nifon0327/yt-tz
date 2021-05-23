<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  BadgeModel extends MC_Model {
   
    function __construct()
    {
        parent::__construct();
    }
    
  
    public function get_badge_value($menutype){
      $this->load->model('LoginUser');
      
      $this->load->model('StaffWorkStatusModel');
      $this->StaffWorkStatusModel->update_estate();
       
      $dataArray=array();
       
      $menutype=$menutype==''?'':$menutype;  
	  $sql="SELECT ModuleId,name,oldModuleId,oldItemId,leftbadge,rightbadge 
	         FROM ac_menus 
	         WHERE parent_id=0 AND typeid=? AND Estate=1 AND (LENGTH(leftbadge)>0 OR LENGTH(rightbadge)>0) 
	         ORDER BY Id";
	  $query = $this->db->query($sql,$menutype);
	  foreach($query->result_array() as $row){
	       $checkSign = true; 
	       if ($row['oldModuleId']!="" || $row['oldItemId']!=""){
	       
	          $checkSign1=false; $checkSign2=false;
	          if ($row['oldModuleId']!=""){
	             $checkSign1=$this->LoginUser->check_authority_modules($row['oldModuleId']);
	          }
	       
	          if ($row['oldItemId']!=""){
		         $checkSign2=$this->LoginUser->check_authority_Items($row['oldItemId']);
	          } 
	          $checkSign=($checkSign1 || $checkSign2)?true:false;
	       }
	       
	       if ($checkSign==true){
	         $left_value="";
	         $right_value=""; 
	         if ($row['leftbadge']!=""){
	           $badges=explode('/', $row['leftbadge']);
	             if (count($badges)==1){
			          $left_value=$this->$badges[0]();  
		         }
		         else{
			         $this->load->model($badges[0]);
		             $left_value=$this->$badges[0]->$badges[1]();        
		         }
		      }
		      
		      if ($row['rightbadge']!=""){
	           $badges=explode('/', $row['rightbadge']);
	             if (count($badges)==1){
			          $right_value=$this->$badges[0]();  
		         }
		         else{
			         $this->load->model($badges[0]);
		             $right_value=$this->$badges[0]->$badges[1]();        
		         }
		      }
		      
		      $right_color='';
		      if (is_array($right_value)){
			     $values= $right_value;
			     $right_value=$values['Text'];
			     $right_color=isset($values['Color'])?$values['Color']:'';
		      }
		      
		       $dataArray[]=array(
		                 'id' =>$row['ModuleId'],
		           'moduleid' =>$row['ModuleId'],
		               'name' =>$row['name'],
		          'leftBadge'  =>$left_value==0?'':'' . $left_value,
		          'rightBadge'=>$right_value==0?'':'' . $right_value,
		          'color'     =>"$right_color"
		      );  
	       }
    }
    return $dataArray;
  }
  
  function get_TotalClients()
  {
	   //读取无线用户数
       return  file_get_contents("http://192.168.16.2/Web_Query/Cisco_WLC_TotalClients.php");
  }
  
  
  //获取新品的数量
  function get_NewArrivalTotals()
  {
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM new_arrivaldata  WHERE Estate>0';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
  }
  
  //获取今日新品的数量
  function get_TodayNewArrivals(){
		$sql = "SELECT IFNULL(COUNT(*),0) AS Counts  FROM new_arrivaldata  WHERE DATE_FORMAT(created,'%Y-%m-%d')=CURDATE() AND Estate>0";
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts']>0?$row['Counts']:'';
	}
	
	
	//获取配件的数量
	function get_StuffTotals(){
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM stuffdata  WHERE Estate>0';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}

    //获取产品的数量
	function get_ProductTotals(){
		$sql = 'SELECT IFNULL(COUNT(*),0) AS Counts  FROM productdata  WHERE Estate>0';
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}
	
	
	 //返回上班人员总数
    function get_worksTotals($GroupId=0){
	    //$this->db->where('CheckType',1);
	    $thisDate = $this->Date;
	    
	    $where = " CheckType='1' AND (Date='$thisDate' OR JobId='38') ";
	    $this->db->where($where);
	    
	    if ($GroupId!=0){
	        $idArray=explode(',', $GroupId);
		    $this->db->where_in('GroupId',$idArray); 
	    }
	    
	    $this->db->from('staff_workstatus');
	    
        return $this->db->count_all_results();
    }
    
    //获取当天行事记录
    function get_eventTotals(){
		$sql = "SELECT IFNULL(COUNT(*),0) AS Counts  FROM event_sheet  WHERE Estate=1 AND DATE_FORMAT( DateTime,'%Y-%m-%d')=?";
		$query=$this->db->query($sql,array($this->Date));
	    $row = $query->row_array();
	    return $row['Counts']>0?$row['Counts']:'';
	}
	
	//今日新单
	function get_orderAmount(){
	    $month= date('Y-m',strtotime($this->Date));
	    $red =$this->colors->get_color('red');
	    
		$sql = "SELECT SUM(S.Price*S.Qty*D.Rate) AS Amount,SUM(IF(M.OrderDate=CURDATE(),1,0)) AS curSign  
	                    FROM yw1_ordersheet S 
	                    LEFT JOIN yw1_ordermain M ON M.OrderNumber=S.OrderNumber
	                    LEFT JOIN trade_object C ON C.CompanyId = M.CompanyId
					    LEFT JOIN currencydata D ON D.Id = C.Currency 
	                    WHERE  DATE_FORMAT( M.OrderDate,'%Y-%m' )='$month' ";
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    $Amount=$row['Amount']==''?0:round($row["Amount"]/10000);
	    
	    $colors=$row['curSign']>0?$red:'';
	    $Amount=$Amount>100?round($Amount/100) . "M":$Amount;
	    return  array('Text'=>"$Amount",'Color'=>"$colors");
	}
	
	//当月已出金额
	function get_shipedAmount(){
	    $month=date('Y-m',strtotime($this->Date));
		$sql = "SELECT SUM(S.Qty*S.Price*D.Rate*M.Sign) AS Amount  
		            FROM ch1_shipmain M 
		            LEFT JOIN ch1_shipsheet S ON S.Mid=M.Id 
		            LEFT JOIN trade_object C ON C.CompanyId=M.CompanyId 
		            LEFT JOIN currencydata D ON D.Id=C.Currency 
		            WHERE M.Estate='0' and DATE_FORMAT( M.Date,'%Y-%m' )='$month' ";
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Amount']==''?0:round($row["Amount"]/10000);
	}
	
	//品检开单记录数
	 function get_gysshTotals(){
		$sql = "SELECT IFNULL(COUNT(*),0) AS Counts  FROM gys_shsheet  WHERE Estate=1 AND SendSign<2";
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Counts'];
	}

	
	//待检+品检中记录数
	 function get_qcingTotals(){
		//$sql = "SELECT IFNULL(COUNT(*),0) AS Counts  FROM gys_shsheet  WHERE Estate=2 AND SendSign<2";
		$sql = "SELECT SUM(S.Qty*G.Price*C.Rate) AS Amount 
								FROM gys_shsheet S 
								LEFT JOIN cg1_stocksheet G ON G.StockId=S.StockId
								LEFT JOIN trade_object P ON P.CompanyId=G.CompanyId
								LEFT JOIN currencydata C ON C.Id=P.Currency 
								WHERE S.Estate=2 AND S.SendSign<2";
		 $query=$this->db->query($sql);
	     $records = $query->row_array();
	     $qcAmount = round($records['Amount']);
         $qcAmount = intval($qcAmount/10000);
         
         return $qcAmount>0 ?number_format($qcAmount) :'';
	  //  return $records['Counts'];
	}
	
	//采购单本周未送金额
	function get_cgshAmount()
	{
	    $thisWeek = $this->ThisWeek;
		$sql = "SELECT SUM((A.cgQty-A.rkQty)*A.Price*A.Rate) AS Amount 
			FROM (
					SELECT (G.AddQty+G.FactualQty) AS cgQty,G.Price,D.Rate,IFNULL(SUM(K.Qty),0) AS rkQty 
					FROM cg1_stocksheet G 
					LEFT JOIN trade_object P ON P.CompanyId=G.CompanyId AND P.ObjectSign IN (1,3)
					LEFT JOIN currencydata D ON D.Id=P.Currency 
					LEFT JOIN ck1_rksheet K ON K.StockId=G.StockId  
					WHERE G.DeliveryWeek='$thisWeek' AND G.Mid>0 AND G.rkSign>0  AND FIND_IN_SET(G.CompanyId,getSysConfig(106))=0 
					GROUP BY G.StockId  
			)A  WHERE A.cgQty>A.rkQty";
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Amount']==''?0:round($row["Amount"]/10000);
	}
	
	//采购单逾期未送金额
	function get_overshAmount()
	{
	    $thisWeek = $this->ThisWeek;
	    $red =$this->colors->get_color('red');
		$sql = "SELECT SUM((A.cgQty-A.rkQty)*A.Price*A.Rate) AS Amount 
			FROM (
					SELECT (G.AddQty+G.FactualQty) AS cgQty,G.Price,D.Rate, IFNULL(SUM(K.Qty),0) AS rkQty 
					FROM cg1_stocksheet G 
					LEFT JOIN cg1_stockmain M ON M.Id=G.Mid 
					LEFT JOIN trade_object P ON P.CompanyId=M.CompanyId AND P.ObjectSign IN (1,3)
					LEFT JOIN currencydata D ON D.Id=P.Currency 
					LEFT JOIN ck1_rksheet K ON K.StockId=G.StockId 
					WHERE G.DeliveryWeek<'$thisWeek' AND G.DeliveryWeek>0 AND G.Mid>0 AND G.rkSign>0  
					AND FIND_IN_SET(M.CompanyId,getSysConfig(106))=0  
					GROUP BY G.StockId  
			)A WHERE A.cgQty>A.rkQty";
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Amount']==''?0:array('Text'=>'' . round($row["Amount"]/10000),'Color'=>"$red");
	}
	
	//组装工价统计
	function get_packagingWages()
	{
	   $this->load->model('StaffMainModel');
	    $this->load->model('WorkShopdataModel');
	    
	    $groupids = $this->WorkShopdataModel->get_workshop_groupid('101',0);
	    $nums     = $this->StaffMainModel->get_checkInNums_ingroup($groupids);
	    
	    $sql = "SELECT IFNULL(SUM(C.Qty*G.Price),0) AS RGAmount  
		       	FROM sc1_cjtj C 
		        INNER JOIN yw1_scsheet S ON S.sPOrderId=C.sPOrderId 
		       	INNER JOIN cg1_stocksheet G ON G.StockId=S.StockId 
				WHERE  C.Date=? AND S.ActionId=? ";
		$query=$this->db->query($sql,array($this->Date,'101'));
	    $row = $query->row_array(); 
	    return $nums>0? ('¥'.number_format($row['RGAmount']/$nums)):0;
	}
	
	//半成品工价统计
	function get_semifinishedWages()
	{
	    
	    $this->load->model('StaffMainModel');
	    $this->load->model('WorkShopdataModel');
	    
	    $groupids = $this->WorkShopdataModel->get_workshop_groupid('',1);
	    $nums     = $this->StaffMainModel->get_checkInNums_ingroup($groupids);
	    
	    $sql = "SELECT IFNULL(SUM(C.Qty*G.Price),0) AS RGAmount  
		       	FROM sc1_cjtj C 
		        INNER JOIN yw1_scsheet S ON S.sPOrderId=C.sPOrderId 
		       	INNER JOIN cg1_stocksheet G ON G.StockId=S.StockId 
				WHERE  C.Date=? AND S.ActionId=102 ";
		$query=$this->db->query($sql,array($this->Date));
	    $row = $query->row_array();
	    return $nums>0?'¥'.number_format($row['RGAmount']/$nums):0;
	}
	
	//10天半成品平均产值
	function get_semifinished_average()
	{
	
	    $this->load->model('ScCjtjModel');
	    $this->load->model('WorkShopdataModel');
	    
	    $ActionId=102;//只取综合加工
	    
	  //  $groupids   = $this->WorkShopdataModel->get_workshop_groupid('',1);
	    $groupids   = $this->WorkShopdataModel->get_action_groupid($ActionId);
	    $day_output = $this->ScCjtjModel->get_day_average($groupids);
	    
	    $sql = "SELECT SUM((A.Qty-A.ScQty)*A.Price) AS RGAmount  
				FROM (
				  SELECT S.Qty,SUM(IFNULL(T.Qty,0)) AS ScQty,G.Price  
					  FROM   yw1_scsheet S 
					  INNER JOIN cg1_stocksheet G ON G.StockId=S.StockId 
					  LEFT JOIN sc1_cjtj T ON T.sPOrderId=S.sPOrderId 
				  WHERE S.ScFrom>0 AND S.Estate>0  AND S.ActionId IN($ActionId) GROUP BY S.sPOrderId 
				)A ";  
		$query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $day_output>0?number_format($row['RGAmount']/$day_output) . 'd':0;
	}

	
	//待组装数量
	function get_packagingQty()
	{
	    $sql = "SELECT SUM(IFNULL(B.Qty-B.ScQty,0)) AS Qty 
		        FROM ( 
					SELECT A.sPOrderId,A.Qty,A.ScQty,getCanStock(A.sPOrderId,0) AS canSign  
					FROM (
						    SELECT S.sPOrderId,S.Qty,IFNULL(SUM(C.Qty),0) AS ScQty   
							FROM      yw1_scsheet    S 
							LEFT JOIN yw1_ordersheet Y ON Y.POrderId=S.POrderId 
                            LEFT JOIN sc1_cjtj C ON C.sPOrderId=S.sPOrderId 
							WHERE S.WorkShopId='101' AND S.ScFrom>0 AND S.Estate>0   
                            GROUP BY   S.sPOrderId
					)A WHERE 1
		         )B WHERE B.canSign>1  AND B.canSign<=3 ";
		         
	    $query=$this->db->query($sql);
	    $row = $query->row_array();
	    return $row['Qty']>0?number_format($row['Qty']/1000) . 'k' :'';
	}
	
	//获取成品金额
	function get_order_rkamount()
	{
		   $this->load->model('YwOrderSheetModel');
		   $records = $this->YwOrderSheetModel->get_waitcp_sum();
		   $rkAmount = round($records['Amount']);
	       $rkAmount = intval($rkAmount/10000);
	       return $rkAmount>0 ? number_format($rkAmount) :'';
	}
	
	//获取本周交期订单金额
	function get_order_weekamount()
	{
	       $thisWeek = $this->ThisWeek;
	       
		   $this->load->model('YwOrderSheetModel');
		   $query = $this->YwOrderSheetModel->get_notout_weeks('',$thisWeek);
		   if ($query->num_rows() > 0) {
			     $records= $query->first_row('array');
		         $weekAmount = round($records['Amount']);
	             $weekAmount = intval($weekAmount/10000);
	             return $weekAmount>0 ? number_format($weekAmount) :'';
	       }else{
		       return '';
	       }
	}
	
	function get_inware_amount() 
	{
		$this->load->model('CkrksheetModel');
		$records = $this->CkrksheetModel->get_stock_amount('all','');
        $stockAmount = round($records['Amount']);
        $stockAmount = intval($stockAmount/10000);
		
		return $stockAmount>0 ? number_format($stockAmount) :'';
	}
	
	
	 function get_llsheet_amount()
	 {
		 $this->load->model('CkllsheetModel');
		 $llAmount = $this->CkllsheetModel->get_day_llamount($this->Date);
         $llAmount = round($llAmount);
		 $llAmount = intval($llAmount/10000);
		 
		 return $llAmount>0 ?number_format($llAmount) :'';
	}
	
}