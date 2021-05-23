<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class  StatisticsModel extends MC_Model {
   
    function __construct()
    {
        parent::__construct();
    }
    
    //读取主分类
    function get_main_menus(){
	     $sql = "SELECT Id,Name,IncomeSign,CallMethod,FirstIds,TotalValue,TotalDate        
	                              FROM ac_statisticsmenus  WHERE Estate=1 Order by SortId,Id"; 	
	                              
	    $query=$this->db->query($sql);
	   
	    return  $query->result_array();
    }
    
     //读取会计科目未收/未付款项
     function get_nopay_amount($FirstId,$Month='')
     {
	     $sql = "SELECT NoPaySql,PayedSql FROM ac_statisticstables WHERE FirstId=$FirstId";
	      $query=$this->db->query($sql);
	     $unionSql='';
	     foreach($query->result_array() as $row)
	     {
	          $unionSql.=$unionSql==""?$row['NoPaySql']:" UNION ALL " . $row['NoPaySql'];
	          
	          if ($Month!='' && $Month!=date('Y-m')){
	                //本期前(含本期)未收/未付货款
				     $unionSql =str_replace('{WHERE_SQL}',  " AND DATE_FORMAT(S.Date,'%Y-%m')<='$Month'" , $unionSql);
				     //$unionSql =str_replace('{WHERE_PDSQL}',  " AND DATE_FORMAT(S.PayDate,'%Y-%m')<='$Month'" , $unionSql); 
				     $unionSql =str_replace('{WHERE_MSQL}',  " AND S.Month<='$Month'" , $unionSql);   
				     //本期后已收/已付货款
				     if ($row['PayedSql']!=""){
				        $unionSql.=$unionSql==""?$row['PayedSql']:" UNION ALL " . $row['PayedSql']; 
				        
	                    $unionSql =str_replace('{WHERE_SQL}',  " AND DATE_FORMAT(M.PayDate,'%Y-%m')>'$Month' AND DATE_FORMAT(S.Date,'%Y-%m')<='$Month' " , $unionSql);  
	                   // $unionSql =str_replace('{WHERE_PDSQL}',  " AND DATE_FORMAT(M.PayDate,'%Y-%m')>'$Month' AND DATE_FORMAT(S.PayDate,'%Y-%m')<='$Month' " , $unionSql);
	                    $unionSql =str_replace('{WHERE_MSQL}',  " AND DATE_FORMAT(M.PayDate,'%Y-%m')>'$Month' AND S.Month<='$Month' " , $unionSql);
	                 }
	                      
	          }else{
		           $unionSql =str_replace('{WHERE_SQL}',  ' ' , $unionSql);
		           $unionSql =str_replace('{WHERE_PDSQL}',  ' ' , $unionSql);
		           $unionSql =str_replace('{WHERE_MSQL}',  ' ' , $unionSql);
		           $unionSql =str_replace('{WHERE_PMSQL}',  ' ' , $unionSql);
	          }
	         
	     }
	 
	    if ($unionSql!=''){
			   $query = null;
			   $row = null;
	           $mySql="SELECT SUM(A.Amount*A.Rate) AS RmbAmount FROM( ".$unionSql.") A ";    
			    $query=$this->db->query($mySql);
			    $row = $query->first_row('array');
			    return $row['RmbAmount'];
	     }
	      else{
		       return 0;
	     }
    }

    
    //读取会计科目统计款项
     function get_totals_amount($FirstId,$Month="")
     {
	     $sql = "SELECT TotalSql FROM ac_statisticstables WHERE FirstId=$FirstId";
	      $query=$this->db->query($sql);
	     $unionSql=''; $SearchRows='';
	     foreach($query->result_array() as $row)
	     {
	          $unionSql.=$unionSql==""?$row['TotalSql']:" UNION ALL " . $row['TotalSql'];
	          
	           if ($Month!='' && $Month!=date('Y-m')){
	              switch($FirstId){
		              case  1601: 
		                       $unionSql =str_replace('{WHERE_SQL}',  " AND DATE_FORMAT(S.PostingDate,'%Y-%m')<='$Month' AND C.Month<='$Month' " , $unionSql);  
		                 break;
		             default:
		                       $unionSql =str_replace('{WHERE_SQL}',  ' ' , $unionSql);
		                break;
	              }
	           }else{
		               $unionSql =str_replace('{WHERE_SQL}',  ' ' , $unionSql);
	           }
	     }
	 
	    if ($unionSql!=''){
			   $query = null;
			   $row = null;
	           $mySql="SELECT SUM(A.Amount*A.Rate) AS RmbAmount FROM( ".$unionSql.") A ";    
			    $query=$this->db->query($mySql);
			    $row = $query->first_row('array');
			    return $row['RmbAmount'];
	     }
	      else{
		       return 0;
	     }
    }
    
    //测试使用
    function get_nopaysql(){
    
	    $sql = "SELECT TableName,NoPaySql FROM ac_statisticstables WHERE Estate=1 AND NoPaySql IS NOT NULL ";
	      $query=$this->db->query($sql);
	     $unionSql='';
	     foreach($query->result_array() as $row)
	     {
	         $TableName = $row['TableName']; 
	         $noPaySql ="SELECT '$TableName' AS ItemName,SUM(Amount*A.Rate) AS Amount FROM (" .  $row['NoPaySql'] .") A "; 
	          $unionSql.=$unionSql==""?$noPaySql:" UNION ALL " . $noPaySql;
	     }
        echo $unionSql . "<br>";
    }
    
    //测试使用
     function get_banksql(){
    
	    $sql = "SELECT TableName,BankSql FROM ac_statisticstables WHERE Estate=1 AND BankSql IS NOT NULL ";
	      $query=$this->db->query($sql);
	     $unionSql='';
	     foreach($query->result_array() as $row)
	     {
	         $TableName = $row['TableName']; 
	         $bankSql ="SELECT '$TableName' AS ItemName,SUM(A.Amount*C.Rate) AS Amount FROM (" .  $row['BankSql'] .") A 
	          LEFT JOIN currencydata C ON C.Id=A.Currency 
	         "; 
	          $unionSql.=$unionSql==""?$bankSql:" UNION ALL " . $bankSql;
	     }
	    $unionSql =str_replace('{WHERE_SQL}',  ' ' , $unionSql);

        echo $unionSql . "<br>";
    }
    
    
     //读取银行结余款项
    function get_bank_amount($BankId,$Month='',$preSign=0)
    {
	    $sql = "SELECT BankSql FROM ac_statisticstables WHERE Estate=1 AND BankSql IS NOT NULL";
	    $query=$this->db->query($sql);
	    $unionSql='';
	    foreach($query->result_array() as $row)
	    {
	          $unionSql.=$unionSql==""?$row['BankSql']:" UNION ALL " . $row['BankSql'];
	    }
	    if ($unionSql!=''){
			    $query = null;
			    $row = null;
			    $SearchRows = " AND M.BankId='$BankId'";  
			    if ($Month!=''){
				      $SearchRows .=$preSign==1? " AND DATE_FORMAT(M.PayDate,'%Y-%m')<'$Month' ": " AND DATE_FORMAT(M.PayDate,'%Y-%m')<='$Month' ";  
			    }
			  
			    $unionSql =str_replace('{WHERE_SQL}',  $SearchRows , $unionSql);
			    
			    $curMonth=date('Y-m');
			    if ($Month!='' && $Month<$curMonth){
				     $mySql="SELECT ROUND(SUM(IFNULL(A.Amount,0)*A.Sign),2) AS TotalAmount,A.Currency,C.PreChar,IFNULL(R.Rate,C.Rate) AS Rate  FROM ( ".$unionSql.") A 
			                    LEFT JOIN currencyrate R ON R.Currency=A.Currency AND R.Month='$Month' 
			                    LEFT JOIN currencydata C ON C.Id=A.Currency 
			                    WHERE 1 GROUP BY A.Currency";
			    }else{
				     $mySql="SELECT ROUND(SUM(IFNULL(A.Amount,0)*A.Sign),2) AS TotalAmount,A.Currency,C.PreChar,C.Rate  FROM ( ".$unionSql.") A 
			                    LEFT JOIN currencydata C ON C.Id=A.Currency 
			                    WHERE 1 GROUP BY A.Currency";
			    }
			    
			                 
			    $query=$this->db->query($mySql);
			    $row = $query->result_array();
			    return $row;
			  //   return $row['TotalAmount'];
	     }
	     else{
		       return array();
	     }
    }
    
    //读取应收货款
     function get_accounts_receivable($FirstId=1122,$Month='',$preSign=0)
     {
          $sql = "SELECT NoPaySql,PayedSql FROM ac_statisticstables WHERE FirstId=$FirstId";
	      $query=$this->db->query($sql);
	     $unionSql='';
	     foreach($query->result_array() as $row)
	     {
	        
	          $unionSql.=$unionSql==""?$row['NoPaySql']:" UNION ALL " . $row['NoPaySql'];
	          
	          if ($Month!='' && $Month!=date('Y-m')){
	                //本期前(含本期)未收货款
				     $unionSql =str_replace('{WHERE_SQL}',  " AND DATE_FORMAT(M.Date,'%Y-%m')<='$Month'" , $unionSql);
				       
				     //本期后已收货款
				     if ($row['PayedSql']!=""){
					     $unionSql.=$unionSql==""?$row['PayedSql']:" UNION ALL " . $row['PayedSql']; 
		                 $unionSql =str_replace('{WHERE_SQL}',  " AND DATE_FORMAT(M.PayDate,'%Y-%m')>'$Month' AND DATE_FORMAT(S.Date,'%Y-%m')<='$Month' " , $unionSql);
	                 }
	                 
		       }else{
			          $unionSql =str_replace('{WHERE_SQL}',  ' ' , $unionSql);
		       }
	     }

	    if ($unionSql!=''){
			    $query = null;
			    $row = null;
	           $mySql="SELECT A.CompanyId,A.Logo,A.Forshort,A.Currency,A.days,A.PayMode,A.PreChar,
	                                        SUM(A.Amount) AS Amount,SUM(A.Amount*A.Rate) AS RmbAmount,
	                                        SUM(IF(DATE_ADD(A.Date,INTERVAL A.days DAY)<CURDATE(),A.Amount*A.Rate,0)) AS OverAmount 
	                          FROM( ".$unionSql.") A 
	                          WHERE 1 
	                         GROUP BY A.CompanyId  ORDER BY RmbAmount DESC";    
	            
			    $query=$this->db->query($mySql);
			    $row = $query->result_array();
			    return $row;
	     }
	      else{
		       return array();
	     }
     }
    
    //读取统计表数据
    function get_statistics_data($FirstId,$Month)
    {
	       $mySql="SELECT TotalValue,OtherValue FROM  ac_statisticsdata WHERE FirstId=? AND Month=?";    
		    $query=$this->db->query($mySql,array($FirstId,$Month));
		    $row = $query->first_row('array');
		    return $row;
    }
    
   //更新统计值
	function update_totalvalue($Id,$totalValue){
	   
	   $totalValue = $totalValue==''?0:$totalValue*1;
	   
	   $data=array(
	               'TotalValue'=>$totalValue,
	               'TotalDate' =>$this->Date
	              );
	              
	   $this->db->update('ac_statisticsmenus',$data, array('Id' => $Id));
	   
	   return $this->db->affected_rows();
   }
   
   // 保存上月的统计数据
   
   function save_lastmonth_totalvalue($FirstId,$Month,$totalValue,$otherValue='')
   {
	   $totalValue = $totalValue==''?0:$totalValue*1;
	   
	   $data=array(
	                       'Month'=>$Month,
	                       'FirstId' =>$FirstId,
	                'TotalValue'=>$totalValue,
	               'OtherValue'=>$otherValue,
	                         'Date' =>$this->DateTime,
	                   'Operator'=>'0'
	              );
	              
        $this->db->insert('ac_statisticsdata', $data); 
	    return $this->db->affected_rows();
   }
   
}