<?php   
//电信-zxq 2012-08-01
header("Content-Type: text/html; charset=utf-8");
header("expires:mon,26jul199705:00:00gmt");
header("cache-control:no-cache,must-revalidate");
header("pragma:no-cache");
//读取入库资料
include "../basic/parameter.inc";
include "../model/modelfunction.php";
$upSql=mysql_query("SELECT R.StockId,R.Qty,R.StuffId,R.llQty AS outQty,
D.StuffCname,D.SeatId,
S.FactualQty,S.AddQty,K.tStockQty 
FROM $DataIn.ck1_rksheet R 
LEFT JOIN $DataIn.stuffdata D ON R.StuffId=D.StuffId 
LEFT JOIN $DataIn.cg1_stocksheet S ON R.StockId=S.StockId 
LEFT JOIN $DataIn.ck9_stocksheet K ON R.StuffId=K.StuffId 
WHERE R.Id=$Id ORDER BY R.Id DESC",$link_id); 
if($upData = mysql_fetch_array($upSql)){
	$StuffId=$upData["StuffId"];
	$StockId=$upData["StockId"];
	$Qty=$upData["Qty"];
	$FactualQty=$upData["FactualQty"];
	$AddQty=$upData["AddQty"];
	$StuffCname=$upData["StuffCname"];
	$SeatId = $upData["SeatId"];
	$tStockQty=$upData["tStockQty"];
	$RealQty=$FactualQty+$AddQty;
	
	$outQty=$upData["outQty"]; //出库数量
	//收货情况				
	$Receive_Temp=mysql_query("SELECT SUM(Qty) AS rkQty FROM $DataIn.ck1_rksheet WHERE StockId='$StockId'",$link_id);; 
	$rkQty=mysql_result($Receive_Temp,0,"rkQty");
	$rkQty=$rkQty==""?0:$rkQty;
	$MantissaQty=$RealQty-$rkQty;  
	
	$lastQty =  $Qty - $outQty;
	
	if($tStockQty==0){
		$tStockQtyINFO="<span class='redB'>没有在库,不可做减少入库数量的操作.</span>";		
		}
	else{
		$OperatorsSTR="<option value='-1'>减少</option>";
		}
		
	if($MantissaQty==0){
		$MantissaQtyINFO="<span class='redB'>已全部收货,不可增加入库数量.</span>";
		}
	else{
		$OperatorsSTR.=" <option value='1'>增加</option>";
		}


    $Sql=mysql_query("select Inventory.*,stuffdata.StuffCname, stuffunit.Id,stuffunit.name from
    (
        select Inset.StuffId,Inset.TaskId,Inset.LocationId,(Inset.TotalInQty- IFNULL(Outset.TotalOutQty,0)) as LeftQty
from (SELECT I.StuffId,I.TaskId,I.LocationId,sum(I.TaskQty) as TotalInQty 
FROM wms_taskin I where I.TaskStatus<=10 GROUP BY I.StuffId,I.TaskId) as Inset
 left outer join
    (SELECT O.StuffId,O.TaskId,O.LocationId,sum(O.TaskQty) as TotalOutQty 
FROM wms_taskout O where O.TaskStatus<=10 GROUP BY O.StuffId,O.TaskId) as Outset
ON Inset.StuffId= Outset.StuffId and Inset.TaskId= Outset.TaskId
UNION
Select Outset.StuffId,Outset.TaskId,Outset.LocationId,-Outset.TotalOutQty
from
(SELECT O.StuffId,O.TaskId,O.LocationId,sum(O.TaskQty) as TotalOutQty 
FROM wms_taskout O where O.TaskStatus<=10 GROUP BY O.StuffId,O.TaskId ) as Outset
where not exists(Select Inset.StuffId from
    (SELECT I.StuffId as StuffId,I.TaskId,I.LocationId,sum(I.TaskQty) as TotalInQty 
FROM wms_taskin I where I.TaskStatus<=10 GROUP BY I.StuffId,I.TaskId) as Inset
where Inset.StuffId= Outset.StuffId)
) as Inventory,stuffdata,stuffunit
where Inventory.StuffId=stuffdata.StuffId and stuffdata.Unit=stuffunit.Id and StockId='$StockId' ",$link_id);
if($Data = mysql_fetch_array($Sql)){
    $LeftQty = $Data["LeftQty"];
    $TaskId = $Data["TaskId"];
    $LocationId = $Data["LocationId"];
}



	}
?>
  <table width="800" height="100" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
    <tr align="center" bgcolor="#d6efb5">
    <td width="100" height="50" class="A1111">流水号<input name="Id" type="hidden" id="Id" value="<?php    echo $Id?>"></td>
    <td width="60" class="A1101">配件ID</td>
    <td width="340" class="A1101">配件名称</td>
    <td width="60" class="A1101">需求数量</td>
    <td width="60" class="A1101">增购数量</td>
    <td width="60" class="A1101">实购数量</td>
    <td width="60" class="A1101">未收数量</td>
    <td width="60" class="A1101">在库</td>
  </tr>
  <tr align="center">
    <td height="50" class="A0111"><?php    echo $StockId?></td>
    <td class="A0101"><?php    echo $StuffId?></td>
    <td class="A0101"><?php    echo $StuffCname?></td>
    <td class="A0101"><?php    echo $FactualQty?></td>
    <td class="A0101"><?php    echo $AddQty?></td>
    <td class="A0101"><?php    echo $RealQty?></td>
    <td class="A0101"><?php    echo $MantissaQty?></td>
    <td class="A0101"><?php    echo $tStockQty?></td>
  </tr>
</table>
<br/>
<table width="800" height="111" border="0" cellpadding="0" cellspacing="0">
  <tr align="center">
    <td width="100" height="50" bgcolor="#d6efb5" class="A0111">本次入库</td>
  	<td width="60" bgcolor="#FFFFFF" class="A0101"><?php    echo $Qty?></td>
  	<td width="80" bgcolor="#d6efb5" class="A1101">出库数量</td>
  	<td width="60" bgcolor="#FFFFFF" class="A0101"><?php    echo $outQty?></td>
    <td width="80" bgcolor="#d6efb5" class="A1101">操作</td>
    <td width="240" bgcolor="#FFFFFF" class="A0101">
    <?php
	  $QtyChange="UpdateRk($Id,$Qty,$tStockQty,$MantissaQty,$outQty);";
	  if($OperatorsSTR==""){
	  	echo"<div class='redB'>条件不足,不能更新.</div>";
		}
	  else{
          echo"<select name='Operators' id='Operators'>$OperatorsSTR</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input name='changeQty' type='text' class='INPUT0100' id='changeQty' size='10' onkeyup=\"this.value=this.value.replace(/[^\d.]/g, '').replace(/^\./g, '').replace('.', 'A').replace(/\./g, '').replace('A', '.').replace(/^(\d+)\.(\d\d).*$/, '$1.$2')\" onblur=\"this.value=this.value.replace(/\.$/g, '')\"  onafterpaste=\"this.value=this.value.replace(/[^\d.]/g, '').replace(/^\./g, '').replace('.', 'A').replace(/\./g, '').replace('A', '.').replace(/^(\d+)\.(\d\d).*$/, '$1.$2')\" >";
      	}
	?>
	 </td>
     <td width="200" bgcolor="#d6efb5" class="A1101" colspan="2"><?php    echo $tStockQtyINFO . $MantissaQtyINFO?></td>
  </tr>
    <tr align="center">
        <td  height="50" bgcolor="#d6efb5" class="A0111">库位编号</td>
        <td bgcolor="#FFFFFF" class="A0101" colspan="3"><?php    echo $SeatId ?></td>
        <td  bgcolor="#d6efb5" class="A1101">包装编号</td>
        <td  bgcolor="#FFFFFF" class="A0101"><?php    echo $TaskId ?></td>
        <td  bgcolor="#d6efb5" class="A1101" width="70">数量</td>
        <td  bgcolor="#FFFFFF" class="A1101"><?php    echo $LeftQty ?></td>
    </tr>
  <tr>
    <td height="61" colspan="8"  align="center" class="A0000" bgcolor="#d6efb5">
	<table border="0" cellpadding="0" cellspacing="8" align="right" width="100%">
      <tr align="center">
	   <td class="A0000" height="45" id="InfoBack" >&nbsp;</td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="取消" onclick="closeMaskDiv()"></td>
        <td width="20">&nbsp;</td>
        <td width="80"><input type="button" name="Submit" value="提交" onclick="<?php    echo $QtyChange?>"></td>
      </tr>
    </table></td>
  </tr>
</table>
