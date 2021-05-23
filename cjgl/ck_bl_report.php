<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php 
//仓库备料
include "../basic/chksession.php" ;
include "../basic/parameter.inc";
include "../model/modelfunction.php";
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
echo "<link rel='stylesheet' href='../cjgl/lightgreen/read_line.css'>";
echo "<style>
 tr,td{
	  font-size:9px;
	  color:#000000;
}
.sp{
     border:1px dotted #BBB;
     width:15px;
     height:15px;
     border-radius:4px;
}
#newsImg {margin: 0px auto; width: 120px; text-align: left;}
#newsImg ul {margin-left:-42px;}
#newsImg li { FLOAT:left;list-style-type:none;}
#newsImg img {display: block;width: 54px; height:78px;}
</style>";
echo "<SCRIPT src='../model/pagefun.js' type=text/javascript></script>";
echo "<body>";
echo "<center>";
$dateResult = mysql_fetch_array(mysql_query("SELECT YEARWEEK(NOW(),1) AS CurWeek",$link_id));
$curWeek=$dateResult["CurWeek"];

$QtyResult=mysql_query("SELECT (S.AddQty+S.FactualQty) AS Qty,D.StuffCname AS cName,Y.OrderPO,YEARWEEK(S.DeliveryDate,1)  AS Weeks  
FROM $DataIn.cg1_stocksheet S 
LEFT JOIN $DataIn.yw1_ordersheet Y ON Y.POrderId=S.POrderId 
LEFT JOIN $DataIn.stuffdata D ON D.StuffId=S.StuffId 
WHERE S.POrderId='$POrderId' AND S.StockId='$mStockId' ",$link_id); 
if($QtyRow=mysql_fetch_array($QtyResult)){
  $OrderPO=$QtyRow["OrderPO"];
  $cName=$QtyRow["cName"];
  $PQty=$QtyRow["Qty"];
  $Weeks=$QtyRow["Weeks"];
  if ($Weeks>0){
     $WeekColor=$curWeek>=$Weeks?"#FF0000":"#000000";
      //$WeekName= substr($Weeks,4,2);
      $week=substr($Weeks, 4,2);
      $dateArray= GetWeekToDate($Weeks,"m/d");
      $dateSTR=$dateArray[0] . "-" .  $dateArray[1];
      
      $WeekName= "<ul><li><img src='../images/lcd/lcd_b" . substr($Weeks,4,1) . ".png'></li><li><img src='../images/lcd/lcd_b" . substr($Weeks,5,1) . ".png' style='margin-left:-3px;'></li></ul>";
      $WeekName="<div  id='newsImg'>$WeekName</div>";
       $dateSTR="<div  style='color:#000000;font-family: Arial;Font-size:18px;'> $dateSTR</div>";
  }
}
$today=date("Y/m/d");

$signTable="<table width='500' cellspacing='0' border='1' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;border-radius:8px;margin-top:12px;page-break-after:avoid;' align='center'>
<tr >
        <td height='20' class='A0001'>&nbsp;&nbsp;开单</td>
        <td class='A0001'>&nbsp;&nbsp;备料</td>
        <td class='A0001'>&nbsp;&nbsp;品检</td>
        <td>&nbsp;&nbsp;出货</td>
        </tr>
<tr >
        <td height='20' class='A0001'>&nbsp;</td>
        <td class='A0001'>&nbsp;</td>
        <td class='A0001'>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
</table>";



echo "<table width='620' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' style='margin-top:20px;text-align:left;' >
          <tr>
          <td rowspan='3'  width='120'>$WeekName$dateSTR</td>
            <td  colspan='3' style='font-size:16px;line-height:24px;' height='50'>$signTable</td>
          </tr>
          <tr>
              <td  colspan='3' style='font-size:16px;line-height:24px;' height='25'>$cName</td>
          </tr>
          <tr >
          <td width='160' height='20' style='font-size:12px;'>订单数量: $PQty</td>
          <td width='200' style='font-size:12px;'>订单流水号: $POrderId</td>
          <td width='140' style='font-size:12px;'>订单PO: $OrderPO</td>
          </tr>
          </table> ";

 $TableSTR="
				<table width='620' cellspacing='0' border='0' style='TABLE-LAYOUT: fixed; WORD-WRAP: break-word;margin-top:10px;' align='center'>
				<tr  bgcolor='#33CCCC'>
				<td class='A0100' height='30' align='center' width='30'>序号</td>
				<td class='A0100' align='center' width='300'>配件名称</td>
				<td class='A0100' width='50' align='center' >单位</td>
				<td class='A0100' width='50' align='center' >订单数</td>
				<td class='A0100' width='50' align='center'>在库</td>
				<td class='A0100' width='80' align='center'>备料数</td>
				<td class='A0100' width='60' align='center'>备料确认</td>
				</tr>";

$i=1;	
// $sTypeSTR="AND NOT EXISTS (SELECT P.StuffId FROM  $DataIn.stuffproperty P WHERE P.property=1 AND P.StuffId=A.StuffId) ";		
$ListResult = mysql_query("SELECT S.Id,G.StockId,S.StuffId,G.OrderQty,A.StuffCname,U.Name AS Unit ,K.tStockQty,P.Property
            FROM $DataIn.cg1_semifinished G 
			LEFT JOIN $DataIn.cg1_stocksheet S ON S.StockId=G.StockId 
			LEFT JOIN $DataIn.stuffdata A ON A.StuffId=S.StuffId
			LEFT JOIN $DataIn.stufftype ST ON ST.TypeId=A.TypeId 
			LEFT JOIN $DataIn.stuffmaintype SM ON SM.Id=ST.mainType 
	        LEFT JOIN $DataPublic.stuffunit U ON U.Id=A.Unit  
	        LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=S.StuffId 
            LEFT JOIN $DataIn.stuffproperty  P  ON P.StuffId=A.StuffId  AND  Property=10 
			WHERE G.mStockId='$mStockId' AND S.POrderId='$POrderId'  AND SM.blSign=1   
			ORDER BY P.Property,G.StockId",$link_id);
				
while($ListRow=mysql_fetch_array($ListResult)){
               $StockId=$ListRow["StockId"]; 
			   $StuffId=$ListRow["StuffId"];
			   $StuffCname=$ListRow["StuffCname"];
			   $OrderQty=$ListRow["OrderQty"];
			   $OrderQty=($OrderQty-floor($OrderQty))>0?number_format($OrderQty,1):number_format($OrderQty,0);
			   $Unit=$ListRow["Unit"];
			   $tStockQty=$ListRow["tStockQty"];
			   $tStockQty=($tStockQty-floor($tStockQty))>0?number_format($tStockQty,1):number_format($tStockQty,0);
			   
			   $checkllResult=mysql_query("SELECT SUM(Qty) AS llQty,sum(case  when Estate=1 then Estate  else 0 end) as llEstate  FROM $DataIn.ck5_llsheet WHERE StockId='$StockId'",$link_id);
			   if ($checkllRow=mysql_fetch_array($checkllResult)){
				   $llQty=$checkllRow["llQty"];
				   $llEstate=$checkllRow["llEstate"];
				   $llQty=($llQty-floor($llQty))>0?number_format($llQty,1):number_format($llQty,0);
				   $llEstate=$llEstate>0?"★":"";
			   }
			  else{
				  $llQty="&nbsp;";$llEstate="";
			  }
			    $Property=$ListRow["Property"];
                if($Property==10)$showStr="<span class='blueB'>按桶/瓶领</span>";
                else $showStr="";
			    $TableSTR.="<td  class='A0100' align='center' height='20'>$i</td>
                         <td  class='A0100'>$StuffCname</td>
						 <td  class='A0100' align='center'>$Unit</td>
						 <td align='right'  class='A0100' >$OrderQty</td>
						 <td align='right' class='A0100'>$tStockQty</td>
						 <td align='left' class='A0100'><span style='margin-left:20px;'>&nbsp;</span>$llEstate $llQty</td>
						<td align='center' class='A0100'>$showStr</td>
						</tr>";
                $i++;
				 
}

$TableSTR.="</table>";
echo $TableSTR;
echo "</body>";
echo "</html>";
?>