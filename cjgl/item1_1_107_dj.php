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


$ProcessNameArray = array();
$ProcessIdArray   = array();
$gxTypeIdArray    = array();
$SortIdArray      = array();
$BeforeArray       = array(); 
$ProcessResult=mysql_query("SELECT B.ProcessId ,PT.Color,PD.ProcessName,PD.gxTypeId,PT.SortId,B.BeforeProcessId 
	    FROM  $DataIn.yw1_scsheet  SC 
        INNER JOIN $DataIn.cg1_processsheet B  ON B.StockId = SC.StockId
        INNER JOIN $DataIn.process_data PD ON PD.ProcessId=B.ProcessId
        INNER JOIN $DataIn.process_type PT ON PT.gxTypeId=PD.gxTypeId
		WHERE B.StockId='$StockId' AND SC.sPOrderId = '$sPOrderId' GROUP BY B.ProcessId ORDER BY PT.SortId,B.Id",$link_id);
if($ProcessRow=mysql_fetch_array($ProcessResult)){
   do{
        $ProcessId          = $ProcessRow["ProcessId"];
        $ProcessNameArray[$ProcessId] = $ProcessRow["ProcessName"];
        $ProcessIdArray[]   = $ProcessRow["ProcessId"];
        $gxTypeIdArray[]    = $ProcessRow["gxTypeId"];
        $SortIdArray[]      = $ProcessRow["SortId"];  //前工序为0
        $BeforeArray[]       = $ProcessRow["BeforeProcessId"]; //约束工序
     }while($ProcessRow=mysql_fetch_array($ProcessResult));
}
$Counts=count($ProcessIdArray);
for($k=0;$k<$Counts;$k++){
  if($ProcessIdArray[$k]==$thisProcessId){
        $tempk=$k-1;

        if($gxTypeIdArray[$k] ==0){ //前工序 A B C D ...
	         $LastProcessName="<span class='redB'>前工序类型</span>";
	         $LastProcessId="";
	         $LastQty=$MaxQty; 
        }else{ 
	        
	        if($BeforeArray[$k]!=''){ //有约束工序,多道工序，取约束工序
	          
	            if($gxTypeIdArray[$k]==1){  //第一个工序取约束工序的生产最小值
		            $BeforeProcessId = $BeforeArray[$k];
		            
	            }else{ //否则取约束工序和前面一个工序的生产最小值
		            $BeforeProcessId = $BeforeArray[$k].",".$ProcessIdArray[$tempk];
		            
	            }
	            //约束工序名称
                $BeforeResult = mysql_query("SELECT ProcessId,ProcessName FROM $DataIn.process_data WHERE ProcessId IN ($BeforeProcessId) ORDER BY gxTypeId ",$link_id);
                $scQtyArray = array();
                while($BeforeRow = mysql_fetch_array($BeforeResult)){
                    $BeforeProcessName = $BeforeRow["ProcessName"];
                    $LastProcessName = $LastProcessName==""?$BeforeProcessName:$LastProcessName."<br>".$BeforeProcessName;
                    
                }
                //约束工序最低生产值  
                $BeforeProcessIdArray = explode(",", $BeforeProcessId);
                $BeforeCount = count($BeforeProcessIdArray);
                $BeforeProcessIdQtyArray = array();
                for($tempj=0;$tempj<$BeforeCount;$tempj++){
                    $thisBeforeProcessId = $BeforeProcessIdArray[$tempj];
	                $thisBeforeProcessRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty 
	                FROM $DataIn.sc1_gxtj C 
                    WHERE  C.StockId='$StockId' AND C.ProcessId='$thisBeforeProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
                    $thisBeforeProcessQty=$thisBeforeProcessRow["gxQty"]==""?0:$thisBeforeProcessRow["gxQty"];
                    $BeforeProcessIdQtyArray[] =$thisBeforeProcessQty;
	                
                }
                $LastQty = count($BeforeProcessIdQtyArray)==0?0:min($BeforeProcessIdQtyArray);        
  
	        }else{ //无约束工序，第一个工序取前工序的生产最小值
	          
		        if($gxTypeIdArray[$k]==1){
		        
			       $LastProcessId = "";
				   $BeforeResult = mysql_query("SELECT P.ProcessId,P.ProcessName FROM $DataIn.cg1_processsheet G 
				   LEFT JOIN $DataIn.process_data P ON P.ProcessId = G.ProcessId
				   WHERE G.StockId ='$StockId' AND  P.gxTypeId=0 ",$link_id);
	                $scQtyArray = array();
	                while($BeforeRow = mysql_fetch_array($BeforeResult)){
	                    $BeforeProcessName = $BeforeRow["ProcessName"];
	                    $LastProcessName = $LastProcessName==""?$BeforeProcessName:$LastProcessName."<br>".$BeforeProcessName;
	                    
	                }
	                //约束工序最低生产值  
	                $BeforeProcessIdArray = explode(",", $BeforeProcessId);
	                $BeforeCount = count($BeforeProcessIdArray);
	                $BeforeProcessIdQtyArray = array();
	                for($tempj=0;$tempj<$BeforeCount;$tempj++){
	                    $thisBeforeProcessId = $BeforeProcessIdArray[$tempj];
		                $thisBeforeProcessRow=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty 
		                FROM $DataIn.sc1_gxtj C 
	                    WHERE  C.StockId='$StockId' AND C.ProcessId='$thisBeforeProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
	                    $thisBeforeProcessQty=$thisBeforeProcessRow["gxQty"]==""?0:$thisBeforeProcessRow["gxQty"];
	                    $BeforeProcessIdQtyArray[] =$thisBeforeProcessQty;
		                
	                }
	                $LastQty = count($BeforeProcessIdQtyArray)==0?0:min($BeforeProcessIdQtyArray);	        
		        }else{   
			        $LastProcessId=$ProcessIdArray[$tempk];
	                $LastProcessName=$ProcessNameArray[$LastProcessId];  
	                $LastResult=mysql_fetch_array(mysql_query("SELECT IFNULL(SUM(C.Qty),0) AS gxQty 
	                FROM $DataIn.sc1_gxtj C 
                    WHERE  C.StockId='$StockId' AND C.ProcessId='$LastProcessId' AND C.sPOrderId='$sPOrderId'",$link_id));
                    $LastQty=$LastResult["gxQty"]==""?0:$LastResult["gxQty"];
		        } 
	        }
        }        
        break;
     }
}




$UnPQty=$gxQty-$thisScQty;
$LastQty="<span class='redB'>$gxQty</span>";

?>
<table width="800" height="221" border="0" cellpadding="0" cellspacing="0">
	<tr align="center" bgcolor="#d6efb5"> 
        <td height="60" colspan="3" class="A1011">半成品:<span class="yellowB"><?php echo $StuffCname?></span></td>
		<td    colspan="3" class="A1001">生产工序:<span class="yellowB"><?php echo $ProcessName?></span></td>
  </tr>

	<tr align="center" bgcolor="#d6efb5">
		<td width="100" height="50" class="A1111">&nbsp;</td>
		<td width="150" class="A1101">工单数量<br>(最低生产数)</td>
		<td width="190" class="A1101">约束工序</td>
		<td width="100" class="A1101">约束工序<br>最低生产数</td>
		<td width="100" class="A1101">本工序已生产</td>
		<td width="160" class="A1101">本次登记</td>
	</tr>
  <tr align="center">
    <td height="50" bgcolor="#d6efb5" class="A0111">数量<?php  echo $UnPQty;?></td>
  	<td class="A0101" bgcolor="#FFFFFF"><?php  echo $gxQty?></td>
    <td class="A0101" bgcolor="#FFFFFF"><span class="redB"><?php  echo $LastProcessName?></span></td>
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