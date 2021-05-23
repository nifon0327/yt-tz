<?php
//日期选择器
$showToday=0;
switch($PickModuleId){
      case "109"://新订单下单日期选择
         $curDate=date("Y-m-d");
         $sDate=date("Y-m-d",strtotime("$curDate  -1   month"));
         $mySql="SELECT DISTINCT M.OrderDate AS Date FROM $DataIn.yw1_ordermain M WHERE M.OrderDate>'$sDate' ORDER BY Date DESC";
         $jsonArray[]=$curDate;
         $showToday=0;
      break;
     case "1090"://图例日期选择
         $curDate=date("Y-m-d");
         $sDate=date("Y-m-d",strtotime("$curDate  -1   month"));
         $mySql="SELECT DISTINCT M.OrderDate AS Date FROM $DataIn.yw1_ordermain M WHERE M.OrderDate>'$sDate' ORDER BY Date ";
         $showToday=1;
      break;
      case "213"://已备料组装

      break;
       case "210"://本月下单总额
        $mySql="SELECT DISTINCT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Date   
				FROM $DataIn.yw1_ordermain M 
				WHERE  M.OrderDate>'2008-01-01' ORDER BY Date";
       $curDate=date("Y-m");
       $showToday=1;
      break;
       case "1111": //今日加工
       case "1112"://今日组装
       $SearchRows=$ModelId=="1111"?" AND D.TypeId<>'7100' ":" AND D.TypeId='7100' ";
      $curDate=date("Y-m-d");
       $sDate=date("Y-m-d",strtotime("$curDate  -1   month"));
       $mySql="SELECT DISTINCT DATE_FORMAT(D.Date,'%Y-%m-%d')  AS Date   
		        FROM  $DataIn.sc1_cjtj D
		        WHERE D.Date>'$sDate'  $SearchRows  ORDER BY Date ";
         $showToday=1;
     break;
      case "1041": //今日出货
      $curDate=date("Y-m-d");
       $sDate=date("Y-m-d",strtotime("$curDate  -1   month"));
       $mySql="SELECT DISTINCT M.Date  AS Date   
		        FROM $DataIn.ch1_shipmain M
		        WHERE  M.Estate='0'  AND M.Date>'$sDate'  ORDER BY Date ";
         $showToday=1;
     break;
     case "104": //已出月份选择
     case "2105"://本月已出
       $mySql="SELECT DISTINCT DATE_FORMAT(M.Date,'%Y-%m') AS Date   
		        FROM $DataIn.ch1_shipmain M
		        WHERE  M.Estate='0'  ORDER BY Date";
        $curDate=date("Y-m");
         $showToday=1;
     break;
     case "198"://本月研砼报关
        $mySql="SELECT DISTINCT DATE_FORMAT(M.Date,'%Y-%m') AS Date    
		        FROM $DataIn.ch1_shipmain M
		        LEFT JOIN $DataIn.ch1_shipsheet S ON S.Mid=M.Id 
		        LEFT JOIN $DataIn.ch1_shiptypedata T ON T.Mid=M.Id 
		        WHERE M.Estate=0 AND (S.Type=1 OR S.Type=3) AND T.Type=1 ORDER BY Date";
       $curDate=date("Y-m");
       $showToday=1;
       break;
     case "122"://未收客户货款
      $CompanyId=$Info;
      $mySql="SELECT DISTINCT DATE_FORMAT(M.Date,'%Y-%m') AS Date 
				FROM $DataIn.ch1_shipmain M
				WHERE M.Estate =0 AND M.cwSign IN (1,2) AND M.CompanyId='$CompanyId' ORDER BY Date";
       $curDate="";
       $showToday=0;
       break;
     case "1232"://未出订单利润
       $CompanyId=$Info;
      $mySql="SELECT DISTINCT DATE_FORMAT(M.OrderDate,'%Y-%m') AS Date   
				FROM $DataIn.yw1_ordermain M 
				LEFT JOIN $DataIn.yw1_ordersheet S ON S.OrderNumber=M.OrderNumber 
				WHERE S.Estate>0 AND M.CompanyId='$CompanyId' ORDER BY Date";
       $curDate="";
       $showToday=0;
       break;
       case "1424"://非BOM采购单
      $mySql="SELECT DISTINCT DATE_FORMAT(M.Date,'%Y-%m') AS Date   
				FROM $DataIn.nonbom6_cgmain M   ORDER BY Date";
       $curDate="";
       $showToday=0;
       break;
 }
 if ($mySql!=""){
		$myResult = mysql_query($mySql);
		if($myRow = mysql_fetch_array($myResult))
		  {
		     do {
		                $Date=$myRow["Date"];
		                if ($Date!=$curDate) {
		                         $jsonArray[]=$Date;
		                }
		      }while($myRow = mysql_fetch_array($myResult));
		}
}
if ($showToday==1) {
	 $jsonArray[]=$curDate;
 }
?>