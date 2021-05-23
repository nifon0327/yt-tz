<?php 
   //检查上月餐费确认
   include "../../basic/parameter.inc"; 
    $jsonArray=array();
    
    $Number=$_POST["Operator"]; 
     $tmpTime=strtotime("-1 month", time());
    $checkDate=date("Y-m",$tmpTime);//取得上个月日期
    //$checkDate="2013-07";
    //上月餐费总额
     $CheckResult0=mysql_fetch_array(mysql_query("SELECT Count(*) AS Sum,SUM(Amount) AS SumAmount FROM $DataPublic.ct_myorder WHERE Operator='$Number' AND DATE_FORMAT(Date,'%Y-%m')='$checkDate' AND Estate=0",$link_id));
      $SumAmount=$CheckResult0["SumAmount"]==""?0:$CheckResult0["SumAmount"];

    //已确定餐费总额
       $CheckmonthResult=mysql_fetch_array(mysql_query("SELECT IFNULL(Amount,0) AS Amount,Operator FROM $DataPublic.ct_monthamount WHERE Number='$Number' AND Month='$checkDate'",$link_id));
       $RegisterAmount=$CheckmonthResult["Amount"]==""?0:$CheckmonthResult["Amount"];

        if($SumAmount>$RegisterAmount ){
                 $tmpArray=explode("-", $checkDate);
                 $checkDate=$tmpArray[0] . "年" . $tmpArray[1] . "月";
                  $jsonArray=array("$checkDate","$SumAmount 元");
         }

       echo json_encode($jsonArray); 	
?>