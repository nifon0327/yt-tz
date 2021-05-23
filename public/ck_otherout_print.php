<?php   
include "../model/modelhead.php";
//读取配件库存
$SearchRows="";
if($chooseDate!=""){
	//$SearchRows=" and  DATE_FORMAT(F.Date,'%Y-%m')='$chooseDate'";
	}
	
$Lens=count($checkid);
for($i=0;$i<$Lens;$i++){
	$Id=$checkid[$i];
	if ($Id!=""){
		$Ids=$Ids==""?$Id:($Ids.",".$Id);
		}
	}
	
$SearchRows.=" AND F.Id IN ($Ids) AND F.OutSign=2";

$result = mysql_query("SELECT F.Id,F.StuffId,F.Qty,F.Remark,F.Type,F.Date,F.Estate,F.Locks,F.Operator,D.StuffCname,K.tStockQty,K.oStockQty,D.Price,D.Price*F.Qty AS Amount,D.Picture,U.Name AS UnitName,C.TypeName,C.TypeColor ,F.Bill,F.DealResult
FROM $DataIn.ck8_bfsheet F
LEFT JOIN $DataIn.stuffdata D ON F.StuffId=D.StuffId 
LEFT JOIN $DataIn.stuffunit U ON U.Id=D.Unit
LEFT JOIN $DataIn.ck9_stocksheet K ON K.StuffId=D.StuffId 
LEFT JOIN $DataIn.ck8_bftype  C ON C.id=F.Type
WHERE 1 $SearchRows  ",$link_id);

?>
<link rel="stylesheet" href="../model/style/orderprint.css">
</head>
<script LANGUAGE="JavaScript">
function window.onload() {
	factory.printing.header ="";
  	factory.printing.footer ="";
  	factory.printing.portrait = true ;//纵向,false横向
	factory.printing.leftMargin =5;
  	factory.printing.topMargin = 1.5;
  	factory.printing.rightMargin =5;
  	factory.printing.bottomMargin = 0.5;
	}
//  End -->
</script>
<body lang=ZH-CN>
<object id="factory" viewastext  style="display:none"
  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  codebase="http://www.middlecloud.com/basic/smsx.cab#Version=6,2,433,70">
</object>
<table width="740" height="116" cellpadding="1"  cellspacing="0">
  <tr valign="top">
    <td height="16" colspan="13" scope="col"><div align="center" class="style1">未出明细列表</div></td>
  </tr>
  <tr>
    <td height="21" colspan="3" class="A0100">PAGE-1</td>
    <td height="21" colspan="4" class="A0100" align="center">&nbsp;</td>
    <td class="A0100" colspan="2" align="right">列印日期:<?php    echo date("Y年m月d日")?></td>
  </tr>
  <tr>
    <td height="8" width="26"  class="A0111">&nbsp;</td>    
    <td width="60" class="A0101" align="center">出库日期</td>
    <td width="50" class="A0101" align="center">配件ID</td>
    <td width="238"  class="A0101" align="center">配件名称</td>
	<td width="45"  class="A0101" align="center">在库</td>
	<td width="45" class="A0101" align="center">可用库存</td>
    <td width="45" class="A0101" align="center">出库数量</td>
    <td width="170" class="A0101" align="center">出库原因</td>
	 <td width="50" class="A0101" align="center">操作</td>
  </tr>
  <?php   
  if($myRow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	$SUMAMOUNT=0;
	do{
			$Id=$myRow["Id"];
			$StuffId=$myRow["StuffId"];
			$StuffCname=$myRow["StuffCname"];
			$UnitName=$myRow["UnitName"]==""?"&nbsp;":$myRow["UnitName"];
			$Qty=$myRow["Qty"];
			$Price=$myRow["Price"];
			$Amount=sprintf("%.0f",$myRow["Amount"]);
			$SumQty+=$Qty;
			$SumAmount+=$Amount;
			$tStockQty=$myRow["tStockQty"];
			$oStockQty=$myRow["oStockQty"];
			$Remark=$myRow["Remark"]==""?"&nbsp":$myRow["Remark"];		
			$Date=$myRow["Date"];
			$Estate=$myRow["Estate"]!=1?"<div class='greenB'>已核</div>":"<div class='redB'>未核</div>";
			$Operator=$myRow["Operator"];
			$Picture=$myRow["Picture"];
			include "../model/subprogram/staffname.php";
			$d=anmaIn("download/stufffile/",$SinkOrder,$motherSTR);		
			//检查是否有图片
			include "../model/subprogram/stuffimg_model.php";
			$Locks=$myRow["Locks"];
			$Type=$myRow["Type"];
			$TypeName=$myRow["TypeName"];
			$TypeColor =$myRow["TypeColor"];
			$TypeName="<span style=\"color:$TypeColor \">$TypeName</span>";
	        $OrderQtyInfo="<a href='cg_historyorder.php?StuffId=$StuffId&Id=' target='_blank'>查看</a>";
	       $DealResult=$myRow["DealResult"]==""?"&nbsp;":$myRow["DealResult"];
	

		  echo"<tr>";
		  echo"<td  height='25' class='A0111' align='center'>$ColSign$i</td>";		
		  echo"<td class='A0101' align='center'>$Date</td>";
		  echo"<td class='A0101' align='center'>$StuffId</td>";
		  echo"<td class='A0101' width='238'>$StuffCname</td>";
		  echo"<td class='A0101' align='right'>$tStockQty</td>";
		  echo"<td class='A0101' align='right'>$oStockQty</td>";
		  echo"<td class='A0101' align='right'>$Qty</td>";
		  echo"<td class='A0101'>$Remark</td>";
		  echo"<td class='A0101' align='center'>$Operator</td>";
		  echo"</tr>";
		 $i++; 
		}while ($myRow = mysql_fetch_array($result));
	}
?>  
</table>
</body>
</html>
