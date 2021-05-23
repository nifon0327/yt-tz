<?php 
//OK
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取产品资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";

$CheckSql=mysql_query("SELECT A.StuffCname ,SC.Qty AS OrderQty
FROM  $DataIn.yw1_scsheet SC 
INNER JOIN $DataIn.cg1_semifinished M ON M.StockId = SC.StockId
INNER JOIN $DataIn.cg1_stocksheet G ON G.StockId = M.mStockId
LEFT JOIN $DataIn.stuffdata A ON A.StuffId=G.StuffId 
WHERE  SC.StockId='$StockId' AND SC.sPOrderId = '$sPOrderId'",$link_id);
if($CheckRow=mysql_fetch_array($CheckSql)){
	$StuffCname=$CheckRow["StuffCname"];
	$OrderQty=$CheckRow["OrderQty"];		
}


 //已完成的工序数量
$CheckthisScQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE   C.StockId='$StockId' AND C.ProcessId='$thisProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
$thisScQty=$CheckthisScQty["gxQty"]==""?0:$CheckthisScQty["gxQty"];

//本道工序
$ProcessResult=mysql_fetch_array(mysql_query("SELECT  * FROM $DataIn.process_data  WHERE ProcessId=$thisProcessId",$link_id));
$ProcessName=$ProcessResult["ProcessName"];
$LastProcessNameArray=array();
$LastProcessIdArray=array();


$OrderbyResult=mysql_query("SELECT B.ProcessId AS LastProcessId,PT.Color,PD.ProcessName AS LastProcessName 
	    FROM  $DataIn.yw1_scsheet  SC 
        INNER JOIN $DataIn.cg1_processsheet B  ON B.StockId = SC.StockId
        INNER JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
        INNER JOIN $DataIn.process_type PT ON PT.gxTypeId=PD.gxTypeId
		WHERE B.StockId='$StockId' AND SC.sPOrderId = '$sPOrderId' GROUP BY B.ProcessId ORDER BY PT.SortId,B.Id",$link_id);
if($OrderbyRow=mysql_fetch_array($OrderbyResult)){
   do{
        $LastProcessNameArray[]=$OrderbyRow["LastProcessName"];
        $LastProcessIdArray[]=$OrderbyRow["LastProcessId"];
     }while($OrderbyRow=mysql_fetch_array($OrderbyResult));
}
$Counts=count($LastProcessIdArray);
for($k=0;$k<$Counts;$k++){
  if($LastProcessIdArray[$k]==$thisProcessId){
        $tempk=$k-1;
        if($tempk<0){
                 $LastProcessName="<span class='redB'>此工序为第一道</span>";
                 $LastProcessId="";
                 $LastQty=$MaxQty;
                 }
         else{
                 $LastProcessId=$LastProcessIdArray[$tempk];
                 $LastProcessName=$LastProcessNameArray[$tempk];
             }
        break;
     }
}
//上一道工序完成的数量
    if($LastProcessId!=""){
              $LastResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE  C.StockId='$StockId' AND C.ProcessId='$LastProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
              $LastQty=$LastResult["gxQty"]==""?0:$LastResult["gxQty"];
    }

$CheckthisGxQty=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty FROM $DataIn.sc1_gxtj C WHERE   C.StockId='$StockId' AND C.ProcessId='$thisProcessId' AND C.sPOrderId='$sPOrderId' ",$link_id));
$thisgxQty=$CheckthisGxQty["gxQty"]==""?0:$CheckthisGxQty["gxQty"];

$UnPQty=$LastQty-$thisgxQty;
$LastQty="<span class='redB'>$LastQty</span>";

?>
<table width="800" height="221" border="0" cellpadding="0" cellspacing="0">
	<tr align="center" bgcolor="#d6efb5">
        <td height="60" colspan="3" class="A1011">半成品:<span class="yellowB"><?php echo $StuffCname?></span></td>
		<td    colspan="3" class="A1001">本道生产工序:<span class="yellowB"><?php echo $ProcessName?></span></td>
  </tr>

	<tr align="center" bgcolor="#d6efb5">
		<td width="249" height="50" class="A1111">&nbsp;</td>
		<td width="259" class="A1101">订单数量<br>(最低生产数)</td>
		<td width="210" class="A1101">约束工序</td>
		<td width="210" class="A1101">约束工序生产数量</td>
		<td width="210" class="A1101">本工序已生产</td>
		<td width="210" class="A1101">本次登记</td>
	</tr>
  <tr align="center">
    <td height="50" bgcolor="#d6efb5" class="A0111">数量</td>
  	<td class="A0101" bgcolor="#FFFFFF"><?php  echo $gxQty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><?php  echo $LastProcessName?></td>
    <td class="A0101" bgcolor="#FFFFFF"><?php  echo $LastQty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><span class="greenB"><?php echo $thisScQty?></span></td>
    <td class="A0101" bgcolor="#FFFFFF"><input name="Qty" type="text" class="I0000C" id="Qty" value="这里输入生产数量" size="15" onfocus="ClearStr()"><input id="UnPQty" name="UnPQty" type="hidden" value="<?php echo $UnPQty?>"></td>
  </tr>
  <tr>
    <td height="61" colspan="6"  align="center" class="A0000" bgcolor="#d6efb5">
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="提交" onclick="ToSaveGxDj(this,<?php  echo "$sPOrderId,$StockId,$thisProcessId,$LastPos"; ?>)"></td>
      </tr>
    </table></td>
  </tr>
</table>