<?php   
//电信-EWEN
include "../model/modelhead.php";
//读取配件库存值
$ClientSTR=$SearchRows==""?"and M.CompanyId='$CompanyId'":"";
$OrderBY="order by  M.CompanyId,M.OrderDate ASC,M.Id DESC";
$TypeIdSTR="";
if($TypeId!=""){
	$TypeIdSTR=" AND P.TypeId='$TypeId'";
	}
//读取客户简称
$checkClient=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
$Forshort=$checkClient["Forshort"];
$result = mysql_query("SELECT S.OrderPO,M.OrderDate,S.POrderId,S.ShipType,S.DeliveryDate,S.Qty,S.Price,S.PackRemark,P.cName,P.ECode,P.Unit 
FROM $DataIn.yw1_ordermain M 
LEFT JOIN $DataIn.yw1_ordersheet S ON M.OrderNumber=S.OrderNumber  
LEFT JOIN $DataIn.productdata P ON S.ProductId=P.ProductId  
where 1 $ClientSTR $TypeIdSTR $SearchRows and S.Estate!='0' $OrderBY",$link_id);
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
    <td height="16" colspan="12" scope="col"><div align="center" class="style1">未出明细列表</div></td>
  </tr>
  <tr>
    <td height="21" colspan="3" class="A0100">PAGE-1</td>
    <td height="21" colspan="3" class="A0100" align="center">客户:<?php    echo $Forshort?></td>
    <td colspan="6" class="A0100" align="right">列印日期:<?php    echo date("Y年m月d日")?></td>
  </tr>
  <tr>
    <td width="15"  class="A0111">&nbsp;</td>
  
    <td width="54" class="A0101" align="center">PO</td>
    <td width="190" class="A0101">中文名</td>
    <td width="25" class="A0101" align="center">售价</td>
    <td width="29" class="A0101" align="center">数量</td>
    <td width="32" class="A0101" align="center">总额</td>
    <td width="170"  class="A0101" align="center">包装</td>
	<td width="40" class="A0101" align="center">出货方式</td>
    <td width="57" class="A0101" align="center">出货时间</td>
    <td width="66" class="A0101" align="center">订单日期</td>
  </tr>
  <?php   
  if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	$SUMAMOUNT=0;
	do{
		  $POrderId=$myrow["POrderId"];
		  $Client=$myrow["Forshort"];
		  $OrderPO=$myrow["OrderPO"]==""?"&nbsp;":$myrow["OrderPO"];
		  $ShipType=$myrow["ShipType"];
		  $DeliveryDate=$myrow["DeliveryDate"];
		  if($DeliveryDate=="0000-00-00"){
		  	if($CompanyId=="1003"){
				$DeliveryDate=$ShipType;
				}
		 	else{
				$DeliveryDate="no delivery";
			 	}
			}
		  $cName=$myrow["cName"];
		  $Price=$myrow["Price"]; 
		  $Qty=$myrow["Qty"]; 
		  $SUMQTY=$SUMQTY+$Qty;
		  $PackRemark=$myrow["PackRemark"]==""?"&nbsp":$myrow["PackRemark"]; 
		  $OrderDate=$myrow["OrderDate"]; 
		  $Dates=substr($Date,5,5);
		  $Amount=sprintf("%.2f",$Price*$Qty);
		  $SUMAMOUNT=sprintf("%.2f",$SUMAMOUNT+$Amount);
		  $OrderDate=$OrderDate."/".CountDays($OrderDate,0);
		$checkExpress=mysql_query("SELECT Type FROM $DataIn.yw2_orderexpress WHERE POrderId='$POrderId' AND Type=1 ORDER BY Id LIMIT 1",$link_id);
		if($checkExpressRow = mysql_fetch_array($checkExpress)){
			$ColSign="★";
			}
		else{
			$ColSign="";
			}
		//出货方式
	   if (strlen(trim($ShipType))>0){
		    $ShipType="<image src='../images/ship$ShipType.png' style='width:20px;height:20px;'/>";
	    }
	    
		  echo"<tr>";
		  echo"<td class='A0111' align='center'>$ColSign$i</td>";
		
		  echo"<td class='A0101'>$OrderPO</td>";
		  echo"<td class='A0101'>$cName</td>";
		  echo"<td class='A0101'><div align='right'>$Price</td>";
		  echo"<td class='A0101'><div align='right'>$Qty</td>";
		  echo"<td class='A0101'><div align='right'>$Amount</td>";
		  echo"<td class='A0101'>$PackRemark</td>";
		  echo"<td class='A0101' align='center'>$ShipType</td>";
		  echo"<td class='A0101'>$DeliveryDate</td>";
		  echo"<td class='A0101'>$OrderDate</td>";
		  echo"</tr>";
		  if($i%75==0){
		  $Page++;
		  $Date=date("Y年m月d日");
		  echo"</table>";
		  echo"<div style='PAGE-BREAK-AFTER: always'></div>";		  
		  //表头
		echo"<table width='740' height='116' cellpadding='1'  cellspacing='0'>
		  <tr valign='top'>
			<td height='16' colspan='12' scope='col'><div align='center' class='style1'>未出明细列表</div></td>
		  </tr>
		  <tr>
			<td height='21' colspan='3' class='A0100'>PAGE-1</td>
			<td height='21' colspan='3' class='A0100' align='center'>客户:</td>
			<td colspan='6' class='A0100' align='right'>列印日期:$Date</td>
		  </tr>
		  <tr>
			<td width='15'  class='A0111'>&nbsp</td>
			
			<td width='54' class='A0101'><div align='center'>PO</div></td>
			<td width='190' class='A0101'>中文名</td>
			<td width='25' class='A0101'><div align='center'>售价</div></td>
			<td width='29' class='A0101'><div align='center'>数量</div></td>
			<td width='32' class='A0101'><div align='center'>总额</div></td>
			<td width='170'  class='A0101'><div align='center'>包装</div></td>
			<td width='40' class='A0101' ><div align='center'>出货方式</div></td>
			<td width='57' class='A0101'><div align='center'>出货时间</div></td>
			<td width='66' class='A0101'><div align='center'>订单日期</div></td>
		  </tr>";
		  }
		 $i++; 
		}while ($myrow = mysql_fetch_array($result));
	}
?>  
  <tr>
    <td height="21" colspan="4" class="A0111">合计：</td>
    <td class="A0101" align='right'><?php    echo $SUMQTY?></td>
    <td class="A0101" align='right'><?php    echo $SUMAMOUNT?></td>
    <td colspan="4" class="A0101">&nbsp;</td>
  </tr>
</table>
</body>
</html>
