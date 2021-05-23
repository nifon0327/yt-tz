<?php   
//电信-EWEN
include "../model/modelhead.php";
//读取配件库存值
//读取配件库存值
$ClientSTR=$SearchRows==""?"and M.CompanyId='$CompanyId'":"";
$OrderBY="order by M.CompanyId,M.OrderDate ASC,M.Id DESC";
$TypeIdSTR="";
if($TypeId!=""){
	$TypeIdSTR=" AND P.TypeId='$TypeId'";
	}
//读取客户简称
$checkClient=mysql_fetch_array(mysql_query("SELECT Forshort FROM $DataIn.trade_object WHERE CompanyId='$CompanyId' LIMIT 1",$link_id));
$Forshort=$checkClient["Forshort"];
$result = mysql_query("SELECT S.OrderPO,M.OrderDate,S.POrderId,S.ShipType,S.DeliveryDate,S.Qty,S.Price,S.PackRemark,S.sgRemark,P.cName,P.ECode,P.Unit 
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
    <td height="16" colspan="13" scope="col"><div align="center" class="style1">未出明细列表</div></td>
  </tr>
  <tr>
    <td height="21" colspan="3" class="A0100">PAGE-1</td>
    <td height="21" colspan="4" class="A0100" align="center">客户:<?php    echo $Forshort?></td>
    <td class="A0100" colspan="2" align="right">列印日期:<?php    echo date("Y年m月d日")?></td>
  </tr>
  <tr>
    <td height="8" width="26"  class="A0111">&nbsp;</td>    
    <td width="60" class="A0101" align="center">订单流水号</td>
    <td width="60" class="A0101">PO</td>
    <td width="228"  class="A0101">中文名</td>
	<td width="35"  class="A0101">订单数</td>
	<td width="44" class="A0101" align="center">出货方式</td>
    <td width="130" class="A0101" align="center">生管备注</td>
    <td width="120" class="A0101" align="center">包装说明</td>
	 <td width="30" class="A0101" align="center">期限</td>
  </tr>
  <?php   
  if($myrow = mysql_fetch_array($result)){
	$i=1;
	$Page=1;
	$SUMQTY=0;
	$SUMAMOUNT=0;
	do{
		  $OrderPO=$myrow["OrderPO"]==""?"&nbsp;":$myrow["OrderPO"];
		  $ShipType=$myrow["ShipType"];
		  $POrderId=$myrow["POrderId"];
		  $cName=$myrow["cName"];
		  $Price=$myrow["Price"]; 
		  $Qty=$myrow["Qty"]; 
		  $SUMQTY=$SUMQTY+$Qty;
		  $sgRemark=$myrow["sgRemark"]==""?"&nbsp":$myrow["sgRemark"]; 
		  $PackRemark=$myrow["PackRemark"]==""?"&nbsp":$myrow["PackRemark"]; 
		  
		  $Amount=sprintf("%.2f",$Price*$Qty);
		  $SUMAMOUNT=sprintf("%.2f",$SUMAMOUNT+$Amount);
		  $OrderDate=$myrow["OrderDate"];
		  $OrderDate=CountDays($OrderDate,0);
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
		  echo"<td  height='10' class='A0111' align='center'>$ColSign$i</td>";		
		  echo"<td class='A0101'>$POrderId</td>";
		  echo"<td class='A0101'>$OrderPO</td>";
		  echo"<td class='A0101'>$cName</td>";
		  echo"<td class='A0101' align='right'>$Qty</td>";
		  echo"<td class='A0101' align='center'>$ShipType</td>";
		  echo"<td class='A0101'>$sgRemark</td>";
		  echo"<td class='A0101'>$PackRemark</td>";
		  echo"<td class='A0101' align='center'>$OrderDate</td>";
		  echo"</tr>";
		 $i++; 
		}while ($myrow = mysql_fetch_array($result));
	}
?>  
</table>
</body>
</html>
