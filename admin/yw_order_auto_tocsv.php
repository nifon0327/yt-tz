<?php   
include "D:/website/mc/basic/parameter.inc";
function GetWeekToDate($Weeks,$dateFormat,$dec=0)
 {
       $year=substr($Weeks, 0,4);
	   $week=substr($Weeks, 4,2);
       $timestamp = mktime(0,0,0,1,1,$year);
       $dayofweek = date("w",$timestamp);
       if( $week != 1){
                $distance = ($week-1)*7-$dayofweek+1;
       }
      else{
	          $distance=$dayofweek-5;
      }
      $passed_seconds = $distance * 86400;
      $timestamp += $passed_seconds;  
      $firt_date_of_week = date("$dateFormat",$timestamp);
      $distance =6-$dec;
      $timestamp += $distance * 86400;
      $last_date_of_week = date("$dateFormat",$timestamp);
      
      return array($firt_date_of_week,$last_date_of_week);
 }

$ClientSTR="and M.CompanyId=1074";
$OrderBY="order by M.OrderDate desc";
$mySql="SELECT S.OrderPO,M.OrderDate,S.ShipType,S.DeliveryDate,S.Qty,S.Price,S.PackRemark,P.cName,P.eCode,P.Unit,P.Description,C.Forshort  ,PI.Leadtime,S.POrderId
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber  
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
LEFT JOIN $DataIn.yw3_pisheet PI ON PI.oId=S.Id
LEFT JOIN $DataIn.trade_object C ON M.CompanyId=C.CompanyId 
where 1 $ClientSTR  $SearchRows and S.Estate!='0' $OrderBY";

$result = mysql_query($mySql,$link_id);	
$str = "PO,Product code,QTY,Delivery date\n";   
$str = iconv('utf-8','gb2312',$str); 
if($myrow = mysql_fetch_array($result)){
	$i=1;
        $Forshort=$myrow["Forshort"];
	do{
	 	$OrderPO=$myrow["OrderPO"];
		$eCode=$myrow["eCode"];
		$Description=$myrow["Description"];
       $POrderId=$myrow["POrderId"];
	  	$Qty=$myrow["Qty"];
		$Leadtime=$myrow["Leadtime"];
		if ($Leadtime==""){
			 $checkTimeResult=mysql_fetch_array(mysql_query("SELECT Leadtime FROM $DataIn.yw3_pileadtime WHERE POrderId='$POrderId'",$link_id));
			 $Leadtime=$checkTimeResult["Leadtime"]==""?"":$checkTimeResult["Leadtime"];
			 $LeadbgColor=$checkTimeResult["Leadtime"]==""?$LeadbgColor:" bgColor='#F7E200' ";
		}
      if ($Leadtime!="" && $Leadtime!="" ){
         if ($curWeeks==""){
	          $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
              $curWeeks=$dateResult["CurWeek"];
         }
	      $Leadtime=str_replace("*", "", $Leadtime);
	      $dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK('$Leadtime',1) AS PIWeek",$link_id));
          $PIWeek=$dateResult["PIWeek"];
    
         if ($PIWeek>0){
	          $week=substr($PIWeek, 4,2);
		      $dateArray= GetWeekToDate($PIWeek,"m/d");
		      $weekName="Week " . $week;
		      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
		      
		      $PI_Color=($PIWeek<=$curWeeks && $PI_NoColor==0)?"#FF0000":"#000000";
		      $Leadtime=$weekName;
	      }
      }
                //去除换行符
                $char_change = array("\r\n", "\n", "\r");   
                $Description=str_replace($char_change,'', $Description);
              $str .= $OrderPO.",".$eCode.",".$Qty.",".$Leadtime."\n"; //用引文逗号分开 
                $i++;	
        }while ($myrow = mysql_fetch_array($result));
}
$filename=$Forshort . "_" . date('Ymd');
$myfile="D:/website/mc/client/strax/".$filename.".csv";
  if (file_exists($myfile)) {
    unlink ($myfile);
  }
$handle=fopen("D:/website/mc/client/strax/".$filename.".csv","a+");
 fwrite($handle,$str);
?>
